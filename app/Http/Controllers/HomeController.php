<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Comision;
use App\Models\Asistencia;
use App\Models\Cursada;
use App\Models\SolicitudCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isDocente = $user->hasRole('docente') && !$user->hasAnyRole(['admin', 'coordinador']);

        if ($isDocente) {
            return $this->dashboardDocente($user);
        }

        return $this->dashboardAdmin($user);
    }

    /**
     * Dashboard para Admin y Coordinador
     */
    private function dashboardAdmin($user)
    {
        $stats = Cache::remember('dashboard_stats_admin', 300, function () {
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
                'solicitudes_pendientes' => 0,
            ];

            try {
                $data['inscripciones'] = Inscripcion::count();
                $data['pendientes'] = Inscripcion::where('estado', 'pendiente')->count();
            } catch (\Exception $e) {}

            try {
                $data['comisiones_total'] = Comision::count();
                $data['comisiones_activas'] = Comision::where('estado', 'activa')->count();
            } catch (\Exception $e) {}

            try {
                $data['alumnos_cursando'] = Cursada::where('estado', Cursada::ESTADO_CURSANDO)->count();
            } catch (\Exception $e) {}

            try {
                $data['asistencias_hoy'] = Asistencia::whereDate('fecha', today())->count();
            } catch (\Exception $e) {}

            try {
                $data['usuarios'] = DB::table('users')->count();
            } catch (\Exception $e) {}

            try {
                $data['municipios'] = DB::table('municipios')->whereNull('deleted_at')->where('activo', true)->count();
                $data['aulas'] = DB::table('aulas')->whereNull('deleted_at')->where('activa', true)->count();
            } catch (\Exception $e) {}

            try {
                $data['solicitudes_pendientes'] = SolicitudCambio::where('estado', 'pendiente')->count();
            } catch (\Exception $e) {}

            return $data;
        });

        $alertas = $this->getAlertasAdmin();

        return view('home', compact('stats', 'alertas'));
    }

    /**
     * Dashboard para Docente - enfocado en sus comisiones
     */
    private function dashboardDocente($user)
    {
        $userId = $user->id;

        // Obtener IDs de comisiones del docente (via pivot y legacy docente_id)
        $comisionIds = collect();
        try {
            $pivotIds = DB::connection('paicat')->table('comision_docente')
                ->where('user_id', $userId)
                ->where('activo', true)
                ->pluck('comision_id');
            $legacyIds = Comision::where('docente_id', $userId)->pluck('id');
            $comisionIds = $pivotIds->merge($legacyIds)->unique();
        } catch (\Exception $e) {}

        // Stats específicas del docente
        $stats = [
            'mis_comisiones' => 0,
            'mis_alumnos' => 0,
            'asistencias_hoy' => 0,
            'promedio_asistencia' => 0,
            'alumnos_en_riesgo' => 0,
            'evaluaciones_pendientes' => 0,
        ];

        try {
            $stats['mis_comisiones'] = $comisionIds->count();
        } catch (\Exception $e) {}

        try {
            if ($comisionIds->isNotEmpty()) {
                $stats['mis_alumnos'] = DB::connection('paicat')->table('inscripcion_comision')
                    ->whereIn('comision_id', $comisionIds)
                    ->where('estado', 'activa')
                    ->count();
            }
        } catch (\Exception $e) {}

        try {
            if ($comisionIds->isNotEmpty()) {
                $stats['asistencias_hoy'] = Asistencia::whereDate('fecha', today())
                    ->whereIn('comision_id', $comisionIds)
                    ->distinct('inscripcion_id')
                    ->count('inscripcion_id');
            }
        } catch (\Exception $e) {}

        // Comisiones del docente con datos para acceso rápido
        $misComisiones = collect();
        try {
            if ($comisionIds->isNotEmpty()) {
                $misComisiones = Comision::whereIn('id', $comisionIds)
                    ->where('estado', 'activa')
                    ->withCount(['inscripciones as alumnos_activos_count' => function ($q) {
                        $q->where('estado', 'activa');
                    }])
                    ->orderByRaw("CASE
                        WHEN modalidad = 'Presencial' THEN 1
                        WHEN modalidad = 'Semipresencial' THEN 2
                        ELSE 3
                    END")
                    ->orderBy('codigo')
                    ->get();
            }
        } catch (\Exception $e) {}

        $alertas = $this->getAlertasDocente($comisionIds);

        return view('home-docente', compact('stats', 'alertas', 'misComisiones'));
    }

    private function getAlertasAdmin(): array
    {
        $alertas = [];

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

        try {
            $solicitudes = SolicitudCambio::where('estado', 'pendiente')->count();
            if ($solicitudes > 0) {
                $alertas[] = [
                    'tipo' => 'info',
                    'icono' => 'refresh',
                    'mensaje' => "{$solicitudes} solicitudes de cambio de comisión pendientes",
                    'url' => route('solicitudes.index'),
                ];
            }
        } catch (\Exception $e) {}

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

    private function getAlertasDocente($comisionIds): array
    {
        $alertas = [];

        if ($comisionIds->isEmpty()) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => 'exclamation',
                'mensaje' => 'No tenés comisiones asignadas actualmente',
                'url' => '#',
            ];
            return $alertas;
        }

        // Comisiones presenciales sin asistencia hoy
        try {
            $presenciales = Comision::whereIn('id', $comisionIds)
                ->where('estado', 'activa')
                ->where('modalidad', 'Presencial')
                ->get();

            foreach ($presenciales as $comision) {
                $tieneAsistenciaHoy = Asistencia::where('comision_id', $comision->id)
                    ->whereDate('fecha', today())
                    ->exists();

                if (!$tieneAsistenciaHoy) {
                    $alertas[] = [
                        'tipo' => 'warning',
                        'icono' => 'clock',
                        'mensaje' => "Falta registrar asistencia hoy en {$comision->codigo}",
                        'url' => route('asistencias.create', $comision->id),
                    ];
                }
            }
        } catch (\Exception $e) {}

        return $alertas;
    }
}
