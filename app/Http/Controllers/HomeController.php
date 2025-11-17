<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cachear estadísticas por 5 minutos para mejor rendimiento
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'users' => DB::table('users')->count(),
                'inscripciones' => 0, // Tabla academico_datos aún no existe
                'comisiones' => 0, // Tabla comisiones aún no existe
            ];
        });

        return view('home', compact('stats'));
    }
}
