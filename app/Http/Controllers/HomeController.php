<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Comision;
use App\Models\Asistencia;
use App\Models\Cursada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cachear estadísticas por 5 minutos para mejor rendimiento
        $stats = Cache::remember('dashboard_stats', 300, function () {
            $data = [
                'inscripciones' => 0,
                'pendientes' => 0,
                'comisiones_activas' => 0,
                'comisiones_total' => 0,
                'alumnos_cursando' => 0,
                'asistencias_hoy' => 0,
                'usuarios' => 0,
                'municipios' => 0,
                'aulas' => 0,
            ];

            // Inscripciones
            try {
                $data['inscripciones'] = Inscripcion::count();
                $data['pendientes'] = Inscripcion::where('estado', 'pendiente')->count();
            } catch (\Exception $e) {}

            // Comisiones
            try {
                $data['comisiones_total'] = Comision::count();
                $data['comisiones_activas'] = Comision::where('estado', 'activa')->count();
            } catch (\Exception $e) {}

            // Cursadas/Alumnos cursando
            try {
                $data['alumnos_cursando'] = Cursada::where('estado', Cursada::ESTADO_CURSANDO)->count();
            } catch (\Exception $e) {}

            // Asistencias de hoy
            try {
                $data['asistencias_hoy'] = Asistencia::whereDate('fecha', today())->count();
            } catch (\Exception $e) {}

            // Usuarios
            try {
                $data['usuarios'] = DB::table('users')->count();
            } catch (\Exception $e) {}

            // Municipios y Aulas
            try {
                $data['municipios'] = DB::table('municipios')->whereNull('deleted_at')->where('activo', true)->count();
                $data['aulas'] = DB::table('aulas')->whereNull('deleted_at')->where('activa', true)->count();
            } catch (\Exception $e) {}

            return $data;
        });

        // Alertas importantes (no cachear, siempre frescas)
        $alertas = $this->getAlertas();

        return view('home', compact('stats', 'alertas'));
    }

    private function getAlertas(): array
    {
        $alertas = [];

        // Inscripciones pendientes de validación
        try {
            $pendientes = Inscripcion::where('estado', 'pendiente')->count();
            if ($pendientes > 0) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'icono' => 'clock',
                    'mensaje' => "{$pendientes} inscripciones pendientes de validación",
                    'url' => route('inscripciones.index', ['estado' => 'pendiente']),
                ];
            }
        } catch (\Exception $e) {}

        // Solicitudes de cambio pendientes
        try {
            $solicitudes = DB::table('solicitud_cambios')->where('estado', 'pendiente')->count();
            if ($solicitudes > 0) {
                $alertas[] = [
                    'tipo' => 'info',
                    'icono' => 'refresh',
                    'mensaje' => "{$solicitudes} solicitudes de cambio de comisión pendientes",
                    'url' => route('solicitudes.index'),
                ];
            }
        } catch (\Exception $e) {}

        // Comisiones sin docentes asignados
        try {
            $sinDocentes = Comision::where('estado', 'activa')
                ->whereDoesntHave('docentes')
                ->count();
            if ($sinDocentes > 0) {
                $alertas[] = [
                    'tipo' => 'danger',
                    'icono' => 'exclamation',
                    'mensaje' => "{$sinDocentes} comisiones activas sin docentes asignados",
                    'url' => route('comisiones.index'),
                ];
            }
        } catch (\Exception $e) {}

        return $alertas;
    }
}
