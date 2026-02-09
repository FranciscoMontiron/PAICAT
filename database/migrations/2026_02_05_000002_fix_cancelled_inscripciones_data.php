<?php

use App\Models\Inscripcion;
use App\Models\Trayectoria;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corregir inscripciones canceladas existentes:
     * - Actualizar estado_ingreso a 'cancelado'
     * - Cancelar inscripciones a comisión activas
     * - Registrar en trayectoria si no existe
     */
    public function up(): void
    {
        $inscripcionesCanceladas = Inscripcion::where('estado', 'cancelado')
            ->where('estado_ingreso', '!=', 'cancelado')
            ->get();

        foreach ($inscripcionesCanceladas as $inscripcion) {
            DB::transaction(function () use ($inscripcion) {
                // 1. Actualizar estado_ingreso
                $inscripcion->update(['estado_ingreso' => 'cancelado']);

                // 2. Cancelar inscripciones a comisión activas
                $inscripcionesComision = $inscripcion->inscripcionesComision()
                    ->whereIn('estado', ['inscripto', 'confirmado'])
                    ->with('comision')
                    ->get();

                foreach ($inscripcionesComision as $inscripcionComision) {
                    $inscripcionComision->update([
                        'estado' => 'cancelado',
                        'observaciones' => 'Cancelado automáticamente - Corrección de datos',
                    ]);

                    // Decrementar cupo si la comisión existe
                    if ($inscripcionComision->comision && $inscripcionComision->comision->cupo_actual > 0) {
                        $inscripcionComision->comision->decrement('cupo_actual');
                    }
                }

                // 3. Registrar en trayectoria si no existe una cancelación
                $tieneTrayectoriaCancelada = Trayectoria::where('inscripcion_id', $inscripcion->id)
                    ->where('estado', 'cancelado')
                    ->exists();

                if (!$tieneTrayectoriaCancelada) {
                    // Cerrar trayectorias vigentes
                    Trayectoria::where('inscripcion_id', $inscripcion->id)
                        ->whereNull('fecha_fin')
                        ->update(['fecha_fin' => now()]);

                    // Crear trayectoria de cancelación
                    Trayectoria::create([
                        'inscripcion_id' => $inscripcion->id,
                        'estado' => 'cancelado',
                        'fecha_inicio' => $inscripcion->updated_at ?? now(),
                        'motivo' => $inscripcion->observaciones ?? 'Cancelación de inscripción',
                        'es_voluntaria' => true,
                        'registrado_por' => null,
                    ]);
                }
            });
        }
    }

    /**
     * No se puede revertir esta migración de corrección de datos.
     */
    public function down(): void
    {
        // No se revierte - son correcciones de datos históricos
    }
};
