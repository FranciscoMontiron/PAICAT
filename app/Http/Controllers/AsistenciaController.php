<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    /**
     * Listado de comisiones para asistencias
     * Si es docente, solo ve sus comisiones asignadas
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Comision::with(['docente', 'inscripciones']);

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

        $comisiones = $query->orderBy('codigo')->paginate(12);

        // Estadísticas generales
        $stats = [
            'comisiones_total' => $user->hasRole('Docente') 
                ? Comision::where('docente_id', $user->id)->count()
                : Comision::count(),
            'comisiones_activas' => $user->hasRole('Docente')
                ? Comision::where('docente_id', $user->id)->where('estado', 'activa')->count()
                : Comision::where('estado', 'activa')->count(),
        ];

        return view('asistencias.index', compact('comisiones', 'stats'));
    }

    /**
     * Mostrar formulario para pasar asistencia de una comisión
     */
    public function create(Comision $comision)
    {
        $user = auth()->user();

        // Verificar que el docente solo pueda pasar asistencia de sus comisiones
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403, 'No tienes permiso para pasar asistencia en esta comisión.');
        }

        $fecha = request('fecha', now()->format('Y-m-d'));

        // Obtener alumnos inscritos con su asistencia del día (si existe)
        $inscripciones = $comision->inscripciones()
            ->with(['alumno', 'asistencias' => function ($query) use ($fecha) {
                $query->where('fecha', $fecha);
            }])
            ->where('estado', 'activo')
            ->orderBy('id')
            ->get();

        return view('asistencias.create', compact('comision', 'inscripciones', 'fecha'));
    }

    /**
     * Guardar asistencias del día
     */
    public function store(Request $request, Comision $comision)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403, 'No tienes permiso para registrar asistencia en esta comisión.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_id' => 'required|exists:inscripcion_comisiones,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['asistencias'] as $asistenciaData) {
                Asistencia::updateOrCreate(
                    [
                        'inscripcion_comision_id' => $asistenciaData['inscripcion_id'],
                        'fecha' => $validated['fecha'],
                    ],
                    [
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                        'registrado_por' => $user->id,
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('asistencias.historial', $comision)
                ->with('success', 'Asistencia registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Historial de asistencias de una comisión
     */
    public function historial(Comision $comision)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403);
        }

        // Obtener inscripciones con todas sus asistencias
        $inscripciones = $comision->inscripciones()
            ->with(['alumno', 'asistencias' => function ($query) {
                $query->orderBy('fecha', 'desc');
            }])
            ->where('estado', 'activo')
            ->get();

        // Calcular estadísticas por alumno
        $estadisticas = $inscripciones->map(function ($inscripcion) {
            $totalAsistencias = $inscripcion->asistencias->count();
            $presentes = $inscripcion->asistencias->where('estado', 'presente')->count();
            $tardanzas = $inscripcion->asistencias->where('estado', 'tardanza')->count();
            $ausentes = $inscripcion->asistencias->where('estado', 'ausente')->count();
            $justificados = $inscripcion->asistencias->where('estado', 'justificado')->count();

            // Calcular porcentaje (presente + tardanza + justificado = asistió)
            $asistio = $presentes + $tardanzas + $justificados;
            $porcentaje = $totalAsistencias > 0 ? round(($asistio / $totalAsistencias) * 100, 2) : 0;

            // Detectar riesgo de deserción (3+ ausencias consecutivas)
            $ultimasAsistencias = $inscripcion->asistencias->take(3);
            $ausentesConsecutivos = $ultimasAsistencias->every(fn($a) => $a->estado === 'ausente');

            return [
                'inscripcion' => $inscripcion,
                'total' => $totalAsistencias,
                'presentes' => $presentes,
                'tardanzas' => $tardanzas,
                'ausentes' => $ausentes,
                'justificados' => $justificados,
                'porcentaje' => $porcentaje,
                'en_riesgo' => $ausentesConsecutivos || $porcentaje < 75,
            ];
        });

        return view('asistencias.historial', compact('comision', 'estadisticas'));
    }

    /**
     * Editar asistencia de un día específico
     */
    public function edit(Comision $comision, $fecha)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->hasRole('Docente') && $comision->docente_id !== $user->id) {
            abort(403);
        }

        // Obtener alumnos inscritos con su asistencia del día
        $inscripciones = $comision->inscripciones()
            ->with(['alumno', 'asistencias' => function ($query) use ($fecha) {
                $query->where('fecha', $fecha);
            }])
            ->where('estado', 'activo')
            ->orderBy('id')
            ->get();

        return view('asistencias.edit', compact('comision', 'inscripciones', 'fecha'));
    }

    /**
     * Actualizar asistencias (reutiliza store)
     */
    public function update(Request $request, Comision $comision)
    {
        return $this->store($request, $comision);
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
            ->with(['alumno', 'asistencias' => function ($query) {
                $query->where('estado', 'ausente');
            }])
            ->where('estado', 'activo')
            ->get()
            ->filter(function ($inscripcion) {
                // Filtrar solo los que tienen ausencias
                return $inscripcion->asistencias->count() > 0;
            })
            ->sortBy('alumno.name');

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

    /**
     * Mostrar formulario para justificar inasistencias
     */
    public function justificarForm(Comision $comision, InscripcionComision $inscripcion)
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

        // Obtener solo las ausencias (sin justificar)
        $ausencias = $inscripcion->asistencias()
            ->where('estado', 'ausente')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('asistencias.justificar', compact('comision', 'inscripcion', 'ausencias'));
    }

    /**
     * Procesar justificación de inasistencias
     */
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

            return redirect()
                ->route('asistencias.alumno.historial', [$comision, $inscripcion])
                ->with('success', 'Inasistencias justificadas exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al justificar inasistencias: ' . $e->getMessage());
        }
    }
}
