<?php

namespace App\Console\Commands;

use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectarInactivos extends Command
{
    protected $signature = 'paicat:detectar-inactivos
        {--dias= : Días de inactividad (usa config si no se especifica)}
        {--marcar : Marcar automáticamente como inactivos}';

    protected $description = 'Detectar estudiantes inactivos tras plazo configurado';

    public function handle()
    {
        $diasLimite = $this->option('dias') ?? config('paicat.dias_inactividad', 30);
        $marcar = $this->option('marcar');
        $fechaLimite = now()->subDays($diasLimite);

        $this->info("Buscando estudiantes sin actividad en los últimos {$diasLimite} días...");

        // Obtener inscripciones que están cursando y tienen comisión activa
        $inscripcionesCursando = Inscripcion::where('estado_ingreso', Inscripcion::INGRESO_CURSANDO)
            ->whereHas('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado']);
            })
            ->with(['inscripcionesComision' => function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado'])->with('comision');
            }])
            ->get();

        $inactivos = collect();

        foreach ($inscripcionesCursando as $inscripcion) {
            $inscripcionComision = $inscripcion->inscripcionesComision->first();
            if (!$inscripcionComision) {
                continue;
            }

            // Buscar última actividad: asistencia o nota
            $ultimaAsistencia = DB::table('asistencias')
                ->where('inscripcion_id', $inscripcion->id)
                ->max('fecha');

            $ultimaNota = DB::table('notas')
                ->where('inscripcion_id', $inscripcion->id)
                ->max('updated_at');

            // La última actividad es la más reciente entre asistencia y nota
            $ultimaActividad = collect([$ultimaAsistencia, $ultimaNota])
                ->filter()
                ->max();

            // Si no tiene ninguna actividad, usar la fecha de inscripción a comisión
            if (!$ultimaActividad) {
                $ultimaActividad = $inscripcionComision->fecha_inscripcion
                    ?? $inscripcionComision->created_at;
            }

            if ($ultimaActividad && $ultimaActividad < $fechaLimite) {
                $diasSinActividad = now()->diffInDays($ultimaActividad);
                $person = $inscripcion->getPerson();

                $inactivos->push([
                    'inscripcion' => $inscripcion,
                    'persona' => $person,
                    'comision' => $inscripcionComision->comision?->nombre ?? 'Sin comisión',
                    'ultima_actividad' => $ultimaActividad,
                    'dias_sin_actividad' => $diasSinActividad,
                ]);
            }
        }

        if ($inactivos->isEmpty()) {
            $this->info('No se encontraron estudiantes inactivos.');
            return 0;
        }

        $this->warn("Se encontraron {$inactivos->count()} estudiantes inactivos:");
        $this->newLine();

        $headers = ['ID', 'Alumno', 'Comisión', 'Última actividad', 'Días sin actividad'];
        $rows = $inactivos->map(function ($item) {
            return [
                $item['inscripcion']->id,
                $item['persona'] ? "{$item['persona']->apellido}, {$item['persona']->nombre}" : 'Sin datos',
                $item['comision'],
                $item['ultima_actividad'],
                $item['dias_sin_actividad'],
            ];
        })->toArray();

        $this->table($headers, $rows);

        if ($marcar) {
            foreach ($inactivos as $item) {
                $item['inscripcion']->update(['estado_ingreso' => Inscripcion::INGRESO_LIBRE]);

                \App\Models\Trayectoria::registrarEvento(
                    $item['inscripcion']->id,
                    \App\Models\Trayectoria::ESTADO_LIBRE,
                    "Inactividad detectada: {$item['dias_sin_actividad']} días sin actividad",
                    false,
                    null
                );
            }
            $this->info("Se marcaron {$inactivos->count()} estudiantes como 'libre' por inactividad.");
        }

        return 0;
    }
}
