<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Listado de comisiones para asistencias
     * Si es docente, solo ve sus comisiones asignadas
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Comision::with(['docente', 'inscripciones.asistencias', 'materias']);

        // Si es docente, solo mostrar sus comisiones
        if ($user->hasRole('Docente')) {
            $query->where('docente_id', $user->id);
        }

        // Filtros
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto, solo comisiones activas
            $query->where('estado', 'activa');
        }

        // Obtener todas las comisiones para calcular stats (sin paginar)
        $todasComisiones = (clone $query)->get();
        
        // Calcular estadísticas globales
        $totalAlumnos = 0;
        $sumaPromedios = 0;
        $totalAlumnosEnRiesgo = 0;
        $comisionesConAlumnos = 0;
        
        foreach ($todasComisiones as $comision) {
            $cantidadAlumnos = $comision->inscripciones->count();
            $totalAlumnos += $cantidadAlumnos;
            
            if ($cantidadAlumnos > 0) {
                $promedioComision = 0;
                $alumnosEnRiesgo = 0;
                $sumaPorcentajes = 0;
                
                foreach ($comision->inscripciones as $inscripcion) {
                    $porcentaje = $this->calcularPorcentajeAsistencia($inscripcion);
                    $sumaPorcentajes += $porcentaje;
                    
                    if ($porcentaje < 75) {
                        $alumnosEnRiesgo++;
                    }
                }
                
                $promedioComision = $sumaPorcentajes / $cantidadAlumnos;
                $sumaPromedios += $promedioComision;
                $totalAlumnosEnRiesgo += $alumnosEnRiesgo;
                $comisionesConAlumnos++;
            }
        }
        
        $promedioGeneral = $comisionesConAlumnos > 0 ? $sumaPromedios / $comisionesConAlumnos : 0;

        // Estadísticas generales
        $stats = [
            'comisiones_total' => $todasComisiones->count(),
            'comisiones_activas' => $todasComisiones->where('estado', 'activa')->count(),
            'total_alumnos' => $totalAlumnos,
            'promedio_asistencia' => round($promedioGeneral, 1),
            'alumnos_en_riesgo' => $totalAlumnosEnRiesgo,
        ];

        // Paginar comisiones
        $comisiones = $query->orderBy('codigo')->paginate(12)->appends($request->all());
        
        // Calcular estadísticas para cada comisión paginada
        foreach ($comisiones as $comision) {
            $cantidadAlumnos = $comision->inscripciones->count();
            $promedioAsistencia = 0;
            $alumnosEnRiesgo = 0;
            
            if ($cantidadAlumnos > 0) {
                $sumaPorcentajes = 0;
                
                foreach ($comision->inscripciones as $inscripcion) {
                    $porcentaje = $this->calcularPorcentajeAsistencia($inscripcion);
                    $sumaPorcentajes += $porcentaje;
                    
                    if ($porcentaje < 75) {
                        $alumnosEnRiesgo++;
                    }
                }
                
                $promedioAsistencia = $sumaPorcentajes / $cantidadAlumnos;
            }
            
            $comision->promedio_asistencia = $promedioAsistencia;
            $comision->alumnos_en_riesgo = $alumnosEnRiesgo;
        }

        return view('asistencias.index', compact('comisiones', 'stats'));
    }

    /**
     * Ver materias de una comisión
     */
    public function comisionMaterias(Comision $comision)
    {
        $user = auth()->user();

        // Verificar permisos
        if (!auth()->user()->hasPermission('asistencias.ver')) {
            abort(403);
        }

        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta comisión.');
        }

        // Cargar todas las relaciones necesarias (EAGER LOADING)
        $comision->load([
            'docente',
            'materias',
            'inscripciones' => function ($query) {
                $query->with([
                    'academicoDato.user',  // Usuario a través de academicoDato
                    'inscripcion',         // Inscripción original (fallback)
                    'asistencias'          // Para cálculos de porcentaje
                ]);
            }
        ]);

        // Calcular estadísticas generales de la comisión
        $totalAlumnos = $comision->inscripciones->count();
        $alumnosEnRiesgo = 0;
        $sumaPorcentajes = 0;

        foreach ($comision->inscripciones as $inscripcion) {
            $porcentaje = $this->calcularPorcentajeAsistencia($inscripcion);
            $sumaPorcentajes += $porcentaje;
            if ($porcentaje < 75) {
                $alumnosEnRiesgo++;
            }
        }

        $promedioAsistencia = $totalAlumnos > 0 ? $sumaPorcentajes / $totalAlumnos : 0;

        return view('asistencias.materias', compact(
            'comision',
            'totalAlumnos',
            'promedioAsistencia',
            'alumnosEnRiesgo'
        ));
    }

    /**
     * Historial de asistencias de una materia específica
     */
    public function materiaHistorial(Comision $comision, Materia $materia, Request $request)
    {
        $user = auth()->user();

        // Verificar permisos
        if (!auth()->user()->hasPermission('asistencias.ver')) {
            abort(403);
        }

        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta comisión.');
        }

        // Obtener todas las fechas con asistencia registrada para esta materia
        $fechasAsistencia = Asistencia::whereHas('inscripcionComision', function ($query) use ($comision) {
                $query->where('comision_id', $comision->id);
            })
            ->where('materia_id', $materia->id)
            ->select('fecha')
            ->distinct()
            ->orderBy('fecha', 'desc')
            ->get()
            ->pluck('fecha');

        // Cargar inscripciones de la comisión con sus asistencias
        $inscripciones = $comision->inscripciones()
            ->with([
                'academicoDato.user',
                'inscripcion',
                'asistencias' => function ($query) use ($materia) {
                    $query->where('materia_id', $materia->id)
                          ->orderBy('fecha', 'desc');
                }
            ])
            ->get();

        // Calcular estadísticas por alumno
        $estadisticas = collect();
        foreach ($inscripciones as $inscripcion) {
            $asistenciasAlumno = $inscripcion->asistencias->where('materia_id', $materia->id);

            if ($asistenciasAlumno->count() > 0) {
                $total = $asistenciasAlumno->count();
                $presentes = $asistenciasAlumno->where('estado', 'presente')->count();
                $tardanzas = $asistenciasAlumno->where('estado', 'tardanza')->count();
                $ausentes = $asistenciasAlumno->where('estado', 'ausente')->count();
                $justificados = $asistenciasAlumno->where('estado', 'justificado')->count();

                $asistio = $presentes + $tardanzas + $justificados;
                $porcentaje = round(($asistio / $total) * 100, 1);
                $enRiesgo = $porcentaje < 75;

                $estadisticas->push([
                    'inscripcion' => $inscripcion,
                    'total' => $total,
                    'presentes' => $presentes,
                    'tardanzas' => $tardanzas,
                    'ausentes' => $ausentes,
                    'justificados' => $justificados,
                    'porcentaje' => $porcentaje,
                    'en_riesgo' => $enRiesgo
                ]);
            }
        }

        // Agrupar asistencias por fecha para vista de tabla
        $asistenciasQuery = Asistencia::whereIn('inscripcion_comision_id', $inscripciones->pluck('id'))
            ->where('materia_id', $materia->id)
            ->with(['inscripcionComision.inscripcion', 'inscripcionComision.academicoDato', 'registradoPor']);

        // Aplicar filtros de fecha
        if ($request->filled('fecha_desde')) {
            $asistenciasQuery->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $asistenciasQuery->where('fecha', '<=', $request->fecha_hasta);
        }

        $asistencias = $asistenciasQuery->orderBy('fecha', 'desc')->get();
        $fechasClases = $asistencias->groupBy(function ($asistencia) {
            return $asistencia->fecha->format('Y-m-d');
        });

        // Calcular estadísticas generales de la materia
        $estadisticasGenerales = $this->calcularEstadisticasMateria($materia, $comision->id);

        return view('asistencias.historial-materia', compact(
            'comision',
            'materia',
            'estadisticas',
            'fechasClases',
            'fechasAsistencia',
            'estadisticasGenerales',
            'inscripciones'
        ));
    }

    /**
     * Mostrar formulario para tomar asistencia de una materia específica
     * Ruta: GET /asistencias/comision/{comision}/materia/{materia}/registrar
     */
    public function tomarAsistencia(Comision $comision, Materia $materia)
    {
        // Verificar permisos
        if (!auth()->user()->hasPermission('asistencias.crear')) {
            abort(403, 'No tienes permiso para registrar asistencias');
        }

        if (auth()->user()->hasRole('Docente') && $comision->docente_id !== auth()->id()) {
            abort(403, 'No tienes permiso para esta comisión.');
        }

        // Obtener fecha (hoy por defecto, o la que venga por parámetro)
        $fecha = request('fecha', today()->format('Y-m-d'));

        // Obtener inscripciones de la comisión con sus relaciones
        $inscripciones = $comision->inscripciones()
            ->with([
                'academicoDato.user',
                'inscripcion',
                'asistencias' => function ($query) use ($fecha, $materia) {
                    $query->where('fecha', $fecha)
                          ->where('materia_id', $materia->id);
                }
            ])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get();

        return view('asistencias.create', compact(
            'comision',
            'materia',
            'inscripciones',
            'fecha'
        ));
    }

    /**
     * Guardar asistencia de una materia específica
     * Ruta: POST /asistencias/comision/{comision}/materia/{materia}/guardar
     */
    public function guardarAsistencia(Request $request, Comision $comision, Materia $materia)
    {
        // Verificar permisos
        if (!auth()->user()->hasPermission('asistencias.crear')) {
            abort(403, 'No tienes permiso para registrar asistencias');
        }

        if (auth()->user()->hasRole('Docente') && $comision->docente_id !== auth()->id()) {
            abort(403, 'No tienes permiso para esta comisión.');
        }

        // Validar datos
        $validated = $request->validate([
            'fecha' => 'required|date|before_or_equal:today',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripciones_comision,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500',
        ]);

        $fecha = Carbon::parse($validated['fecha']);
        $registrados = 0;
        $actualizados = 0;

        DB::beginTransaction();
        try {
            foreach ($validated['asistencias'] as $asistenciaData) {
                // Buscar si ya existe un registro para esta inscripción, fecha y materia
                $asistencia = Asistencia::where('inscripcion_comision_id', $asistenciaData['inscripcion_comision_id'])
                    ->where('fecha', $fecha)
                    ->where('materia_id', $materia->id)
                    ->first();

                $datos = [
                    'inscripcion_comision_id' => $asistenciaData['inscripcion_comision_id'],
                    'materia_id' => $materia->id,
                    'fecha' => $fecha,
                    'estado' => $asistenciaData['estado'],
                    'observaciones' => $asistenciaData['observaciones'] ?? null,
                    'registrado_por' => auth()->id(),
                ];

                if ($asistencia) {
                    // Actualizar registro existente
                    $asistencia->update($datos);
                    $actualizados++;
                } else {
                    // Crear nuevo registro
                    Asistencia::create($datos);
                    $registrados++;
                }
            }

            DB::commit();

            $mensaje = "Asistencia guardada exitosamente.";
            if ($registrados > 0) {
                $mensaje .= " Registros nuevos: $registrados.";
            }
            if ($actualizados > 0) {
                $mensaje .= " Registros actualizados: $actualizados.";
            }

            return redirect()
                ->route('asistencias.materia.historial', [$comision, $materia])
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para registrar asistencia de una materia
     * (Mantengo el método original con nombre diferente)
     */
    public function materiaRegistrar(Comision $comision, Materia $materia)
    {
        $user = auth()->user();

        // Verificar permisos
        if (!$user->hasPermission('asistencias.crear')) {
            abort(403, 'No tienes permiso para registrar asistencias.');
        }

        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403, 'No tienes permiso para esta comisión.');
        }

        // Obtener la fecha (hoy por defecto o la enviada)
        $fecha = request('fecha', today()->format('Y-m-d'));

        // Obtener inscripciones con sus asistencias para esta fecha y materia
        $inscripciones = $comision->inscripciones()
            ->with(['inscripcion', 'academicoDato', 'asistencias' => function ($query) use ($fecha, $materia) {
                $query->where('fecha', $fecha)
                    ->where('materia_id', $materia->id);
            }])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get();

        return view('asistencias.create', compact('comision', 'materia', 'inscripciones', 'fecha'));
    }

    /**
     * Guardar asistencia de una materia
     * (Mantengo el método original)
     */
    public function materiaGuardar(Request $request, Comision $comision, Materia $materia)
    {
        $user = auth()->user();

        // Verificar permisos
        if (!$user->hasPermission('asistencias.crear')) {
            abort(403, 'No tienes permiso para registrar asistencias.');
        }

        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403, 'No tienes permiso para esta comisión.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date|before_or_equal:today',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripciones_comision,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['asistencias'] as $asistenciaData) {
                Asistencia::updateOrCreate(
                    [
                        'inscripcion_comision_id' => $asistenciaData['inscripcion_comision_id'],
                        'fecha' => $validated['fecha'],
                        'materia_id' => $materia->id
                    ],
                    [
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                        'registrado_por' => $user->id
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('asistencias.materia.historial', [$comision, $materia])
                ->with('success', 'Asistencia registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar asistencia de una materia
     */
    public function materiaEditar(Comision $comision, Materia $materia, Request $request)
    {
        $user = auth()->user();

        // Verificar permisos
        if (!$user->hasPermission('asistencias.editar')) {
            abort(403, 'No tienes permiso para editar asistencias.');
        }

        $fecha = $request->get('fecha', today()->format('Y-m-d'));

        // Obtener inscripciones con sus asistencias para esta fecha y materia
        $inscripciones = $comision->inscripciones()
            ->with(['inscripcion', 'academicoDato', 'asistencias' => function ($query) use ($fecha, $materia) {
                $query->where('fecha', $fecha)
                    ->where('materia_id', $materia->id);
            }])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get();

        return view('asistencias.edit', compact('comision', 'materia', 'inscripciones', 'fecha'));
    }

    /**
     * Actualizar asistencia de una materia
     */
    public function materiaActualizar(Request $request, Comision $comision, Materia $materia)
    {
        $user = auth()->user();

        // Verificar permisos
        if (!$user->hasPermission('asistencias.editar')) {
            abort(403, 'No tienes permiso para editar asistencias.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripciones_comision,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['asistencias'] as $asistenciaData) {
                Asistencia::updateOrCreate(
                    [
                        'inscripcion_comision_id' => $asistenciaData['inscripcion_comision_id'],
                        'fecha' => $validated['fecha'],
                        'materia_id' => $materia->id
                    ],
                    [
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                        'registrado_por' => $user->id
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('asistencias.materia.historial', [$comision, $materia])
                ->with('success', 'Asistencia actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Seleccionar alumno para justificar inasistencias por materia
     * Ruta: GET /asistencias/comision/{comision}/materia/{materia}/justificar-alumno
     */
    public function seleccionarAlumnoPorMateria(Comision $comision, Materia $materia)
    {
        // Verificar permisos
        if (!auth()->user()->hasPermission('asistencias.editar')) {
            abort(403);
        }

        // Verificar que el docente solo pueda acceder a sus comisiones
        if (auth()->user()->hasRole('Docente') && $comision->docente_id !== auth()->id()) {
            abort(403, 'No tienes permiso para esta comisión.');
        }

        // Obtener inscripciones con ausencias sin justificar para esta materia
        $inscripciones = $comision->inscripciones()
            ->whereHas('asistencias', function($q) use ($materia) {
                $q->where('materia_id', $materia->id)
                  ->where('estado', 'ausente');
            })
            ->with([
                'academicoDato.user',
                'inscripcion',
                'asistencias' => function($q) use ($materia) {
                    $q->where('materia_id', $materia->id)
                      ->where('estado', 'ausente')
                      ->orderBy('fecha', 'desc');
                }
            ])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get();

        return view('asistencias.seleccionar-alumno-por-materia', compact(
            'comision',
            'materia',
            'inscripciones'
        ));
    }

    /**
     * Ver alertas de alumnos en riesgo
     */
    public function alertas(Request $request)
    {
        $comisionId = $request->input('comision_id');
        $query = InscripcionComision::with(['inscripcion', 'academicoDato', 'comision', 'asistencias'])
            ->whereIn('estado', ['inscripto', 'confirmado']);

        if ($comisionId) {
            $query->where('comision_id', $comisionId);
        }

        $inscripciones = $query->get();

        // Filtrar alumnos en riesgo
        $alumnosEnRiesgo = $inscripciones->filter(function ($inscripcion) {
            if ($inscripcion->asistencias->count() === 0) {
                return false;
            }

            $porcentaje = $this->calcularPorcentajeAsistencia($inscripcion);

            // En riesgo si tiene menos de 75% de asistencia
            return $porcentaje < 75;
        })->sortBy(function ($inscripcion) {
            return $this->calcularPorcentajeAsistencia($inscripcion);
        });

        $comisiones = Comision::where('estado', 'activa')->orderBy('codigo')->get();

        return view('asistencias.alertas', compact('alumnosEnRiesgo', 'comisiones', 'comisionId'));
    }

    /**
     * Listado de asistencia filtrado por materia
     */
    public function porMateria(Request $request)
    {
        $user = auth()->user();

        // Obtener materias disponibles
        $materiasQuery = Materia::with('comisiones')->where('activa', true);

        // Si es docente, solo mostrar materias de sus comisiones
        if ($user->hasRole('Docente')) {
            $materiasQuery->whereHas('comisiones', function ($q) use ($user) {
                $q->where('docente_id', $user->id);
            });
        }

        $materias = $materiasQuery->orderBy('nombre')->get();

        $materiaId = $request->input('materia_id');
        $comisionId = $request->input('comision_id');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $asistencias = collect();
        $estadisticasPorAlumno = collect();
        $materiaSeleccionada = null;
        $comisionSeleccionada = null;
        $comisionesMateria = collect();

        if ($materiaId) {
            $materiaSeleccionada = Materia::find($materiaId);

            if ($materiaSeleccionada) {
                // Obtener comisiones de esta materia
                $comisionesQuery = $materiaSeleccionada->comisiones()
                    ->where('estado', 'activa');

                if ($user->hasRole('Docente')) {
                    $comisionesQuery->where('docente_id', $user->id);
                }

                $comisionesMateria = $comisionesQuery->orderBy('codigo')->get();

                // Construir query de asistencias
                $query = Asistencia::with(['inscripcion', 'inscripcionComision.comision', 'registradoPor'])
                    ->where('materia_id', $materiaId);

                if ($comisionId) {
                    $comisionSeleccionada = Comision::find($comisionId);
                    $query->whereHas('inscripcionComision', function ($q) use ($comisionId) {
                        $q->where('comision_id', $comisionId);
                    });
                }

                if ($fechaDesde) {
                    $query->where('fecha', '>=', $fechaDesde);
                }

                if ($fechaHasta) {
                    $query->where('fecha', '<=', $fechaHasta);
                }

                $asistencias = $query->orderBy('fecha', 'desc')->get();

                // Calcular estadísticas por alumno
                $estadisticasPorAlumno = $asistencias->groupBy('inscripcion_comision_id')
                    ->map(function ($asistenciasAlumno) {
                        $inscripcionComision = $asistenciasAlumno->first()->inscripcionComision;
                        $total = $asistenciasAlumno->count();
                        $presentes = $asistenciasAlumno->where('estado', 'presente')->count();
                        $tardanzas = $asistenciasAlumno->where('estado', 'tardanza')->count();
                        $ausentes = $asistenciasAlumno->where('estado', 'ausente')->count();
                        $justificados = $asistenciasAlumno->where('estado', 'justificado')->count();

                        $asistio = $presentes + $tardanzas + $justificados;
                        $porcentaje = $total > 0 ? round(($asistio / $total) * 100, 2) : 0;

                        // Obtener datos del alumno desde alumnos_utn
                        $person = $inscripcionComision?->inscripcion?->getPerson();

                        return [
                            'inscripcion_comision' => $inscripcionComision,
                            'alumno_nombre' => $person ? "{$person->nombre} {$person->apellido}" : 'N/A',
                            'alumno_documento' => $person?->documento ?? 'N/A',
                            'comision' => $inscripcionComision?->comision?->nombre ?? 'N/A',
                            'total' => $total,
                            'presentes' => $presentes,
                            'tardanzas' => $tardanzas,
                            'ausentes' => $ausentes,
                            'justificados' => $justificados,
                            'porcentaje' => $porcentaje,
                            'en_riesgo' => $porcentaje < config('paicat.asistencia_minima', 75),
                        ];
                    })
                    ->sortBy('alumno_nombre');
            }
        }

        return view('asistencias.por-materia', compact(
            'materias',
            'materiaSeleccionada',
            'comisionesMateria',
            'comisionSeleccionada',
            'asistencias',
            'estadisticasPorAlumno',
            'materiaId',
            'comisionId',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Vista de una comisión (MANTENER POR COMPATIBILIDAD)
     */
    public function comision(Comision $comision)
    {
        // Redirigir a la nueva vista de materias
        return redirect()->route('asistencias.comision.materias', $comision);
    }

    /**
     * Ver historial de una comisión (MANTENER POR COMPATIBILIDAD)
     */
    public function historial(Comision $comision)
    {
        // Redirigir a la vista de materias de la comisión
        return redirect()->route('asistencias.comision.materias', $comision);
    }

    /**
     * Formulario para crear asistencia (MANTENER POR COMPATIBILIDAD - sin materia)
     */
    public function create(Comision $comision)
    {
        // Si la comisión tiene materias, redirigir a seleccionar materia
        if ($comision->materias->count() > 0) {
            return view('asistencias.seleccionar-materia', compact('comision'));
        }

        // Si no tiene materias, mostrar formulario genérico
        $fecha = request('fecha', today()->format('Y-m-d'));
        $inscripciones = $comision->inscripciones()
            ->with(['inscripcion', 'academicoDato', 'asistencias' => function ($query) use ($fecha) {
                $query->where('fecha', $fecha);
            }])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get();

        $materia = null;
        return view('asistencias.create', compact('comision', 'materia', 'inscripciones', 'fecha'));
    }

    /**
     * Guardar asistencia (MANTENER POR COMPATIBILIDAD - sin materia)
     */
    public function store(Request $request, Comision $comision)
    {
        $user = auth()->user();

        if (!$user->hasPermission('asistencias.crear')) {
            abort(403, 'No tienes permiso para registrar asistencias.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date|before_or_equal:today',
            'materia_id' => 'nullable|exists:materias,id',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripciones_comision,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['asistencias'] as $asistenciaData) {
                Asistencia::updateOrCreate(
                    [
                        'inscripcion_comision_id' => $asistenciaData['inscripcion_comision_id'],
                        'fecha' => $validated['fecha'],
                        'materia_id' => $validated['materia_id'] ?? null
                    ],
                    [
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                        'registrado_por' => $user->id
                    ]
                );
            }

            DB::commit();

            // Si tiene materia, redirigir al historial de la materia
            if ($validated['materia_id']) {
                $materia = Materia::find($validated['materia_id']);
                return redirect()
                    ->route('asistencias.materia.historial', [$comision, $materia])
                    ->with('success', 'Asistencia registrada correctamente.');
            }

            return redirect()
                ->route('asistencias.comision.materias', $comision)
                ->with('success', 'Asistencia registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Editar asistencia (MANTENER POR COMPATIBILIDAD)
     */
    public function edit(Comision $comision, $fecha)
    {
        $user = auth()->user();

        if (!$user->hasPermission('asistencias.editar')) {
            abort(403, 'No tienes permiso para editar asistencias.');
        }

        $inscripciones = $comision->inscripciones()
            ->with(['inscripcion', 'academicoDato', 'asistencias' => function ($query) use ($fecha) {
                $query->where('fecha', $fecha);
            }])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get();

        $materia = null;
        return view('asistencias.edit', compact('comision', 'materia', 'inscripciones', 'fecha'));
    }

    /**
     * Actualizar asistencia (MANTENER POR COMPATIBILIDAD)
     */
    public function update(Request $request, Comision $comision, $fecha)
    {
        $user = auth()->user();

        if (!$user->hasPermission('asistencias.editar')) {
            abort(403, 'No tienes permiso para editar asistencias.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'materia_id' => 'nullable|exists:materias,id',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripciones_comision,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Si la fecha cambió, eliminar las asistencias antiguas
            $nuevaFecha = $validated['fecha'];
            if ($fecha !== $nuevaFecha) {
                Asistencia::whereIn('inscripcion_comision_id', collect($validated['asistencias'])->pluck('inscripcion_comision_id'))
                    ->where('fecha', $fecha)
                    ->delete();
            }

            foreach ($validated['asistencias'] as $asistenciaData) {
                Asistencia::updateOrCreate(
                    [
                        'inscripcion_comision_id' => $asistenciaData['inscripcion_comision_id'],
                        'fecha' => $nuevaFecha,
                        'materia_id' => $validated['materia_id'] ?? null
                    ],
                    [
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                        'registrado_por' => $user->id
                    ]
                );
            }

            DB::commit();

            $mensaje = $fecha !== $nuevaFecha
                ? 'Asistencia actualizada y movida a la nueva fecha exitosamente.'
                : 'Asistencia actualizada exitosamente.';

            if ($validated['materia_id']) {
                $materia = Materia::find($validated['materia_id']);
                return redirect()
                    ->route('asistencias.materia.historial', [$comision, $materia])
                    ->with('success', $mensaje);
            }

            return redirect()
                ->route('asistencias.comision.materias', $comision)
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Buscador global de alumnos para justificar inasistencias
     */
    public function buscarAlumno(Request $request)
    {
        $search = $request->get('search', '');

        // Obtener usuarios con rol Alumno que tienen inscripciones activas y ausencias
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'Alumno');
        })
            ->with(['inscripcionesComision' => function ($q) {
                $q->where('estado', 'activo')
                    ->with(['comision', 'asistencias' => function ($query) {
                        $query->where('estado', 'ausente');
                    }]);
            }]);

        // Filtrar por búsqueda
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $alumnos = $query->orderBy('name')->paginate(20);

        // Filtrar alumnos que tienen al menos una ausencia sin justificar
        $alumnos->getCollection()->transform(function ($alumno) {
            $alumno->total_ausencias = $alumno->inscripcionesComision->sum(function ($inscripcion) {
                return $inscripcion->asistencias->count();
            });
            return $alumno;
        });

        // Filtrar solo los que tienen ausencias
        $alumnos->setCollection($alumnos->getCollection()->filter(function ($alumno) {
            return $alumno->total_ausencias > 0;
        }));

        return view('asistencias.buscar-alumno', compact('alumnos', 'search'));
    }

    /**
     * Vista para seleccionar alumno a justificar inasistencias
     */
    public function seleccionarAlumno(Comision $comision)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403);
        }

        // Obtener solo alumnos con ausencias sin justificar
        $inscripciones = $comision->inscripciones()
            ->with(['inscripcion', 'academicoDato', 'asistencias' => function ($query) {
                $query->where('estado', 'ausente');
            }])
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->get()
            ->filter(function ($inscripcion) {
                // Filtrar solo los que tienen ausencias
                return $inscripcion->asistencias->count() > 0;
            })
            ->sortBy(function ($inscripcion) {
                return $inscripcion->alumno->name ?? '';
            });

        return view('asistencias.seleccionar-alumno', compact('comision', 'inscripciones'));
    }

    /**
     * Ver historial individual de un alumno en una comisión
     */
    public function alumnoHistorial(Comision $comision, InscripcionComision $inscripcion)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403);
        }

        // Verificar que la inscripción pertenece a la comisión
        if ($inscripcion->comision_id !== $comision->id) {
            abort(404);
        }

        // Obtener todas las asistencias del alumno ordenadas por fecha
        $asistencias = $inscripcion->asistencias()
            ->with('registradoPor')
            ->orderBy('fecha', 'desc')
            ->get();

        // Calcular estadísticas
        $totalClases = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $ausentes = $asistencias->where('estado', 'ausente')->count();
        $justificados = $asistencias->where('estado', 'justificado')->count();

        $asistio = $presentes + $tardanzas + $justificados;
        $porcentaje = $totalClases > 0 ? round(($asistio / $totalClases) * 100, 2) : 0;

        $estadisticas = [
            'total' => $totalClases,
            'presentes' => $presentes,
            'tardanzas' => $tardanzas,
            'ausentes' => $ausentes,
            'justificados' => $justificados,
            'porcentaje' => $porcentaje,
        ];

        return view('asistencias.alumno-historial', compact('comision', 'inscripcion', 'asistencias', 'estadisticas'));
    }


    public function justificarForm(Comision $comision, InscripcionComision $inscripcion, Request $request)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403);
        }

        // Verificar que la inscripción pertenece a la comisión
        if ($inscripcion->comision_id !== $comision->id) {
            abort(404);
        }

        // Obtener información del alumno
        $alumnoNombre = 'N/A';
        $alumnoDocumento = 'N/A';
        
        if ($inscripcion->academicoDato && $inscripcion->academicoDato->user) {
            $alumnoNombre = $inscripcion->academicoDato->user->name;
            $alumnoDocumento = $inscripcion->academicoDato->documento;
        } elseif ($inscripcion->inscripcion) {
            $person = $inscripcion->inscripcion->getPerson();
            if ($person) {
                $alumnoNombre = $person->nombre . ' ' . $person->apellido;
                $alumnoDocumento = $person->documento;
            }
        }

        // Filtrar ausencias por materia si se especifica
        $materia = null;
        $ausenciasQuery = $inscripcion->asistencias()
            ->where('estado', 'ausente')
            ->orderBy('fecha', 'desc');

        if ($request->has('materia_id')) {
            $materiaId = $request->input('materia_id');
            $materia = Materia::find($materiaId);
            if ($materia) {
                $ausenciasQuery->where('materia_id', $materiaId);
            }
        }

        $ausencias = $ausenciasQuery->get();

        // Obtener nombres de materias para las ausencias
        $materiasIds = $ausencias->pluck('materia_id')->unique()->filter();
        $materiasNombres = Materia::whereIn('id', $materiasIds)
            ->pluck('nombre', 'id');

        // Calcular estadísticas
        $totalAsistencias = $inscripcion->asistencias()->count();
        $totalAusencias = $inscripcion->asistencias()->where('estado', 'ausente')->count();
        $presentes = $inscripcion->asistencias()->where('estado', 'presente')->count();
        $tardanzas = $inscripcion->asistencias()->where('estado', 'tardanza')->count();
        $justificados = $inscripcion->asistencias()->where('estado', 'justificado')->count();
        
        $asistio = $presentes + $tardanzas + $justificados;
        $porcentajeAsistencia = $totalAsistencias > 0 ? round(($asistio / $totalAsistencias) * 100, 1) : 0;

        return view('asistencias.justificar', compact(
            'comision',
            'inscripcion',
            'ausencias',
            'materia',
            'alumnoNombre',
            'alumnoDocumento',
            'materiasNombres',
            'totalAusencias',
            'porcentajeAsistencia'
        ));
    }

    /**
     * Método auxiliar para calcular porcentaje de asistencia de una inscripción
     */
    private function calcularPorcentajeAsistencia($inscripcion)
    {
        $totalAsistencias = $inscripcion->asistencias->count();

        if ($totalAsistencias == 0) {
            return 0;
        }

        $presentes = $inscripcion->asistencias->where('estado', 'presente')->count();
        $tardanzas = $inscripcion->asistencias->where('estado', 'tardanza')->count();
        $justificados = $inscripcion->asistencias->where('estado', 'justificado')->count();

        $asistio = $presentes + $tardanzas + $justificados;

        return round(($asistio / $totalAsistencias) * 100, 1);
    }

    /**
     * Método auxiliar para calcular estadísticas de una materia
     */
    private function calcularEstadisticasMateria(Materia $materia, $comisionId)
    {
        // Obtener todas las asistencias de esta materia en la comisión
        $asistencias = Asistencia::whereHas('inscripcionComision', function ($query) use ($comisionId) {
                $query->where('comision_id', $comisionId);
            })
            ->where('materia_id', $materia->id)
            ->get();

        $totalAsistencias = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $ausentes = $asistencias->where('estado', 'ausente')->count();
        $justificados = $asistencias->where('estado', 'justificado')->count();

        $asistio = $presentes + $tardanzas + $justificados;
        $porcentaje = $totalAsistencias > 0 ? round(($asistio / $totalAsistencias) * 100, 2) : 0;

        return [
            'total_clases' => $totalAsistencias,
            'presentes' => $presentes,
            'tardanzas' => $tardanzas,
            'ausentes' => $ausentes,
            'justificados' => $justificados,
            'porcentaje_asistencia' => $porcentaje,
            'total_alumnos' => $asistencias->groupBy('inscripcion_comision_id')->count(),
        ];
    }
        
    public function justificarStore(Request $request, Comision $comision, InscripcionComision $inscripcion)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403);
        }

        // Verificar que la inscripción pertenece a la comisión
        if ($inscripcion->comision_id !== $comision->id) {
            abort(404);
        }

        $validated = $request->validate([
            'asistencias_ids' => 'required|array|min:1',
            'asistencias_ids.*' => 'required|exists:asistencias,id',
            'observaciones' => 'required|string|max:500',
            'materia_id' => 'nullable|exists:materias,id',
            'archivo' => 'nullable|file|max:5120', // 5MB máximo
        ], [
            'asistencias_ids.required' => 'Debes seleccionar al menos una fecha para justificar.',
            'asistencias_ids.min' => 'Debes seleccionar al menos una fecha para justificar.',
            'observaciones.required' => 'Debes agregar una observación (motivo de la justificación).',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar las asistencias seleccionadas
            Asistencia::whereIn('id', $validated['asistencias_ids'])
                ->where('inscripcion_comision_id', $inscripcion->id)
                ->where('estado', 'ausente')
                ->update([
                    'estado' => 'justificado',
                    'observaciones' => $validated['observaciones'],
                    'registrado_por' => $user->id,
                ]);

            DB::commit();

            // Redirigir según si hay materia específica
            if (isset($validated['materia_id']) && $validated['materia_id']) {
                $materia = Materia::find($validated['materia_id']);
                return redirect()
                    ->route('asistencias.materia.historial', [$comision, $materia])
                    ->with('success', 'Inasistencias justificadas exitosamente.');
            } else {
                return redirect()
                    ->route('asistencias.alumno.historial', [$comision, $inscripcion])
                    ->with('success', 'Inasistencias justificadas exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al justificar inasistencias: ' . $e->getMessage());
        }
    }
}