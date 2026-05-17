<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfraestructuraController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'municipios');

        // Municipios
        $queryMunicipios = Municipio::query()
            ->withCount(['comisiones', 'aulas']);

        if ($request->filled('buscar_municipio')) {
            $termino = $request->input('buscar_municipio');
            $queryMunicipios->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('codigo', 'like', "%{$termino}%")
                    ->orWhere('direccion', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('estado_municipio')) {
            $queryMunicipios->where('activo', $request->input('estado_municipio') === '1');
        }

        $municipios = $queryMunicipios->orderBy('nombre')->get();

        // Aulas
        $queryAulas = Aula::with('municipio')->withCount('comisiones');

        if ($request->filled('buscar_aula')) {
            $termino = $request->input('buscar_aula');
            $queryAulas->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('codigo', 'like', "%{$termino}%")
                    ->orWhere('ubicacion', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('municipio_aula')) {
            $queryAulas->where('municipio_id', $request->input('municipio_aula'));
        }

        if ($request->filled('estado_aula')) {
            $queryAulas->where('activa', $request->input('estado_aula') === '1');
        }

        $aulas = $queryAulas->orderBy('municipio_id')->orderBy('nombre')->get();

        // Stats
        $stats = [
            'municipios_activos' => Municipio::where('activo', true)->count(),
            'municipios_total' => Municipio::count(),
            'aulas_activas' => Aula::where('activa', true)->count(),
            'aulas_total' => Aula::count(),
            'capacidad_total' => Aula::where('activa', true)->sum('capacidad'),
        ];

        $municipiosActivos = Municipio::activos()->orderBy('nombre')->get();

        return view('infraestructura.index', compact(
            'municipios', 'aulas', 'stats', 'tab', 'municipiosActivos'
        ));
    }
}
