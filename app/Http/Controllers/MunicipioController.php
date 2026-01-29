<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MunicipioController extends Controller
{
    /**
     * Listado de municipios
     */
    public function index(Request $request): View
    {
        $query = Municipio::query()->withCount('comisiones');

        if ($request->filled('buscar')) {
            $termino = $request->input('buscar');
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('codigo', 'like', "%{$termino}%")
                    ->orWhere('direccion', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->input('activo') === '1');
        }

        $municipios = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('municipios.index', compact('municipios'));
    }

    /**
     * Formulario de creación
     */
    public function create(): View
    {
        return view('municipios.create');
    }

    /**
     * Guardar nuevo municipio
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:municipios,nombre',
            'codigo' => 'nullable|string|max:20|unique:municipios,codigo',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        Municipio::create($validated);

        return redirect()->route('municipios.index')
            ->with('success', 'Municipio creado exitosamente.');
    }

    /**
     * Mostrar detalle de municipio
     */
    public function show(Municipio $municipio): View
    {
        $municipio->load(['comisiones' => function ($q) {
            $q->orderBy('anio', 'desc')->orderBy('nombre');
        }]);

        return view('municipios.show', compact('municipio'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Municipio $municipio): View
    {
        return view('municipios.edit', compact('municipio'));
    }

    /**
     * Actualizar municipio
     */
    public function update(Request $request, Municipio $municipio): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:municipios,nombre,' . $municipio->id,
            'codigo' => 'nullable|string|max:20|unique:municipios,codigo,' . $municipio->id,
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $municipio->update($validated);

        return redirect()->route('municipios.index')
            ->with('success', 'Municipio actualizado exitosamente.');
    }

    /**
     * Eliminar municipio (soft delete)
     */
    public function destroy(Municipio $municipio): RedirectResponse
    {
        // Verificar que no tenga comisiones activas
        if ($municipio->comisiones()->where('estado', 'activa')->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el municipio porque tiene comisiones activas.');
        }

        $municipio->delete();

        return redirect()->route('municipios.index')
            ->with('success', 'Municipio eliminado exitosamente.');
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleActivo(Municipio $municipio): RedirectResponse
    {
        $municipio->update(['activo' => !$municipio->activo]);

        $estado = $municipio->activo ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Municipio {$estado} exitosamente.");
    }
}
