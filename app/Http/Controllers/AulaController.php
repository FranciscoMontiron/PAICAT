<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AulaController extends Controller
{
    /**
     * Listado de aulas
     */
    public function index(Request $request): View
    {
        $query = Aula::with('municipio')->withCount('comisiones');

        // Filtrar por búsqueda
        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('codigo', 'like', "%{$termino}%")
                  ->orWhere('ubicacion', 'like', "%{$termino}%");
            });
        }

        // Filtrar por municipio
        if ($request->filled('municipio_id')) {
            $query->where('municipio_id', $request->input('municipio_id'));
        }

        // Filtrar por estado
        if ($request->filled('activa')) {
            $query->where('activa', $request->boolean('activa'));
        }

        $aulas = $query->orderBy('municipio_id')->orderBy('nombre')->paginate(20)->withQueryString();
        $municipios = Municipio::activos()->orderBy('nombre')->get();

        return view('aulas.index', compact('aulas', 'municipios'));
    }

    /**
     * Formulario de creación
     */
    public function create(): View
    {
        $municipios = Municipio::activos()->orderBy('nombre')->get();
        return view('aulas.create', compact('municipios'));
    }

    /**
     * Guardar nueva aula
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20|unique:aulas,codigo',
            'capacidad' => 'nullable|integer|min:1|max:500',
            'municipio_id' => 'required|exists:municipios,id',
            'ubicacion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $validated['activa'] = $request->boolean('activa', true);

        Aula::create($validated);

        return redirect()->route('aulas.index')
            ->with('success', 'Aula creada exitosamente.');
    }

    /**
     * Ver detalle de aula
     */
    public function show(Aula $aula): View
    {
        $aula->load(['municipio', 'comisiones' => function ($q) {
            $q->with('docentes')->orderBy('estado')->orderBy('nombre');
        }]);

        return view('aulas.show', compact('aula'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Aula $aula): View
    {
        $municipios = Municipio::activos()->orderBy('nombre')->get();
        return view('aulas.edit', compact('aula', 'municipios'));
    }

    /**
     * Actualizar aula
     */
    public function update(Request $request, Aula $aula): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20|unique:aulas,codigo,' . $aula->id,
            'capacidad' => 'nullable|integer|min:1|max:500',
            'municipio_id' => 'required|exists:municipios,id',
            'ubicacion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $validated['activa'] = $request->boolean('activa', true);

        $aula->update($validated);

        return redirect()->route('aulas.index')
            ->with('success', 'Aula actualizada exitosamente.');
    }

    /**
     * Toggle estado activa
     */
    public function toggleActiva(Aula $aula): RedirectResponse
    {
        $aula->update(['activa' => !$aula->activa]);

        $estado = $aula->activa ? 'activada' : 'desactivada';
        return redirect()->back()
            ->with('success', "Aula {$estado} exitosamente.");
    }

    /**
     * Eliminar aula
     */
    public function destroy(Aula $aula): RedirectResponse
    {
        // Verificar que no tenga comisiones activas
        if ($aula->comisiones()->where('estado', 'activa')->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el aula porque tiene comisiones activas.');
        }

        $aula->delete();

        return redirect()->route('aulas.index')
            ->with('success', 'Aula eliminada exitosamente.');
    }

    /**
     * API: Obtener aulas por municipio (para AJAX)
     */
    public function porMunicipio(Municipio $municipio)
    {
        $aulas = Aula::where('municipio_id', $municipio->id)
            ->where('activa', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo', 'capacidad']);

        return response()->json($aulas);
    }
}
