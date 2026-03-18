<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Services\ConfiguracionService;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Mostrar lista de materias
     */
    public function index(Request $request)
    {
        $query = Materia::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('activa')) {
            $query->where('activa', $request->activa == '1');
        }

        $materias = $query->orderBy('codigo')->paginate(20);

        $stats = [
            'total' => Materia::count(),
            'activas' => Materia::where('activa', true)->count(),
            'nivelacion' => Materia::where('es_nivelacion', true)->count(),
        ];

        return view('materias.index', compact('materias', 'stats'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        return view('materias.create');
    }

    /**
     * Guardar nueva materia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:materias,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:' . implode(',', array_keys(Materia::getTipos())),
            'carga_horaria' => 'nullable|integer|min:1',
            'es_nivelacion' => 'boolean',
            'activa' => 'boolean',
        ]);

        $validated['es_nivelacion'] = $request->boolean('es_nivelacion');
        $validated['activa'] = $request->boolean('activa', true);

        Materia::create($validated);

        return redirect()->route('materias.index')
            ->with('success', 'Materia creada exitosamente.');
    }

    /**
     * Mostrar detalle de materia
     */
    public function show(Materia $materia)
    {
        $materia->load('comisiones');
        return view('materias.show', compact('materia'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Materia $materia)
    {
        return view('materias.edit', compact('materia'));
    }

    /**
     * Actualizar materia
     */
    public function update(Request $request, Materia $materia)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:materias,codigo,' . $materia->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:' . implode(',', array_keys(Materia::getTipos())),
            'carga_horaria' => 'nullable|integer|min:1',
            'es_nivelacion' => 'boolean',
            'activa' => 'boolean',
        ]);

        $validated['es_nivelacion'] = $request->boolean('es_nivelacion');
        $validated['activa'] = $request->boolean('activa');

        $materia->update($validated);

        return redirect()->route('materias.index')
            ->with('success', 'Materia actualizada exitosamente.');
    }

    /**
     * Eliminar materia
     */
    public function destroy(Materia $materia)
    {
        // Verificar si tiene comisiones
        if ($materia->comisiones()->count() > 0) {
            return redirect()->route('materias.index')
                ->with('error', 'No se puede eliminar la materia porque tiene comisiones asociadas.');
        }

        $materia->delete();

        return redirect()->route('materias.index')
            ->with('success', 'Materia eliminada exitosamente.');
    }
}
