<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluacionRequest;
use App\Http\Requests\UpdateEvaluacionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Evaluacion;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\Nota;
use App\Models\AcademicoDato;


class EvaluacionController extends Controller
{

    /**
     * Mostrar listado de comisiones para gestionar evaluaciones
     */
    public function index(Request $request): View
    {
        $query = Comision::query()
            ->with(['materias', 'evaluaciones'])
            ->orderBy('anio', 'desc')
            ->orderBy('nombre', 'asc');

        // Filtros
        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        $comisiones = $query->paginate(12);

        return view('evaluaciones.index', compact('comisiones'));
    }

    /**
     * Mostrar evaluaciones de una comisión específica
     */
    public function showComision(Comision $comision): View
    {
        $comision->load(['materias', 'municipio']);

        // Evaluaciones agrupadas por materia
        $evaluaciones = Evaluacion::where('comision_id', $comision->id)
            ->with(['materia', 'notas'])
            ->orderBy('materia_id')
            ->orderBy('fecha', 'asc')
            ->get()
            ->groupBy('materia_id');

        // Materias de la comisión (para crear nuevas evaluaciones)
        $materias = $comision->materias;

        // Alumnos inscriptos
        $alumnosCount = $comision->inscripciones()
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->count();

        return view('evaluaciones.comision', compact('comision', 'evaluaciones', 'materias', 'alumnosCount'));
    }

    /**
     * Mostrar formulario para crear nueva evaluacion
     */
    public function create(): View
    {
        $comisiones = Comision::orderBy('nombre', 'asc')->get();

        return view('evaluaciones.create', compact('comisiones'));
    }

    /**
     * Guardar nueva evaluacion
     */
    public function store(StoreEvaluacionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // Crear Evaluacion
        $evaluacion = Evaluacion::create([
            'nombre' => $data['name'],
            'descripcion' => $data['descripcion'] ?? null,
            'tipo' => $data['tipo'],
            'instancia' => $data['instancia'] ?? null,
            'fecha' => $data['fecha'],
            'peso_porcentual' => $data['porcentual'],
            'comision_id' => $data['comision'] ?: null,
            'materia_id' => $data['materia_id'],
            'anio' => $data['anio'],
            'cuenta_promedio' => $request->boolean('cuenta_promedio', true),
        ]);


        return redirect()
            ->route('evaluaciones.index')
            ->with('success', 'Evaluacion creada exitosamente.');
    }

    /**
     * Editar Evaluacion- Formulario
     */
    public function edit(Evaluacion $evaluacion): View
    {
        $comisiones = Comision::orderBy('nombre', 'asc')->get();

        return view('evaluaciones.edit', compact('evaluacion', 'comisiones'));
    }

    /**
     * Editar Evaluacion- Formulario
     */
    public function update(UpdateEvaluacionRequest $request, Evaluacion $evaluacion): RedirectResponse
    {
        $data = $request->validated();

        $evaluacion->update([
            'nombre' => $data['name'],
            'descripcion' => $data['descripcion'] ?? null,
            'tipo' => $data['tipo'],
            'instancia' => $data['instancia'] ?? null,
            'fecha' => $data['fecha'],
            'peso_porcentual' => $data['porcentual'],
            'comision_id' => $data['comision'] ?? null,
            'materia_id' => $data['materia_id'],
            'anio' => $data['anio'],
            'cuenta_promedio' => $request->boolean('cuenta_promedio', true),
        ]);

        return redirect()
            ->route('evaluaciones.index')
            ->with('success', 'Evalu actualizado exitosamente.');
    }


    /**
     * Eliminar Evaluacion (soft delete)
     */
    public function destroy(Evaluacion $evaluacion): RedirectResponse
    {
        $evaluacion->delete();

        return redirect()
            ->route('evaluaciones.index')
            ->with('success', 'Evaluacion eliminada exitosamente.');
    }


    /**
     * SECCIÓN DE NOTAS
     */

