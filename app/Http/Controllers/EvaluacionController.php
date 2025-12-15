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
     * Mostrar evaluaciones
     */
        public function index(): View
        {
            $evaluaciones = Evaluacion::orderBy('created_at', 'desc')
                ->paginate(15);

            $comisiones = Comision::orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('evaluaciones.index',compact('evaluaciones','comisiones'));
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
                'fecha' => $data['fecha'],
                'peso_porcentual' => $data['porcentual'] ,
                'comision_id' => $data['comision'] ?: null,
                'anio' => $data['anio'],
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
        public function update(UpdateEvaluacionRequest $request,Evaluacion $evaluacion): RedirectResponse
        {
             $data = $request->validated();

            $evaluacion->update([
                'nombre' => $data['name'],
                'descripcion' => $data['descripcion'] ?? null,
                'tipo' => $data['tipo'],
                'fecha' => $data['fecha'],
                'peso_porcentual' => $data['porcentual'] ,
                'comision_id' => $data['comision'] ?? null,
                #'comision_id' => $data['comision'],
                'anio' => $data['anio'],
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
    public function indexNota(Comision $comision): View
    {
        // Traer inscripciones de la comisión
        $inscripcionesIds = InscripcionComision::where('comision_id', $comision->id)
            ->pluck('id');

        // Notas de esa comisión con relaciones
        $notas = Nota::whereIn('inscripcion_comision_id', $inscripcionesIds)
            ->with(['evaluacion', 'inscripcionComision.academicoDato', 'inscripcionComision.inscripcion', 'cargadoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Evaluaciones disponibles para esta comisión
        $evaluaciones = Evaluacion::where('comision_id', $comision->id)
            ->orWhereNull('comision_id')
            ->orderBy('fecha', 'desc')
            ->get();
            
        return view('notas.index', compact('notas', 'comision', 'evaluaciones'));
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
            ->with(['academicoDato', 'notas.evaluacion'])
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
        foreach ($inscripciones as $inscripcion) {
            $fila = [
                $contador++,
                $inscripcion->academicoDato->apellido . ', ' . $inscripcion->academicoDato->nombre ?? 'N/A',
                $inscripcion->academicoDato->documento ?? 'N/A',
            ];

            // Notas por evaluación
            foreach ($evaluaciones as $eval) {
                $nota = $inscripcion->notas->where('evaluacion_id', $eval->id)->first();
                $fila[] = $nota ? number_format($nota->nota, 2) : '-';
            }

            // Promedio
            $promedio = $inscripcion->calcularPromedioPonderado();
            $fila[] = $promedio !== null ? number_format($promedio, 2) : '-';

            // Asistencia
            $fila[] = $inscripcion->calcularPorcentajeAsistencia() . '%';

            // Condición
            $condicion = $inscripcion->determinarCondicion();
            $fila[] = $condicion['condicion'];

            $datos[] = $fila;
        }

        // Generar archivo Excel usando Maatwebsite
        $filename = 'acta_notas_' . $comision->codigo . '_' . date('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ActaNotasExport($datos, $comision),
            $filename
        );
    }
}
