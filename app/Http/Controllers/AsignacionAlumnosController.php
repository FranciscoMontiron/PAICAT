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

        // Estadísticas generales
        $totalCuposDisponibles = $comisiones->sum('cupos_disponibles');

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
            'distribuir_por_carrera' => 'nullable|boolean',
            'filtrar_especialidad' => 'nullable|integer',
        ]);

        $comisionesIds = $request->comisiones;
        $distribuirPorCarrera = $request->boolean('distribuir_por_carrera', false);
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
        $simulacion = $this->simularAsignacion($comisiones, $distribuirPorCarrera, $filtrarEspecialidad);

        return view('asignacion-alumnos.preview', [
            'comisiones' => $comisiones,
            'simulacion' => $simulacion,
            'comisionesIds' => $comisionesIds,
            'distribuirPorCarrera' => $distribuirPorCarrera,
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
            'distribuir_por_carrera' => 'nullable|boolean',
            'filtrar_especialidad' => 'nullable|integer',
        ]);

        $comisionesIds = $request->comisiones;
        $excluirIds = $request->excluir ?? [];
        $distribuirPorCarrera = $request->boolean('distribuir_por_carrera', false);
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
            // Usar la conexión específica de paicat para la transacción
            DB::connection('paicat')->beginTransaction();

            $resultado = $this->ejecutarAsignacionEquitativa($comisiones, $excluirIds, $distribuirPorCarrera, $filtrarEspecialidad);

            DB::connection('paicat')->commit();

            $mensaje = "Se asignaron {$resultado['total']} alumnos a {$resultado['comisiones']} comisiones.";
            if ($distribuirPorCarrera) {
                $mensaje .= " (Distribución equitativa por carrera)";
            }

            return redirect()->route('asignacion-alumnos.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::connection('paicat')->rollBack();
            return redirect()->route('asignacion-alumnos.index')
                ->with('error', 'Error al ejecutar la asignación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener inscripciones sin asignar a ninguna comisión
     */
    private function obtenerAlumnosSinAsignar(?array $comisionesIds = null, ?int $filtrarEspecialidad = null)
    {
        return Inscripcion::activas()
            ->whereDoesntHave('inscripcionesComision', function ($q) use ($comisionesIds) {
                if ($comisionesIds) {
                    $q->whereIn('comision_id', $comisionesIds);
                }
            })
            ->when($filtrarEspecialidad, fn($q) => $q->where('especialidad_id_sysacad', $filtrarEspecialidad))
            ->get();
    }

    /**
     * Filtrar alumnos elegibles por requisitos (Modalidad)
     * Ignora especialidad según reglas de ingreso.
     */
    private function filtrarPorRequisitos($inscripciones, Comision $comision)
    {
        return $inscripciones->filter(function ($inscripcion) use ($comision) {
            // Regla 1: La modalidad debe coincidir (Presencial/Virtual)
            if ($inscripcion->modalidad !== $comision->modalidad) {
                return false;
            }

            // Regla 2: Ignorar especialidad (cualquiera puede ir a cualquier comisión de su modalidad)

            // Regla 3: Turno (Opcional, por ahora ignorado hasta confirmar formato)

            return true;
        });
    }

    /**
     * Obtener especialidades/carreras desde sysacad
     */
    private function obtenerEspecialidades(): array
    {
        try {
            $especialidades = DB::connection('sysacad')
                ->table('sysacad_especialidades')
                ->orderBy('nombre')
                ->pluck('nombre', 'id_sysacad')
                ->toArray();

            return $especialidades;
        } catch (\Exception $e) {
            // Si falla la conexión a sysacad, devolver array vacío
            return [];
        }
    }

    /**
     * Simular asignación sin ejecutar cambios
     * @param bool $distribuirPorCarrera Si true, distribuye equitativamente por carrera
     * @param int|null $filtrarEspecialidad Si se especifica, solo asigna alumnos de esa especialidad
     */
    private function simularAsignacion($comisiones, bool $distribuirPorCarrera = false, ?int $filtrarEspecialidad = null)
    {
        $simulacion = [];
        $asignadosGlobal = collect();

        // Obtener todos los alumnos sin asignar
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar($comisiones->pluck('id')->toArray(), $filtrarEspecialidad);

        // Preparar pool por comisión
        $poolPorComision = [];
        foreach ($comisiones as $comision) {
            $elegibles = $this->filtrarPorRequisitos($alumnosSinAsignar, $comision);
            $poolPorComision[$comision->id] = [
                'comision' => $comision,
                'elegibles' => $elegibles,
                'cuposDisponibles' => max(0, $comision->cupo_maximo - max(0, $comision->cupo_actual)),
                'asignados' => collect(),
            ];
        }

        if ($distribuirPorCarrera) {
            // Distribución equitativa por carrera: round-robin alternando entre carreras
            $this->distribuirPorCarreraRoundRobin($poolPorComision, $asignadosGlobal);
        } else {
            // Distribución equitativa simple: round-robin
            $this->distribuirRoundRobinSimple($poolPorComision, $asignadosGlobal);
        }

        // Preparar resultado de simulación
        foreach ($poolPorComision as $data) {
            // Calcular distribución por carrera en los asignados
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
     * Distribución round-robin simple (sin considerar carrera)
     */
    private function distribuirRoundRobinSimple(array &$poolPorComision, &$asignadosGlobal): void
    {
        // Mezclar alumnos
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
     * Distribución round-robin por carrera
     * Alterna entre carreras para asegurar distribución equitativa
     */
    private function distribuirPorCarreraRoundRobin(array &$poolPorComision, &$asignadosGlobal): void
    {
        // Obtener todas las carreras únicas de los elegibles
        $todasLasCarreras = collect();
        foreach ($poolPorComision as &$data) {
            // Agrupar elegibles por carrera y mezclar dentro de cada grupo
            $data['elegiblesPorCarrera'] = $data['elegibles']
                ->groupBy('especialidad_id_sysacad')
                ->map(fn($group) => $group->shuffle()->values());

            $todasLasCarreras = $todasLasCarreras->merge($data['elegiblesPorCarrera']->keys());
        }
        $carrerasUnicas = $todasLasCarreras->unique()->shuffle()->values();

        // Índice circular para iterar sobre carreras
        $carreraIndex = 0;
        $totalCarreras = $carrerasUnicas->count();

        if ($totalCarreras === 0) {
            return;
        }

        $hayMas = true;
        $iteracionesSinCambio = 0;
        $maxIteraciones = $totalCarreras * 10; // Límite de seguridad

        while ($hayMas && $iteracionesSinCambio < $maxIteraciones) {
            $hayMas = false;
            $carreraActual = $carrerasUnicas[$carreraIndex % $totalCarreras];

            // Iterar sobre comisiones en round-robin
            foreach ($poolPorComision as &$data) {
                if ($data['asignados']->count() >= $data['cuposDisponibles']) {
                    continue;
                }

                // Buscar alumno de la carrera actual
                if (!isset($data['elegiblesPorCarrera'][$carreraActual])) {
                    continue;
                }

                $elegiblesDeCarrera = $data['elegiblesPorCarrera'][$carreraActual];

                foreach ($elegiblesDeCarrera as $key => $inscripcion) {
                    if (!$asignadosGlobal->contains($inscripcion->id)) {
                        $data['asignados']->push($inscripcion);
                        $asignadosGlobal->push($inscripcion->id);
                        $data['elegiblesPorCarrera'][$carreraActual]->forget($key);
                        $hayMas = true;
                        $iteracionesSinCambio = 0;
                        break;
                    }
                }
            }

            $carreraIndex++;
            if (!$hayMas) {
                $iteracionesSinCambio++;
                $hayMas = true; // Continuar buscando en otras carreras
            }
        }
    }

    /**
     * Ejecutar asignación equitativa (round-robin)
     */
    private function ejecutarAsignacionEquitativa($comisiones, array $excluirIds = [], bool $distribuirPorCarrera = false, ?int $filtrarEspecialidad = null)
    {
        $totalAsignados = 0;
        $comisionesConAsignados = 0;
        $asignadosGlobal = collect($excluirIds);

        // Obtener todos los alumnos sin asignar
        $alumnosSinAsignar = $this->obtenerAlumnosSinAsignar($comisiones->pluck('id')->toArray(), $filtrarEspecialidad)
            ->reject(fn($i) => in_array($i->id, $excluirIds));

        // Preparar pool por comisión
        $poolPorComision = [];
        foreach ($comisiones as $comision) {
            $elegibles = $this->filtrarPorRequisitos($alumnosSinAsignar, $comision);
            $poolPorComision[$comision->id] = [
                'comision' => $comision,
                'elegibles' => $elegibles,
                'cuposDisponibles' => max(0, $comision->cupo_maximo - max(0, $comision->cupo_actual)),
                'asignados' => collect(),
            ];
        }

        if ($distribuirPorCarrera) {
            // Distribución equitativa por carrera
            $this->distribuirPorCarreraRoundRobin($poolPorComision, $asignadosGlobal);
        } else {
            // Distribución equitativa simple
            $this->distribuirRoundRobinSimple($poolPorComision, $asignadosGlobal);
        }

        // Crear inscripciones a comisión
        foreach ($poolPorComision as $data) {
            if ($data['asignados']->isEmpty()) {
                continue;
            }

            $comisionesConAsignados++;

            foreach ($data['asignados'] as $inscripcion) {
                // Obtener academico_dato_id si existe
                $academicoDato = $inscripcion->getAcademicoDato();

                $observacion = $distribuirPorCarrera
                    ? 'Asignación aleatoria automática (distribución por carrera)'
                    : 'Asignación aleatoria automática';

                InscripcionComision::create([
                    'inscripcion_id' => $inscripcion->id,
                    'academico_dato_id' => $academicoDato?->id,
                    'comision_id' => $data['comision']->id,
                    'fecha_inscripcion' => now(),
                    'estado' => 'inscripto',
                    'observaciones' => $observacion,
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
                        'comision_id' => $data['comision']->id,
                        'anio' => $anioActual,
                    ],
                    [
                        'estado' => Cursada::ESTADO_CURSANDO,
                        'modalidad' => $data['comision']->modalidad,
                        'es_recursante' => $esRecursante,
                        'fecha_inicio' => now(),
                    ]
                );

                // Actualizar estado de la inscripción a 'cursando'
                $inscripcion->update(['estado_ingreso' => Inscripcion::INGRESO_CURSANDO]);

                $data['comision']->incrementarCupo();
                $totalAsignados++;
            }
        }

        return [
            'total' => $totalAsignados,
            'comisiones' => $comisionesConAsignados,
        ];
    }
}
