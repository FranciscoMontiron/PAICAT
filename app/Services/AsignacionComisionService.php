<?php

namespace App\Services;

use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use App\Models\Materia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para asignación aleatoria de estudiantes a comisiones (RF10)
 */
class AsignacionComisionService
{
    /**
     * Asignar estudiantes aleatoriamente a comisiones de una materia
     * 
     * @param Materia $materia La materia
     * @param int $anio Año académico
     * @param array $restricciones Restricciones adicionales (ej: por carrera)
     * @return array Resultado de la asignación
     */
    public function asignarAleatorio(Materia $materia, int $anio, array $restricciones = []): array
    {
        $resultado = [
            'exitosas' => 0,
            'fallidas' => 0,
            'sin_cupo' => 0,
            'detalle' => [],
        ];

        // Obtener comisiones activas de la materia con cupos disponibles
        $comisiones = Comision::where('materia_id', $materia->id)
            ->where('anio', $anio)
            ->where('estado', 'activa')
            ->whereRaw('cupo_actual < cupo_maximo')
            ->orderBy('cupo_actual') // Priorizar las que tienen menos inscriptos
            ->get();

        if ($comisiones->isEmpty()) {
            $resultado['error'] = 'No hay comisiones disponibles con cupos.';
            return $resultado;
        }

        // Obtener inscripciones sin comisión asignada para esta materia
        $inscripcionesSinAsignar = $this->obtenerInscripcionesSinAsignar($materia, $anio, $restricciones);

        if ($inscripcionesSinAsignar->isEmpty()) {
            $resultado['mensaje'] = 'No hay inscripciones pendientes de asignación.';
            return $resultado;
        }

        // Mezclar aleatoriamente las inscripciones
        $inscripcionesSinAsignar = $inscripcionesSinAsignar->shuffle();

        DB::beginTransaction();
        try {
            foreach ($inscripcionesSinAsignar as $inscripcion) {
                // Buscar comisión con cupo que cumpla restricciones
                $comisionAsignada = $this->buscarComisionDisponible($comisiones, $inscripcion, $restricciones);

                if ($comisionAsignada) {
                    // Crear inscripción a comisión
                    InscripcionComision::create([
                        'inscripcion_id' => $inscripcion->id,
                        'academico_dato_id' => $inscripcion->academico_dato_id,
                        'comision_id' => $comisionAsignada->id,
                        'fecha_inscripcion' => now(),
                        'estado' => 'inscripto',
                    ]);

                    // Actualizar cupo
                    $comisionAsignada->increment('cupo_actual');

                    $resultado['exitosas']++;
                    $resultado['detalle'][] = [
                        'inscripcion_id' => $inscripcion->id,
                        'comision_id' => $comisionAsignada->id,
                        'comision_nombre' => $comisionAsignada->nombre,
                    ];

                    // Refrescar comisiones para verificar cupos
                    if ($comisionAsignada->cupo_actual >= $comisionAsignada->cupo_maximo) {
                        $comisiones = $comisiones->reject(fn($c) => $c->id === $comisionAsignada->id);
                    }
                } else {
                    $resultado['sin_cupo']++;
                    $resultado['fallidas']++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $resultado['error'] = 'Error durante la asignación: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Obtener inscripciones sin comisión asignada para una materia
     */
    private function obtenerInscripcionesSinAsignar(Materia $materia, int $anio, array $restricciones): Collection
    {
        $query = Inscripcion::where('anio_ingreso', $anio)
            ->whereIn('estado', [
                Inscripcion::ESTADO_DOCUMENTACION_OK,
                Inscripcion::ESTADO_CONFIRMADO,
            ]);

        // Filtrar por especialidad si la materia es específica
        if ($materia->especialidad_id_sysacad) {
            $query->where('especialidad_id_sysacad', $materia->especialidad_id_sysacad);
        }

        // Aplicar restricciones adicionales
        if (!empty($restricciones['especialidad_id'])) {
            $query->where('especialidad_id_sysacad', $restricciones['especialidad_id']);
        }

        if (!empty($restricciones['modalidad'])) {
            $query->where('modalidad', $restricciones['modalidad']);
        }

        // Excluir las que ya tienen comisión asignada para esta materia
        $comisionesMateria = Comision::where('materia_id', $materia->id)->pluck('id');

        $query->whereDoesntHave('inscripcionesComision', function ($q) use ($comisionesMateria) {
            $q->whereIn('comision_id', $comisionesMateria)
                ->where('estado', 'inscripto');
        });

        return $query->get();
    }

    /**
     * Buscar comisión disponible que cumpla restricciones
     */
    private function buscarComisionDisponible(Collection $comisiones, Inscripcion $inscripcion, array $restricciones): ?Comision
    {
        foreach ($comisiones as $comision) {
            // Verificar cupo
            if ($comision->cupo_actual >= $comision->cupo_maximo) {
                continue;
            }

            // Verificar restricciones de modalidad
            if (!empty($restricciones['respetar_modalidad'])) {
                if ($comision->modalidad !== $inscripcion->modalidad) {
                    continue;
                }
            }

            // Verificar restricciones de turno
            if (!empty($restricciones['respetar_turno'])) {
                if ($comision->turno !== $inscripcion->turno_ingreso) {
                    continue;
                }
            }

            return $comision;
        }

        return null;
    }

    /**
     * Generar listado de cambios para CVG
     */
    public function generarListadoCVG(int $anio): array
    {
        $cambios = [];

        $inscripcionesComision = InscripcionComision::with(['inscripcion', 'comision.materia'])
            ->whereHas('comision', fn($q) => $q->where('anio', $anio))
            ->where('estado', 'inscripto')
            ->get();

        foreach ($inscripcionesComision as $ic) {
            $person = $ic->inscripcion->getPerson();
            if ($person) {
                $cambios[] = [
                    'dni' => $person->documento,
                    'nombre' => $person->nombre . ' ' . $person->apellido,
                    'materia' => $ic->comision->materia?->nombre ?? 'N/A',
                    'comision' => $ic->comision->codigo,
                    'turno' => $ic->comision->turno,
                    'modalidad' => $ic->comision->modalidad,
                ];
            }
        }

        return $cambios;
    }
}