    /**
     * Listar notas de una comisión
     */
    /**
     * Listar notas de una comisión (Vista Matrix)
     */
    public function indexNota(Comision $comision): View
    {
        // Evaluaciones ordered by date
        $evaluaciones = Evaluacion::where('comision_id', $comision->id)
            ->orderBy('fecha', 'asc')
            ->get();

        // Inscripciones con Alumno y Notas cargadas
        // Filtramos por estado válido para evitar duplicados o basuras
        $inscripciones = InscripcionComision::where('comision_id', $comision->id)
            ->whereIn('estado', ['inscripto', 'confirmado', 'regular'])
            ->with(['inscripcion', 'academicoDato'])
            ->get()
            ->sortBy(function ($ic) {
                $p = $ic->inscripcion?->getPerson();
                return $p->apellido ?? $ic->academicoDato->apellido ?? '';
            });

        // Matrix de notas: [inscripcion_comision_id][evaluacion_id] -> nota
        // Usamos inscripcion_id como key más robusta
        $inscripcionesIds = $inscripciones->pluck('inscripcion_id');
        $evaluacionesIds = $evaluaciones->pluck('id');

        $notasRaw = Nota::whereIn('evaluacion_id', $evaluacionesIds)
            ->whereIn('inscripcion_id', $inscripcionesIds)
            ->get();

        $notasMap = [];
        foreach ($notasRaw as $nota) {
            $notasMap[$nota->inscripcion_id . '_' . $nota->evaluacion_id] = $nota;
        }

        // Log de últimas notas para la tabla inferior
        $notasLog = Nota::whereIn('evaluacion_id', $evaluacionesIds)
            ->with(['evaluacion', 'cargadoPor', 'inscripcionComision.inscripcion'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('notas.index', compact('comision', 'evaluaciones', 'inscripciones', 'notasMap', 'notasLog'));
    }

    /**
     * Guardar Sábana de Notas (Matrix)
     */
    public function storeMatrix(Request $request, Comision $comision): RedirectResponse
    {
        // Input: notas[inscripcion_id][evaluacion_id] = valor
        $matrix = $request->input('notas', []);

        \Illuminate\Support\Facades\DB::transaction(function () use ($matrix, $comision) {
            foreach ($matrix as $inscripcionId => $evals) {
                foreach ($evals as $evaluacionId => $valor) {

                    $nota = Nota::where('evaluacion_id', $evaluacionId)
                        ->where('inscripcion_id', $inscripcionId)
                        ->first();

                    // Si valor vacío, borrar
                    if ($valor === null || $valor === '') {
                        if ($nota) $nota->delete();
                        continue;
                    }

                    if (!is_numeric($valor) || $valor < 0 || $valor > 10) continue;

                    if ($nota) {
                        // Solo actualizar si cambió para evitar queries
                        if ((float)$nota->nota != (float)$valor) {
                            $nota->update(['nota' => $valor]);
                        }
                    } else {
                        // Crear
                        // Buscar IC id
                        $ic = InscripcionComision::where('comision_id', $comision->id)
                            ->where('inscripcion_id', $inscripcionId)
                            ->first();

                        Nota::create([
                            'evaluacion_id' => $evaluacionId,
                            'inscripcion_id' => $inscripcionId,
                            'inscripcion_comision_id' => $ic ? $ic->id : null,
                            'nota' => $valor,
                            'cargado_por' => auth()->id(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('notas.index', $comision)->with('success', 'Notas actualizadas correctamente.');
    }

    /**
     * Formulario para crear nota
     */
    public function createNota(Comision $comision): View
    {
        // Inscripciones activas de la comisión (inscripto o confirmado)
        $inscripciones = InscripcionComision::where('comision_id', $comision->id)
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->with(['academicoDato', 'inscripcion'])
            ->get();

        // Evaluaciones disponibles
        $evaluaciones = Evaluacion::where('comision_id', $comision->id)
            ->orWhereNull('comision_id')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('notas.create', compact('comision', 'evaluaciones', 'inscripciones'));
    }

    /**
     * Guardar nueva nota
     */
    public function storeNota(Request $request, Comision $comision): RedirectResponse
    {
        $validated = $request->validate([
            'evaluacion_id' => 'required|exists:evaluaciones,id',
            'inscripcion_comision_id' => 'required|exists:inscripcion_comisiones,id',
            'nota' => 'required|numeric|min:0|max:10',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Verificar que no exista ya la nota para esa evaluación e inscripción
        $existente = Nota::where('evaluacion_id', $validated['evaluacion_id'])
            ->where('inscripcion_comision_id', $validated['inscripcion_comision_id'])
            ->first();

        if ($existente) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya existe una nota para este alumno en esta evaluación.');
        }

        Nota::create([
            'evaluacion_id' => $validated['evaluacion_id'],
            'inscripcion_comision_id' => $validated['inscripcion_comision_id'],
            'nota' => $validated['nota'],
            'fecha_carga' => now(),
            'cargado_por' => auth()->id(),
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        return redirect()
            ->route('evaluaciones.notas.index', $comision)
            ->with('success', 'Nota registrada exitosamente.');
    }

    /**
     * Formulario para editar nota
     */
    public function editNota(Comision $comision, Nota $nota): View
    {
        // Cargar relaciones necesarias
        $nota->load(['inscripcionComision.inscripcion', 'inscripcionComision.academicoDato', 'evaluacion']);

        $evaluaciones = Evaluacion::where('comision_id', $comision->id)
            ->orWhereNull('comision_id')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('notas.edit', compact('comision', 'nota', 'evaluaciones'));
    }

    /**
     * Actualizar nota
     */
    public function updateNota(Request $request, Comision $comision, Nota $nota): RedirectResponse
    {
        $validated = $request->validate([
            'nota' => 'required|numeric|min:0|max:10',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $nota->update([
            'nota' => $validated['nota'],
            'observaciones' => $validated['observaciones'] ?? null,
            'cargado_por' => auth()->id(),
        ]);

        return redirect()
            ->route('evaluaciones.notas.index', $comision)
            ->with('success', 'Nota actualizada exitosamente.');
    }

    /**
     * Eliminar nota
     */
    public function destroyNota(Comision $comision, Nota $nota): RedirectResponse
    {
        $nota->delete();

        return redirect()
            ->route('evaluaciones.notas.index', $comision)
            ->with('success', 'Nota eliminada exitosamente.');
    }

    /**
     * Ver historial de notas de un alumno en una comisión
     */
    public function historialAlumno(Comision $comision, InscripcionComision $inscripcion): View
    {
        // Verificar que la inscripción pertenece a la comisión
        if ($inscripcion->comision_id !== $comision->id) {
            abort(404, 'El alumno no pertenece a esta comisión');
        }

        // Obtener todas las notas del alumno con evaluaciones
        $notas = Nota::where('inscripcion_comision_id', $inscripcion->id)
            ->with(['evaluacion', 'cargadoPor'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas
        $promedioPonderado = $inscripcion->calcularPromedioPonderado();
        $promedioSimple = $inscripcion->calcularPromedioSimple();
        $condicion = $inscripcion->determinarCondicion();
        $porcentajeAsistencia = $inscripcion->calcularPorcentajeAsistencia();

        // Evaluaciones disponibles (para ver cuáles faltan)
        $evaluacionesComision = Evaluacion::where('comision_id', $comision->id)
            ->orWhereNull('comision_id')
            ->get();

        $evaluacionesRendidas = $notas->pluck('evaluacion_id')->toArray();
        $evaluacionesFaltantes = $evaluacionesComision->whereNotIn('id', $evaluacionesRendidas);

        return view('notas.historial-alumno', compact(
            'comision',
            'inscripcion',
            'notas',
            'promedioPonderado',
            'promedioSimple',
            'condicion',
            'porcentajeAsistencia',
            'evaluacionesFaltantes'
        ));
    }

    /**
     * Formulario para registrar recuperatorio
     */
    public function createRecuperatorio(Comision $comision): View
    {
        // Inscripciones que pueden rendir recuperatorio (promedio < 6)
        $inscripciones = InscripcionComision::where('comision_id', $comision->id)
            ->where('estado', 'confirmado')
            ->with(['academicoDato', 'notas.evaluacion'])
            ->get()
            ->filter(function ($inscripcion) {
                return $inscripcion->puedeRendirRecuperatorio();
            });

        // Evaluaciones tipo parcial para recuperar
        $evaluacionesRecuperables = Evaluacion::where(function ($q) use ($comision) {
            $q->where('comision_id', $comision->id)->orWhereNull('comision_id');
        })
            ->whereIn('tipo', ['parcial', 'examen_final'])
            ->orderBy('fecha', 'desc')
            ->get();

        return view('notas.recuperatorio', compact('comision', 'inscripciones', 'evaluacionesRecuperables'));
    }

    /**
     * Guardar nota de recuperatorio
     */
    public function storeRecuperatorio(Request $request, Comision $comision): RedirectResponse
    {
        $validated = $request->validate([
            'evaluacion_original_id' => 'required|exists:evaluaciones,id',
            'inscripcion_comision_id' => 'required|exists:inscripcion_comisiones,id',
            'nota' => 'required|numeric|min:0|max:10',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Buscar la evaluación original
        $evaluacionOriginal = Evaluacion::findOrFail($validated['evaluacion_original_id']);

        // Crear evaluación de recuperatorio si no existe
        $evaluacionRecuperatorio = Evaluacion::firstOrCreate(
            [
                'nombre' => 'Recuperatorio - ' . $evaluacionOriginal->nombre,
                'comision_id' => $comision->id,
                'tipo' => 'recuperatorio',
            ],
            [
                'descripcion' => 'Recuperatorio de ' . $evaluacionOriginal->nombre,
                'fecha' => now(),
                'peso_porcentual' => $evaluacionOriginal->peso_porcentual,
                'anio' => $evaluacionOriginal->anio ?? date('Y'),
            ]
        );

        // Verificar nota existente del recuperatorio
        $notaExistente = Nota::where('evaluacion_id', $evaluacionRecuperatorio->id)
            ->where('inscripcion_comision_id', $validated['inscripcion_comision_id'])
            ->first();

        if ($notaExistente) {
            // Actualizar nota existente
            $notaExistente->update([
                'nota' => $validated['nota'],
                'observaciones' => $validated['observaciones'] ?? 'Recuperatorio actualizado',
                'cargado_por' => auth()->id(),
            ]);
        } else {
            // Crear nueva nota de recuperatorio
            Nota::create([
                'evaluacion_id' => $evaluacionRecuperatorio->id,
                'inscripcion_comision_id' => $validated['inscripcion_comision_id'],
                'nota' => $validated['nota'],
                'fecha_carga' => now(),
                'cargado_por' => auth()->id(),
                'observaciones' => $validated['observaciones'] ?? 'Nota de recuperatorio',
            ]);
        }

        // Si la nota del recuperatorio es mejor que la original, marcar la original como reemplazada
        $notaOriginal = Nota::where('evaluacion_id', $validated['evaluacion_original_id'])
            ->where('inscripcion_comision_id', $validated['inscripcion_comision_id'])
            ->first();

        if ($notaOriginal && $validated['nota'] > $notaOriginal->nota) {
            $notaOriginal->update([
                'observaciones' => ($notaOriginal->observaciones ?? '') . ' [Reemplazada por recuperatorio: ' . $validated['nota'] . ']',
            ]);
        }

        return redirect()
            ->route('evaluaciones.notas.index', $comision)
            ->with('success', 'Nota de recuperatorio registrada exitosamente.');
    }

    /**
     * Exportar acta de notas a Excel
     */
    public function exportarActa(Comision $comision)
    {
        // Obtener todas las inscripciones con sus notas
        $inscripciones = InscripcionComision::where('comision_id', $comision->id)
            ->with(['inscripcion', 'academicoDato', 'notas.evaluacion'])
            ->get();

        // Obtener evaluaciones de la comisión
        $evaluaciones = Evaluacion::where('comision_id', $comision->id)
            ->orWhereNull('comision_id')
            ->orderBy('fecha')
            ->get();

        // Preparar datos para el Excel
        $datos = [];

        // Encabezados
        $encabezados = ['#', 'Alumno', 'DNI'];
        foreach ($evaluaciones as $eval) {
            $encabezados[] = $eval->nombre . ' (' . $eval->peso_porcentual . '%)';
        }
        $encabezados[] = 'Promedio';
        $encabezados[] = 'Asistencia';
        $encabezados[] = 'Condición';

        $datos[] = $encabezados;

        // Datos de alumnos
        $contador = 1;
        foreach ($inscripciones as $inscripcionComision) {
            // Obtener datos del alumno desde alumnos_utn o academicoDato
            $person = $inscripcionComision->inscripcion?->getPerson();
            $academicoDato = $inscripcionComision->academicoDato;

            $nombreCompleto = 'N/A';
            $documento = 'N/A';

            if ($person) {
                $nombreCompleto = ($person->apellido ?? '') . ', ' . ($person->nombre ?? '');
                $documento = $person->documento ?? 'N/A';
            } elseif ($academicoDato) {
                $nombreCompleto = ($academicoDato->apellido ?? '') . ', ' . ($academicoDato->nombre ?? '');
                $documento = $academicoDato->documento ?? $academicoDato->dni ?? 'N/A';
            }

            $fila = [
                $contador++,
                $nombreCompleto,
                $documento,
            ];

            // Notas por evaluación
            foreach ($evaluaciones as $eval) {
                $nota = $inscripcionComision->notas->where('evaluacion_id', $eval->id)->first();
                $fila[] = $nota ? number_format($nota->nota, 2) : '-';
            }

            // Promedio
            $promedio = method_exists($inscripcionComision, 'calcularPromedioPonderado')
                ? $inscripcionComision->calcularPromedioPonderado()
                : null;
            $fila[] = $promedio !== null ? number_format($promedio, 2) : '-';

            // Asistencia
            $porcentajeAsistencia = method_exists($inscripcionComision, 'calcularPorcentajeAsistencia')
                ? $inscripcionComision->calcularPorcentajeAsistencia()
                : 0;
            $fila[] = $porcentajeAsistencia . '%';

            // Condición
            $condicion = method_exists($inscripcionComision, 'determinarCondicion')
                ? $inscripcionComision->determinarCondicion()
                : ['condicion' => 'N/A'];
            $fila[] = $condicion['condicion'] ?? 'N/A';

            $datos[] = $fila;
        }

        // Generar archivo Excel usando Maatwebsite
        $filename = 'acta_notas_' . $comision->codigo . '_' . date('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ActaNotasExport($datos, $comision),
            $filename
        );
    }
    /**
     * Obtener materias de una comisión (AJAX)
     */
    public function getMaterias(Comision $comision)
    {
        // Devolver id y nombre de las materias asociadas a la comisión
        return response()->json($comision->materias()->orderBy('nombre')->get(['materias.id', 'materias.nombre']));
    }

    /**
     * Mostrar vista de carga masiva de notas
     */
    public function cargaMasiva(Evaluacion $evaluacion): View
    {
        $comision = $evaluacion->comision;

        // Obtener inscripciones de la comisión (alumnos)
        // Se asume relación: Comision -> hasMany InscripcionComision -> belongsTo Inscripcion -> belongsTo Alumno (User)
        $inscripciones = $comision->inscripciones()
            ->with(['inscripcion'])
            ->get()
            ->sortBy(function ($ic) {
                $p = $ic->inscripcion?->getPerson();
                return $p->apellido ?? '';
            });

        // Obtener notas ya cargadas para esta evaluación, indexadas por inscripcion_id
        $notas = $evaluacion->notas->keyBy('inscripcion_id');

        return view('evaluaciones.carga-masiva', compact('evaluacion', 'comision', 'inscripciones', 'notas'));
    }

    /**
     * Guardar notas masivas
     */
    public function storeCargaMasiva(Request $request, Evaluacion $evaluacion): RedirectResponse
    {
        $notasData = $request->input('notas', []);

        \Illuminate\Support\Facades\DB::transaction(function () use ($notasData, $evaluacion) {
            foreach ($notasData as $inscripcionId => $valor) {

                // Buscar nota existente
                $nota = Nota::where('evaluacion_id', $evaluacion->id)
                    ->where('inscripcion_id', $inscripcionId)
                    ->first();

                // Si el valor está vacío, eliminar nota si existe (o ignorar)
                if ($valor === null || $valor === '') {
                    if ($nota) {
                        $nota->delete();
                    }
                    continue;
                }

                // Validar que sea numérico y rango 1-10 (o 0-10)
                if (!is_numeric($valor) || $valor < 0 || $valor > 10) {
                    continue;
                }

                if ($nota) {
                    $nota->update(['nota' => $valor]);
                } else {
                    // Buscar inscripcion_comision_id para compatibilidad
                    $ic = InscripcionComision::where('comision_id', $evaluacion->comision_id)
                        ->where('inscripcion_id', $inscripcionId)
                        ->first();

                    Nota::create([
                        'evaluacion_id' => $evaluacion->id,
                        'inscripcion_id' => $inscripcionId,
                        'inscripcion_comision_id' => $ic ? $ic->id : null,
                        'nota' => $valor,
                        'cargado_por' => auth()->id(),
                    ]);
                }
            }
        });

        return redirect()->route('evaluaciones.index')->with('success', 'Notas cargadas exitosamente.');
    }
}
