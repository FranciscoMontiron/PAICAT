<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Cursada;
use App\Models\InscripcionComision;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CursadaController extends Controller
{
    /**
     * Listado de cursadas por comisión
     */
    public function index(Request $request, Comision $comision): View
    {
        $query = Cursada::with(['inscripcion', 'comision'])
            ->where('comision_id', $comision->id);

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // Filtrar por año
        if ($request->filled('anio')) {
            $query->where('anio', $request->input('anio'));
        }

        // Buscar por nombre/apellido
        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->whereHas('inscripcion', function ($q) use ($termino) {
                $q->whereHas('person', function ($q2) use ($termino) {
                    $q2->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('apellido', 'like', "%{$termino}%")
                        ->orWhere('documento', 'like', "%{$termino}%");
                });
            });
        }

        $cursadas = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('cursadas.index', compact('comision', 'cursadas'));
    }

    /**
     * Ver detalle de una cursada
     */
    public function show(Comision $comision, Cursada $cursada): View
    {
        $cursada->load(['inscripcion', 'comision', 'notas.evaluacion', 'asistencias', 'reincorporaciones']);

        return view('cursadas.show', compact('comision', 'cursada'));
    }

    /**
     * Formulario para editar cursada
     */
    public function edit(Comision $comision, Cursada $cursada): View
    {
        return view('cursadas.edit', compact('comision', 'cursada'));
    }

    /**
     * Actualizar cursada
     */
    public function update(Request $request, Comision $comision, Cursada $cursada): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Cursada::ESTADOS)),
            'modalidad' => 'nullable|string|max:50',
            'nota_final' => 'nullable|numeric|min:0|max:10',
            'resultado' => 'nullable|string|max:100',
            'es_recursante' => 'boolean',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $validated['es_recursante'] = $request->boolean('es_recursante');

        // Registrar cambio de estado
        if ($cursada->estado !== $validated['estado']) {
            $cursada->cambiarEstado(
                $validated['estado'],
                auth()->id(),
                $request->input('motivo_cambio')
            );
        } else {
            $cursada->update($validated);
        }

        return redirect()->route('cursadas.show', [$comision, $cursada])
            ->with('success', 'Cursada actualizada exitosamente.');
    }

    /**
     * Cambiar estado de cursada (AJAX o normal)
     */
    public function cambiarEstado(Request $request, Comision $comision, Cursada $cursada): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Cursada::ESTADOS)),
            'observacion' => 'nullable|string|max:500',
        ]);

        $cursada->cambiarEstado($validated['estado'], auth()->id(), $validated['observacion'] ?? null);

        return redirect()->back()
            ->with('success', 'Estado actualizado a: ' . Cursada::ESTADOS[$validated['estado']]);
    }

    /**
     * Recalcular nota final de cursada
     */
    public function recalcularNota(Comision $comision, Cursada $cursada): RedirectResponse
    {
        $notaFinal = $cursada->calcularNotaFinal();

        if ($notaFinal === null) {
            return redirect()->back()
                ->with('warning', 'No hay notas cargadas para calcular el promedio.');
        }

        return redirect()->back()
            ->with('success', "Nota final recalculada: {$notaFinal}");
    }

    /**
     * Listado global de cursadas (todas las comisiones)
     */
    public function indexGlobal(Request $request): View
    {
        $query = Cursada::with(['inscripcion', 'comision']);

        // Filtros
        if ($request->filled('comision_id')) {
            $query->where('comision_id', $request->input('comision_id'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('anio')) {
            $query->where('anio', $request->input('anio'));
        }

        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->whereHas('inscripcion', function ($q) use ($termino) {
                $q->whereHas('person', function ($q2) use ($termino) {
                    $q2->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('apellido', 'like', "%{$termino}%")
                        ->orWhere('documento', 'like', "%{$termino}%");
                });
            });
        }

        $cursadas = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        $comisiones = Comision::where('estado', 'activa')->orderBy('nombre')->get();

        // Estadísticas
        $estadisticas = [
            'total' => Cursada::count(),
            'cursando' => Cursada::where('estado', Cursada::ESTADO_CURSANDO)->count(),
            'aprobados' => Cursada::where('estado', Cursada::ESTADO_APROBADO)->count(),
            'desaprobados' => Cursada::where('estado', Cursada::ESTADO_DESAPROBADO)->count(),
            'libres' => Cursada::where('estado', Cursada::ESTADO_LIBRE)->count(),
        ];

        return view('cursadas.index-global', compact('cursadas', 'comisiones', 'estadisticas'));
    }

    /**
     * Crear cursadas para todos los alumnos de una comisión que no las tengan
     */
    public function sincronizarConComision(Comision $comision): RedirectResponse
    {
        $anioActual = date('Y');
        $creadas = 0;

        // Obtener inscripciones de la comisión
        $inscripciones = InscripcionComision::where('comision_id', $comision->id)
            ->where('estado', 'inscripto')
            ->get();

        foreach ($inscripciones as $inscripcionComision) {
            // Verificar si ya tiene cursada
            $existeCursada = Cursada::where('inscripcion_id', $inscripcionComision->inscripcion_id)
                ->where('comision_id', $comision->id)
                ->where('anio', $anioActual)
                ->exists();

            if (!$existeCursada && $inscripcionComision->inscripcion_id) {
                // Verificar si es recursante
                $esRecursante = Cursada::where('inscripcion_id', $inscripcionComision->inscripcion_id)
                    ->where('anio', '<', $anioActual)
                    ->whereIn('estado', [Cursada::ESTADO_DESAPROBADO, Cursada::ESTADO_LIBRE])
                    ->exists();

                Cursada::create([
                    'inscripcion_id' => $inscripcionComision->inscripcion_id,
                    'comision_id' => $comision->id,
                    'anio' => $anioActual,
                    'estado' => Cursada::ESTADO_CURSANDO,
                    'modalidad' => $comision->modalidad,
                    'es_recursante' => $esRecursante,
                    'fecha_inicio' => now(),
                ]);

                $creadas++;
            }
        }

        if ($creadas > 0) {
            return redirect()->back()
                ->with('success', "Se crearon {$creadas} cursadas nuevas.");
        }

        return redirect()->back()
            ->with('info', 'Todos los alumnos ya tienen cursada registrada.');
    }
}
