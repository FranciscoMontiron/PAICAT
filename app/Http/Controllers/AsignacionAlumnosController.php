<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Cursada;
use App\Models\Inscripcion;
use App\Models\InscripcionComision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionAlumnosController extends Controller
{
    /**
     * Mostrar vista principal de asignación aleatoria
     */
    public function index(Request $request)
    {
        // Obtener comisiones activas con cupos disponibles
        $comisiones = Comision::with(['materias', 'docente'])
            ->activas()
            ->conCuposDisponibles()
            ->when($request->anio, fn($q, $anio) => $q->anio($anio))
            ->when($request->periodo, fn($q, $periodo) => $q->where('periodo', $periodo))
            ->orderBy('codigo')
            ->get();

        // Estadísticas generales (solo comisiones con cupo definido)
        $totalCuposDisponibles = $comisiones->sum(fn($c) => $c->cupos_disponibles ?? 0);

        // Alumnos sin asignar a ninguna comisión
        $alumnosSinAsignarCollection = $this->obtenerAlumnosSinAsignar();
        $alumnosSinAsignar = $alumnosSinAsignarCollection->count();

        // Obtener especialidades/carreras disponibles (de sysacad)
        $especialidades = $this->obtenerEspecialidades();

        // Estadísticas por carrera de alumnos sin asignar
        $alumnosPorCarrera = $alumnosSinAsignarCollection->groupBy('especialidad_id_sysacad')
            ->map(function ($group, $espId) use ($especialidades) {
                return [
                    'cantidad' => $group->count(),
                    'nombre' => $especialidades[$espId] ?? 'Sin carrera asignada',
                ];
            });

        return view('asignacion-alumnos.index', [
            'comisiones' => $comisiones,
            'totalCuposDisponibles' => $totalCuposDisponibles,
            'alumnosSinAsignar' => $alumnosSinAsignar,
            'alumnosPorCarrera' => $alumnosPorCarrera,
            'especialidades' => $especialidades,
            'anioActual' => $request->anio ?? date('Y'),
        ]);
    }

    /**
     * Vista previa de la asignación (simulación)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'comisiones' => 'required|array|min:1',
            'comisiones.*' => 'exists:comisiones,id',
            'agrupar_por_carrera' => 'nullable|boolean',
            'filtrar_especialidad' => 'nullable|integer',
        ]);

        $comisionesIds = $request->comisiones;
        $agruparPorCarrera = $request->boolean('agrupar_por_carrera', false);
        $filtrarEspecialidad = $request->filtrar_especialidad;

        // Obtener comisiones seleccionadas
        $comisiones = Comision::whereIn('id', $comisionesIds)
            ->activas()
            ->conCuposDisponibles()
            ->with('materias')
            ->get();

        if ($comisiones->isEmpty()) {
            return back()->with('error', 'Las comisiones seleccionadas no tienen cupos disponibles.');
        }

        // Simular asignación
        $simulacion = $this->simularAsignacion($comisiones, $agruparPorCarrera, $filtrarEspecialidad);

        return view('asignacion-alumnos.preview', [
            'comisiones' => $comisiones,
            'simulacion' => $simulacion,
            'comisionesIds' => $comisionesIds,
            'agruparPorCarrera' => $agruparPorCarrera,
            'filtrarEspecialidad' => $filtrarEspecialidad,
            'especialidades' => $this->obtenerEspecialidades(),
        ]);
    }

    /**
     * Ejecutar la asignación aleatoria
     */
    public function ejecutar(Request $request)
    {
        $request->validate([
            'comisiones' => 'required|array|min:1',
            'comisiones.*' => 'exists:comisiones,id',
            'excluir' => 'nullable|array',
            'excluir.*' => 'exists:inscripciones,id',
            'agrupar_por_carrera' => 'nullable|boolean',
            'filtrar_especialidad' => 'nullable|integer',
        ]);

        $comisionesIds = $request->comisiones;
        $excluirIds = $request->excluir ?? [];
        $agruparPorCarrera = $request->boolean('agrupar_por_carrera', false);
        $filtrarEspecialidad = $request->filtrar_especialidad;

        // Obtener comisiones seleccionadas
        $comisiones = Comision::whereIn('id', $comisionesIds)
            ->activas()
            ->conCuposDisponibles()
            ->with('materias')
            ->get();

        if ($comisiones->isEmpty()) {
            return redirect()->route('asignacion-alumnos.index')
                ->with('error', 'Las comisiones seleccionadas no tienen cupos disponibles.');
        }

        try {
            $resultado = DB::transaction(function () use ($comisiones, $excluirIds, $agruparPorCarrera, $filtrarEspecialidad) {
                return $this->ejecutarAsignacion($comisiones, $excluirIds, $agruparPorCarrera, $filtrarEspecialidad);
            });

            $mensaje = "Se asignaron {$resultado['total']} alumnos a {$resultado['comisiones']} comisiones.";
            if ($agruparPorCarrera) {
                $mensaje .= " (Agrupados por carrera)";
            }

            return redirect()->route('asignacion-alumnos.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()->route('asignacion-alumnos.index')
                ->with('error', 'Error al ejecutar la asignación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener inscripciones sin asignar a NINGUNA comisión activa.
     * Un alumno ya asignado a cualquier comisión NO aparece como disponible.
     */
    private function obtenerAlumnosSinAsignar(?int $filtrarEspecialidad = null)
    {
        return Inscripcion::activas()
            ->whereDoesntHave('inscripcionesComision', function ($q) {
                $q->whereIn('estado', ['inscripto', 'confirmado', 'aprobado']);
            })
            ->when($filtrarEspecialidad, fn($q) => $q->where('especialidad_id_sysacad', $filtrarEspecialidad))
            ->get();
    }

    /**
     * Filtrar alumnos elegibles por requisitos de la comisión.
     * Reglas: Modalidad, Tipo Ingreso (periodo), Turno, y Año deben coincidir.
     */
    private function filtrarPorRequisitos($inscripciones, Comision $comision)
    {
        return $inscripciones->filter(function ($inscripcion) use ($comision) {
            // Regla 1: El año de ingreso debe coincidir con el año de la comisión
            if ($inscripcion->anio_ingreso && $comision->anio) {
                if ((int) $inscripcion->anio_ingreso !== (int) $comision->anio) {
                    return false;
                }
            }

            // Regla 2: La modalidad debe coincidir (Presencial/Virtual/Semipresencial)
            if ($inscripcion->modalidad && $comision->modalidad) {
                if (strtolower($inscripcion->modalidad) !== strtolower($comision->modalidad)) {
                    return false;
                }
            }

            // Regla 3: El tipo de ingreso debe coincidir (Intensivo/Extensivo)
            if ($inscripcion->tipo_ingreso && $comision->periodo) {
                if (strtolower($inscripcion->tipo_ingreso) !== strtolower($comision->periodo)) {
                    return false;
                }
            }

            // Regla 4: El turno debe coincidir (Mañana/TardeNoche) - solo si la comisión tiene turno
            if ($inscripcion->turno_ingreso && $comision->turno) {
                if (strtolower($inscripcion->turno_ingreso) !== strtolower($comision->turno)) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Obtener cupos disponibles reales de una comisión (considera extracupos).
     * Para virtuales (sin limite) se usa un tope alto para el algoritmo.
     */
    private function getCuposParaAsignacion(Comision $comision): int
    {
        $cupoTotal = $comision->cupo_total;

        if (is_null($cupoTotal)) {
            return 9999; // Virtual: sin limite práctico
        }

        return max(0, $cupoTotal - $comision->cupo_real);
    }

    /**
     * Obtener especialidades/carreras desde sysacad
     */
    private function obtenerEspecialidades(): array
    {
        try {
            return DB::connection('sysacad')
                ->table('sysacad_especialidades')
                ->orderBy('nombre')
                ->pluck('nombre', 'id_sysacad')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Simular asignación sin ejecutar cambios.
     */
    private function simularAsignacion($comisiones, bool $agruparPorCarrera = false, ?int $filtrarEspecialidad = null)
    {
        $simulacion = [];
        $asignadosGlobal = collect();

        // Obtener todos los alumnos sin asignar
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar($filtrarEspecialidad);

        // Preparar pool por comisión
        $poolPorComision = [];
        foreach ($comisiones as $comision) {
            $elegibles = $this->filtrarPorRequisitos($alumnosSinAsignar, $comision);
            $poolPorComision[$comision->id] = [
                'comision' => $comision,
                'elegibles' => $elegibles,
                'cuposDisponibles' => $this->getCuposParaAsignacion($comision),
                'asignados' => collect(),
            ];
        }

        if ($agruparPorCarrera) {
            $this->distribuirAgrupandoPorCarrera($poolPorComision, $asignadosGlobal);
        } else {
            $this->distribuirRoundRobinSimple($poolPorComision, $asignadosGlobal);
        }

        // Preparar resultado de simulación
        foreach ($poolPorComision as $data) {
            $distribucionPorCarrera = $data['asignados']->groupBy('especialidad_id_sysacad')->map->count();

            $simulacion[] = [
                'comision' => $data['comision'],
                'asignados' => $data['asignados'],
                'cuposDisponibles' => $data['cuposDisponibles'],
                'cantidadAsignados' => $data['asignados']->count(),
                'distribucionPorCarrera' => $distribucionPorCarrera,
            ];
        }

        return $simulacion;
    }

    /**
     * Distribución round-robin simple (sin considerar carrera).
     * Reparte equitativamente entre comisiones.
     */
    private function distribuirRoundRobinSimple(array &$poolPorComision, &$asignadosGlobal): void
    {
        foreach ($poolPorComision as &$data) {
            $data['elegibles'] = $data['elegibles']->shuffle();
        }

        $hayMas = true;
        while ($hayMas) {
            $hayMas = false;
            foreach ($poolPorComision as &$data) {
                if ($data['asignados']->count() >= $data['cuposDisponibles']) {
                    continue;
                }

                foreach ($data['elegibles'] as $key => $inscripcion) {
                    if (!$asignadosGlobal->contains($inscripcion->id)) {
                        $data['asignados']->push($inscripcion);
                        $asignadosGlobal->push($inscripcion->id);
                        $data['elegibles']->forget($key);
                        $hayMas = true;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Distribución agrupando por carrera.
     *
     * Prioriza llenar cada comisión con la máxima cantidad posible de una misma carrera.
     * Algoritmo:
     *   1. Para cada comisión, determinar cuál carrera tiene más elegibles
     *   2. Asignar primero todos los de la carrera mayoritaria
     *   3. Luego completar con las demás carreras si quedan cupos
     */
    private function distribuirAgrupandoPorCarrera(array &$poolPorComision, &$asignadosGlobal): void
    {
        // Paso 1: Determinar carrera dominante para cada comisión
        // Ordenar comisiones por cupos disponibles (las más chicas primero para que no queden sin su carrera)
        $comisionesOrdenadas = collect($poolPorComision)->sortBy('cuposDisponibles')->keys()->toArray();

        // Rastrear qué carreras ya fueron "asignadas primariamente" a una comisión
        $carrerasAsignadas = [];

        foreach ($comisionesOrdenadas as $comisionId) {
            $data = &$poolPorComision[$comisionId];
            $cuposDisponibles = $data['cuposDisponibles'];

            // Agrupar elegibles no asignados globalmente por carrera
            $elegiblesPorCarrera = $data['elegibles']
                ->reject(fn($i) => $asignadosGlobal->contains($i->id))
                ->groupBy('especialidad_id_sysacad')
                ->sortByDesc(fn($group) => $group->count());

            if ($elegiblesPorCarrera->isEmpty()) {
                continue;
            }

            // Elegir la carrera con más elegibles que no haya sido primaria de otra comisión
            $carreraPrimaria = null;
            foreach ($elegiblesPorCarrera->keys() as $carreraId) {
                if (!in_array($carreraId, $carrerasAsignadas)) {
                    $carreraPrimaria = $carreraId;
                    break;
                }
            }

            // Si todas las carreras ya fueron asignadas, tomar la que tenga más elegibles
            if (is_null($carreraPrimaria)) {
                $carreraPrimaria = $elegiblesPorCarrera->keys()->first();
            } else {
                $carrerasAsignadas[] = $carreraPrimaria;
            }

            // Paso 2: Asignar primero todos los de la carrera primaria
            $elegiblesCarreraPrimaria = ($elegiblesPorCarrera[$carreraPrimaria] ?? collect())->shuffle();
            foreach ($elegiblesCarreraPrimaria as $inscripcion) {
                if ($data['asignados']->count() >= $cuposDisponibles) {
                    break;
                }
                if (!$asignadosGlobal->contains($inscripcion->id)) {
                    $data['asignados']->push($inscripcion);
                    $asignadosGlobal->push($inscripcion->id);
                }
            }

            // Paso 3: Completar con otras carreras si quedan cupos
            $otrasCarreras = $elegiblesPorCarrera->forget($carreraPrimaria);
            foreach ($otrasCarreras as $carreraId => $elegibles) {
                $elegibles = $elegibles->shuffle();
                foreach ($elegibles as $inscripcion) {
                    if ($data['asignados']->count() >= $cuposDisponibles) {
                        break 2;
                    }
                    if (!$asignadosGlobal->contains($inscripcion->id)) {
                        $data['asignados']->push($inscripcion);
                        $asignadosGlobal->push($inscripcion->id);
                    }
                }
            }
        }
    }

    /**
     * Ejecutar asignación con persistencia en BD.
     */
    private function ejecutarAsignacion($comisiones, array $excluirIds = [], bool $agruparPorCarrera = false, ?int $filtrarEspecialidad = null)
    {
        $totalAsignados = 0;
        $comisionesConAsignados = 0;
        $asignadosGlobal = collect($excluirIds);

        // Obtener todos los alumnos sin asignar
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar($filtrarEspecialidad)
            ->reject(fn($i) => in_array($i->id, $excluirIds));

        // Preparar pool por comisión
        $poolPorComision = [];
        foreach ($comisiones as $comision) {
            $elegibles = $this->filtrarPorRequisitos($alumnosSinAsignar, $comision);
            $poolPorComision[$comision->id] = [
                'comision' => $comision,
                'elegibles' => $elegibles,
                'cuposDisponibles' => $this->getCuposParaAsignacion($comision),
                'asignados' => collect(),
            ];
        }

        if ($agruparPorCarrera) {
            $this->distribuirAgrupandoPorCarrera($poolPorComision, $asignadosGlobal);
        } else {
            $this->distribuirRoundRobinSimple($poolPorComision, $asignadosGlobal);
        }

        // Persistir asignaciones
        foreach ($poolPorComision as $data) {
            if ($data['asignados']->isEmpty()) {
                continue;
            }

            $comisionesConAsignados++;

            foreach ($data['asignados'] as $inscripcion) {
                $this->persistirAsignacion($inscripcion, $data['comision'], $agruparPorCarrera);
                $totalAsignados++;
            }
        }

        return [
            'total' => $totalAsignados,
            'comisiones' => $comisionesConAsignados,
        ];
    }

    /**
     * Persistir una asignación individual (InscripcionComision + Cursada + Trayectoria).
     */
    private function persistirAsignacion(Inscripcion $inscripcion, Comision $comision, bool $porCarrera = false): void
    {
        $academicoDato = $inscripcion->getAcademicoDato();
        $observacion = $porCarrera
            ? 'Asignación automática (agrupada por carrera)'
            : 'Asignación aleatoria automática';

        // Buscar si existe una inscripción_comision cancelada para reactivarla
        $existente = InscripcionComision::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $comision->id)
            ->whereIn('estado', ['cancelado', 'trasladado'])
            ->first();

        if ($existente) {
            $existente->update([
                'estado' => 'inscripto',
                'fecha_inscripcion' => now(),
                'observaciones' => $observacion . ' - Reactivada',
            ]);
        } else {
            InscripcionComision::create([
                'inscripcion_id' => $inscripcion->id,
                'academico_dato_id' => $academicoDato?->id,
                'comision_id' => $comision->id,
                'fecha_inscripcion' => now(),
                'estado' => 'inscripto',
                'observaciones' => $observacion,
            ]);
        }

        // Crear o reactivar cursada
        $anioActual = date('Y');
        $esRecursante = Cursada::where('inscripcion_id', $inscripcion->id)
            ->where('anio', '<', $anioActual)
            ->whereIn('estado', [Cursada::ESTADO_DESAPROBADO, Cursada::ESTADO_LIBRE])
            ->exists();

        $cursadaExistente = Cursada::where('inscripcion_id', $inscripcion->id)
            ->where('comision_id', $comision->id)
            ->where('anio', $anioActual)
            ->first();

        if ($cursadaExistente) {
            if ($cursadaExistente->estado === Cursada::ESTADO_BAJA) {
                $cursadaExistente->update([
                    'estado' => Cursada::ESTADO_CURSANDO,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                    'usuario_cambio_estado_id' => auth()->id(),
                    'fecha_cambio_estado' => now(),
                    'observaciones' => 'Reactivada por reasignación automática',
                ]);
            }
        } else {
            Cursada::create([
                'inscripcion_id' => $inscripcion->id,
                'comision_id' => $comision->id,
                'anio' => $anioActual,
                'estado' => Cursada::ESTADO_CURSANDO,
                'modalidad' => $comision->modalidad,
                'es_recursante' => $esRecursante,
                'fecha_inicio' => now(),
            ]);
        }

        // Actualizar estado de la inscripción
        $estadoAnterior = $inscripcion->estado_ingreso;
        $inscripcion->update(['estado_ingreso' => Inscripcion::INGRESO_CURSANDO]);

        // Registrar en trayectoria
        if ($estadoAnterior !== Inscripcion::INGRESO_CURSANDO) {
            try {
                \App\Models\Trayectoria::registrarEvento(
                    $inscripcion->id,
                    \App\Models\Trayectoria::ESTADO_ACTIVO,
                    'Asignado a comisión ' . $comision->nombre,
                    null,
                    auth()->id()
                );
            } catch (\Exception $e) {
                \Log::warning("No se pudo crear trayectoria para inscripción {$inscripcion->id}: " . $e->getMessage());
            }
        }

        $comision->incrementarCupo();
    }
}
