<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cachear estadísticas por 5 minutos para mejor rendimiento
        $stats = Cache::remember('dashboard_stats', 300, function () {
            $inscripcionesCount = 0;
            $pendientesCount = 0;
            $comisionesCount = 0;

            // Contar inscripciones si existe la tabla
            try {
                $inscripcionesCount = Inscripcion::count();
                $pendientesCount = Inscripcion::where('estado', 'pendiente')->count();
            } catch (\Exception $e) {
                // Tabla aún no existe
            }

            // Contar comisiones si existe la tabla
            try {
                $comisionesCount = DB::table('inscripcion_comisiones')->count();
            } catch (\Exception $e) {
                // Tabla aún no existe
            }

            return [
                'users' => DB::table('users')->count(),
                'inscripciones' => $inscripcionesCount,
                'comisiones' => $comisionesCount,
                'pendientes' => $pendientesCount,
            ];
        });

        return view('home', compact('stats'));
    }
}
