<?php

namespace App\Services;

use App\Models\Comision;
use App\Models\Cursada;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use App\Models\Materia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para asignación aleatoria de estudiantes a comisiones (RF10)
 *
 * Características:
 * - Distribución equitativa entre comisiones del mismo turno/modalidad
 * - Opción de asignar por carrera (especialidad)
 * - Respeta preferencias de turno y modalidad del alumno
 */
class AsignacionComisionService
{
    /**
     * Asignar estudiantes aleatoriamente a comisiones de una materia
     *
     * @param Materia $materia La materia
     * @param int $anio Año académico
     * @param array $opciones Opciones de asignación:
     *   - 'por_carrera' => bool: Asignar primero por carrera/especialidad
     *   - 'respetar_turno' => bool: Respetar turno preferido del alumno
     *   - 'respetar_modalidad' => bool: Respetar modalidad preferida del alumno
     *   - 'distribucion_equitativa' => bool: Distribuir equitativamente entre comisiones similares
     *   - 'especialidad_id' => int: Filtrar solo alumnos de esta especialidad
     * @return array Resultado de la asignación
     */
    public function asignarAleatorio(Materia $materia, int $anio, array $opciones = []): array
    {
        // Opciones por defecto
        $opciones = array_merge([
            'por_carrera' => false,
            'respetar_turno' => true,
            'respetar_modalidad' => true,
            'distribucion_equitativa' => true,
            'especialidad_id' => null,
        ], $opciones);

        $resultado = [
            'exitosas' => 0,
            'fallidas' => 0,
            'sin_cupo' => 0,
            'sin_comision_compatible' => 0,
            'detalle' => [],
            'por_comision' => [],
        ];

        // Obtener comisiones activas de la materia con cupos disponibles
        $comisiones = $this->obtenerComisionesDisponibles($materia, $anio);

        if ($comisiones->isEmpty()) {
            $resultado['error'] = 'No hay comisiones disponibles con cupos para esta materia.';
            return $resultado;
        }

        // Obtener inscripciones sin comisión asignada para esta materia
        $inscripcionesSinAsignar = $this->obtenerInscripcionesSinAsignar($materia, $anio, $opciones);

        if ($inscripcionesSinAsignar->isEmpty()) {
            $resultado['mensaje'] = 'No hay inscripciones pendientes de asignación.';
            return $resultado;
        }

        // Si es por carrera, agrupar por especialidad
        if ($opciones['por_carrera']) {
            $inscripcionesSinAsignar = $inscripcionesSinAsignar->sortBy('especialidad_id_sysacad');
        }

        // Mezclar aleatoriamente (manteniendo grupos si es por carrera)
        if (!$opciones['por_carrera']) {
            $inscripcionesSinAsignar = $inscripcionesSinAsignar->shuffle();
        } else {
            // Shuffle dentro de cada grupo de carrera
            $inscripcionesSinAsignar = $inscripcionesSinAsignar
                ->groupBy('especialidad_id_sysacad')
                ->map(fn($grupo) => $grupo->shuffle())
                ->flatten(1);
        }

        // Inicializar contadores por comisión para distribución equitativa
        $asignacionesPorComision = $comisiones->mapWithKeys(fn($c) => [$c->id => 0]);

        DB::beginTransaction();
        try {
            foreach ($inscripcionesSinAsignar as $inscripcion) {
                // Buscar comisión con cupo que cumpla restricciones
                $comisionAsignada = $this->buscarComisionDisponible(
                    $comisiones,
                    $inscripcion,
                    $opciones,
                    $asignacionesPorComision
                );

                if ($comisionAsignada) {
                    // Crear inscripción a comisión
                    InscripcionComision::create([
                        'inscripcion_id' => $inscripcion->id,
                        'academico_dato_id' => $inscripcion->academico_dato_id,
                        'comision_id' => $comisionAsignada->id,
                        'fecha_inscripcion' => now(),
                        'estado' => 'inscripto',
                    ]);

                    // Crear registro de cursada
                    $anioActual = date('Y');
                    $esRecursante = Cursada::where('inscripcion_id', $inscripcion->id)
                        ->where('anio', '<', $anioActual)
                        ->whereIn('estado', [Cursada::ESTADO_DESAPROBADO, Cursada::ESTADO_LIBRE])
                        ->exists();

                    Cursada::firstOrCreate(
                        [
                            'inscripcion_id' => $inscripcion->id,
                            'comision_id' => $comisionAsignada->id,
                            'anio' => $anioActual,
                        ],
                        [
                            'estado' => Cursada::ESTADO_CURSANDO,
                            'modalidad' => $comisionAsignada->modalidad,
                            'es_recursante' => $esRecursante,
                            'fecha_inicio' => now(),
                        ]
                    );

                    // Actualizar estado de la inscripción a 'cursando'
                    $inscripcion->update(['estado_ingreso' => Inscripcion::INGRESO_CURSANDO]);

                    // Actualizar cupo
                    $comisionAsignada->increment('cupo_actual');
                    $asignacionesPorComision[$comisionAsignada->id]++;

                    $resultado['exitosas']++;
                    $resultado['detalle'][] = [
                        'inscripcion_id' => $inscripcion->id,
                        'comision_id' => $comisionAsignada->id,
                        'comision_nombre' => $comisionAsignada->nombre,
                        'turno' => $comisionAsignada->turno,
                        'modalidad' => $comisionAsignada->modalidad,
                    ];

                    // Si la comisión se llenó, quitarla de las disponibles
                    if (!$comisionAsignada->tieneCuposDisponibles()) {
                        $comisiones = $comisiones->reject(fn($c) => $c->id === $comisionAsignada->id);
                    }
                } else {
                    $resultado['sin_comision_compatible']++;
                    $resultado['fallidas']++;
                }
            }

            // Generar resumen por comisión
            foreach ($asignacionesPorComision as $comisionId => $cantidad) {
                if ($cantidad > 0) {
                    $comision = $comisiones->firstWhere('id', $comisionId)
                        ?? Comision::find($comisionId);
                    if ($comision) {
                        $resultado['por_comision'][] = [
                            'comision' => $comision->nombre,
                            'turno' => $comision->turno,
                            'modalidad' => $comision->modalidad,
                            'asignados' => $cantidad,
                            'cupo_actual' => $comision->cupo_actual,
                            'cupo_maximo' => $comision->cupo_maximo,
                        ];
                    }
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
     * Obtener comisiones disponibles para una materia
     */
    private function obtenerComisionesDisponibles(Materia $materia, int $anio): Collection
    {
        return Comision::whereHas('materias', function ($q) use ($materia) {
            $q->where('materias.id', $materia->id);
        })
            ->where('anio', $anio)
            ->where('estado', 'activa')
            ->where(function ($q) {
                // Con cupo disponible O sin límite (virtual)
                $q->whereRaw('cupo_actual < cupo_maximo')
                    ->orWhereNull('cupo_maximo');
            })
            ->orderBy('cupo_actual') // Priorizar las que tienen menos inscriptos
            ->get();
    }

    /**
     * Obtener inscripciones sin comisión asignada para una materia
     */
    private function obtenerInscripcionesSinAsignar(Materia $materia, int $anio, array $opciones): Collection
    {
        $query = Inscripcion::where('anio_ingreso', $anio)
            ->whereIn('estado', [
                Inscripcion::ESTADO_PENDIENTE,
                Inscripcion::ESTADO_DOCUMENTACION_OK,
                Inscripcion::ESTADO_CONFIRMADO,
            ]);

        // Filtrar por especialidad si se especificó
        if (!empty($opciones['especialidad_id'])) {
            $query->where('especialidad_id_sysacad', $opciones['especialidad_id']);
        }

        // Obtener IDs de comisiones de esta materia
        $comisionesMateria = Comision::whereHas('materias', function ($q) use ($materia) {
            $q->where('materias.id', $materia->id);
        })->pluck('id');

        // Excluir las que ya tienen comisión asignada para esta materia
        $query->whereDoesntHave('inscripcionesComision', function ($q) use ($comisionesMateria) {
            $q->whereIn('comision_id', $comisionesMateria)
                ->whereIn('estado', ['inscripto', 'confirmado']);
        });

        return $query->get();
    }

    /**
     * Buscar comisión disponible que cumpla restricciones
     * Con distribución equitativa entre comisiones del mismo turno/modalidad
     */
    private function buscarComisionDisponible(
        Collection $comisiones,
        Inscripcion $inscripcion,
        array $opciones,
        Collection $asignacionesPorComision
    ): ?Comision {
        // Filtrar comisiones compatibles
        $compatibles = $comisiones->filter(function ($comision) use ($inscripcion, $opciones) {
            // Verificar cupo
            if (!$comision->tieneCuposDisponibles()) {
                return false;
            }

            // Verificar modalidad si se debe respetar
            if ($opciones['respetar_modalidad'] && $inscripcion->modalidad) {
                if (strtolower($comision->modalidad) !== strtolower($inscripcion->modalidad)) {
                    return false;
                }
            }

            // Verificar turno si se debe respetar
            if ($opciones['respetar_turno'] && $inscripcion->turno_ingreso) {
                if (strtolower($comision->turno ?? '') !== strtolower($inscripcion->turno_ingreso)) {
                    return false;
                }
            }

            return true;
        });

        if ($compatibles->isEmpty()) {
            // Si no hay compatibles con restricciones, intentar sin restricciones de turno
            if ($opciones['respetar_turno']) {
                $compatibles = $comisiones->filter(function ($comision) use ($inscripcion, $opciones) {
                    if (!$comision->tieneCuposDisponibles()) {
                        return false;
                    }
                    if ($opciones['respetar_modalidad'] && $inscripcion->modalidad) {
                        if (strtolower($comision->modalidad) !== strtolower($inscripcion->modalidad)) {
                            return false;
                        }
                    }
                    return true;
                });
            }
        }

        if ($compatibles->isEmpty()) {
            return null;
        }

        // Si hay distribución equitativa, elegir la comisión con menos asignaciones en esta ronda
        if ($opciones['distribucion_equitativa']) {
            return $compatibles->sortBy(function ($comision) use ($asignacionesPorComision) {
                // Ordenar por: asignaciones en esta ronda + cupo actual
                $asignacionesRonda = $asignacionesPorComision[$comision->id] ?? 0;
                return $asignacionesRonda + $comision->cupo_actual;
            })->first();
        }

        // Si no, elegir aleatoriamente
        return $compatibles->random();
    }

    /**
     * Asignar por carrera (especialidad) - ejecuta asignación para cada carrera
     */
    public function asignarPorCarrera(Materia $materia, int $anio, array $opciones = []): array
    {
        $opciones['por_carrera'] = true;

        // Obtener especialidades únicas de las inscripciones pendientes
        $especialidades = Inscripcion::where('anio_ingreso', $anio)
            ->whereIn('estado', [
                Inscripcion::ESTADO_PENDIENTE,
                Inscripcion::ESTADO_DOCUMENTACION_OK,
                Inscripcion::ESTADO_CONFIRMADO,
            ])
            ->distinct()
            ->pluck('especialidad_id_sysacad')
            ->filter();

        $resultadoTotal = [
            'exitosas' => 0,
            'fallidas' => 0,
            'por_carrera' => [],
        ];

        foreach ($especialidades as $especialidadId) {
            $opciones['especialidad_id'] = $especialidadId;
            $resultado = $this->asignarAleatorio($materia, $anio, $opciones);

            $resultadoTotal['exitosas'] += $resultado['exitosas'];
            $resultadoTotal['fallidas'] += $resultado['fallidas'];
            $resultadoTotal['por_carrera'][$especialidadId] = [
                'exitosas' => $resultado['exitosas'],
                'fallidas' => $resultado['fallidas'],
            ];
        }

        return $resultadoTotal;
    }

    /**
     * Generar listado de cambios para CVG
     */
    public function generarListadoCVG(int $anio): array
    {
        $cambios = [];

        $inscripcionesComision = InscripcionComision::with(['inscripcion', 'comision.materias'])
            ->whereHas('comision', fn($q) => $q->where('anio', $anio))
            ->where('estado', 'inscripto')
            ->get();

        foreach ($inscripcionesComision as $ic) {
            $person = $ic->inscripcion->getPerson();
            if ($person) {
                $materias = $ic->comision->materias->pluck('nombre')->join(', ');
                $cambios[] = [
                    'dni' => $person->documento,
                    'nombre' => $person->nombre . ' ' . $person->apellido,
                    'materia' => $materias ?: 'N/A',
                    'comision' => $ic->comision->codigo,
                    'turno' => $ic->comision->turno,
                    'modalidad' => $ic->comision->modalidad,
                ];
            }
        }

        return $cambios;
    }

    /**
     * Obtener estadísticas de distribución actual
     */
    public function obtenerEstadisticasDistribucion(Materia $materia, int $anio): array
    {
        $comisiones = Comision::whereHas('materias', function ($q) use ($materia) {
            $q->where('materias.id', $materia->id);
        })
            ->where('anio', $anio)
            ->where('estado', 'activa')
            ->withCount(['inscripciones as alumnos_inscriptos'])
            ->get();

        $stats = [];
        foreach ($comisiones as $comision) {
            $stats[] = [
                'comision' => $comision->nombre,
                'codigo' => $comision->codigo,
                'turno' => $comision->turno,
                'modalidad' => $comision->modalidad,
                'cupo_actual' => $comision->cupo_actual,
                'cupo_maximo' => $comision->cupo_maximo,
                'disponibles' => $comision->cupos_disponibles,
                'porcentaje' => $comision->porcentaje_ocupacion,
            ];
        }

        return $stats;
    }
}
