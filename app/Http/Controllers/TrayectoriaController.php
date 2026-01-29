<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Trayectoria;
use App\Models\SolicitudCambio;
use App\Models\CondicionParticular;
use App\Models\Comision;
use Illuminate\Http\Request;

class TrayectoriaController extends Controller
{
    /**
     * Mostrar listado de estudiantes con sus trayectorias
     */
    public function index(Request $request)
    {
        $query = Inscripcion::with(['trayectoriaActual'])
            ->whereNotIn('estado', [Inscripcion::ESTADO_CANCELADO]);

        // Filtros
        if ($request->filled('anio')) {
            $query->where('anio_ingreso', $request->anio);
        }

        if ($request->filled('estado_trayectoria')) {
            $query->whereHas('trayectoriaActual', function ($q) use ($request) {
                $q->where('estado', $request->estado_trayectoria);
            });
        }

        $inscripciones = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Inscripcion::activas()->count(),
            'activos' => Trayectoria::where('estado', 'activo')->whereNull('fecha_fin')->count(),
            'bajas' => Trayectoria::where('estado', 'baja')->count(),
            'libres' => Trayectoria::where('estado', 'libre')->count(),
        ];

        return view('trayectorias.index', compact('inscripciones', 'stats'));
    }

    /**
     * Mostrar historial de trayectoria de un estudiante
     */
    public function show(Inscripcion $inscripcion)
    {
        $inscripcion->load([
            'trayectorias' => fn($q) => $q->orderBy('fecha_inicio', 'desc'),
            'solicitudesCambio' => fn($q) => $q->orderBy('created_at', 'desc'),
            'condicionesParticulares' => fn($q) => $q->where('activa', true),
            'inscripcionesComision.comision.municipio',
        ]);

        $person = $inscripcion->getPerson();

        // Obtener comisiones actuales del estudiante para excluirlas
        $comisionesActualesIds = $inscripcion->inscripcionesComision
            ->where('estado', 'inscripto')
            ->pluck('comision_id')
            ->toArray();

        // Obtener comisiones disponibles para cambio
        // Filtrar por año actual, activas, con cupo disponible
        $comisionesDisponibles = Comision::with('municipio')
            ->where('estado', 'activa')
            ->where('anio', date('Y'))
            ->whereNotIn('id', $comisionesActualesIds)
            ->where(function ($query) {
                // Con cupo disponible O virtuales (sin límite)
                $query->whereRaw('cupo_actual < cupo_maximo')
                    ->orWhere('modalidad', 'Virtual')
                    ->orWhereNull('cupo_maximo');
            })
            ->orderBy('turno')
            ->orderBy('modalidad')
            ->orderBy('nombre')
            ->get();

        // Obtener turnos y modalidades desde alumnos_utn si están disponibles
        $turnos = Comision::TURNOS;
        $modalidades = Comision::MODALIDADES;

        return view('trayectorias.show', compact(
            'inscripcion',
            'person',
            'comisionesDisponibles',
            'turnos',
            'modalidades'
        ));
    }

    /**
     * Registrar cambio de estado en la trayectoria
     */
    public function cambiarEstado(Request $request, Inscripcion $inscripcion)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activo,pausado,libre,baja,reincorporado,aprobado,desaprobado',
            'motivo' => 'required|string|max:500',
            'es_voluntaria' => 'nullable|boolean',
        ]);

        // Cerrar trayectoria actual si existe
        $trayectoriaActual = $inscripcion->trayectorias()->whereNull('fecha_fin')->first();
        if ($trayectoriaActual) {
            $trayectoriaActual->update(['fecha_fin' => now()]);
        }

        // Crear nueva trayectoria
        Trayectoria::create([
            'inscripcion_id' => $inscripcion->id,
            'estado' => $validated['estado'],
            'fecha_inicio' => now(),
            'motivo' => $validated['motivo'],
            'es_voluntaria' => $validated['es_voluntaria'] ?? null,
            'registrado_por' => auth()->id(),
        ]);

        // Actualizar estado de inscripción si corresponde
        if ($validated['estado'] === 'baja') {
            $inscripcion->update(['estado' => Inscripcion::ESTADO_BAJA]);
        }

        return redirect()->back()->with('success', 'Estado de trayectoria actualizado.');
    }

    /**
     * Registrar una baja (RF03)
     */
    public function registrarBaja(Request $request, Inscripcion $inscripcion)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
            'es_voluntaria' => 'required|boolean',
        ]);

        // Cerrar trayectoria actual
        $inscripcion->trayectorias()->whereNull('fecha_fin')->update(['fecha_fin' => now()]);

        // Crear trayectoria de baja
        Trayectoria::create([
            'inscripcion_id' => $inscripcion->id,
            'estado' => Trayectoria::ESTADO_BAJA,
            'fecha_inicio' => now(),
            'motivo' => $validated['motivo'],
            'es_voluntaria' => $validated['es_voluntaria'],
            'registrado_por' => auth()->id(),
        ]);

        // Actualizar inscripción
        $inscripcion->update(['estado' => Inscripcion::ESTADO_BAJA]);

        return redirect()->route('trayectorias.index')
            ->with('success', 'Baja registrada correctamente.');
    }

    /**
     * Listar estudiantes inactivos (RF04)
     */
    public function inactivos(Request $request)
    {
        $diasInactividad = $request->input('dias', 14); // Por defecto 14 días

        $fechaLimite = now()->subDays($diasInactividad);

        // Inscripciones activas sin asistencia reciente
        $inactivos = Inscripcion::activas()
            ->whereDoesntHave('asistencias', function ($q) use ($fechaLimite) {
                $q->where('fecha', '>=', $fechaLimite)
                    ->where('estado', 'presente');
            })
            ->with('trayectoriaActual')
            ->paginate(20);

        return view('trayectorias.inactivos', compact('inactivos', 'diasInactividad'));
    }

    // ========== SOLICITUDES DE CAMBIO ==========

    /**
     * Listar solicitudes de cambio
     */
    public function solicitudesIndex(Request $request)
    {
        $query = SolicitudCambio::with(['inscripcion', 'comisionOrigen', 'comisionDestino']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pendientes' => SolicitudCambio::pendientes()->count(),
            'trueques' => SolicitudCambio::where('estado', 'trueque_detectado')->count(),
        ];

        return view('trayectorias.solicitudes.index', compact('solicitudes', 'stats'));
    }

    /**
     * Crear solicitud de cambio de comisión (RF01)
     */
    public function crearSolicitudCambio(Request $request, Inscripcion $inscripcion)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:comision,modalidad,turno,carrera',
            'comision_destino_id' => 'required_if:tipo,comision|exists:comisiones,id',
            'modalidad_destino' => 'required_if:tipo,modalidad|in:Presencial,Virtual,Semipresencial',
            'turno_destino' => 'required_if:tipo,turno|in:Mañana,Tarde,Noche',
            'motivo' => 'required|string|max:500',
        ]);

        // Obtener valores origen
        $comisionActual = $inscripcion->inscripcionesComision()
            ->where('estado', 'inscripto')
            ->first();

        $solicitud = SolicitudCambio::create([
            'inscripcion_id' => $inscripcion->id,
            'tipo' => $validated['tipo'],
            'comision_origen_id' => $comisionActual?->comision_id,
            'comision_destino_id' => $validated['comision_destino_id'] ?? null,
            'modalidad_origen' => $inscripcion->modalidad,
            'modalidad_destino' => $validated['modalidad_destino'] ?? null,
            'turno_origen' => $inscripcion->turno_ingreso,
            'turno_destino' => $validated['turno_destino'] ?? null,
            'motivo' => $validated['motivo'],
            'estado' => SolicitudCambio::ESTADO_PENDIENTE,
        ]);

        // RF11: Detectar posible trueque
        if ($validated['tipo'] === 'comision') {
            $trueque = SolicitudCambio::detectarTrueque($solicitud);
            if ($trueque) {
                $solicitud->update([
                    'estado' => SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                    'solicitud_trueque_id' => $trueque->id,
                ]);
                $trueque->update([
                    'estado' => SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                    'solicitud_trueque_id' => $solicitud->id,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Solicitud de cambio creada.');
    }

    /**
     * Procesar solicitud de cambio
     */
    public function procesarSolicitud(Request $request, SolicitudCambio $solicitud)
    {
        $validated = $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
            'motivo_rechazo' => 'required_if:accion,rechazar|nullable|string|max:500',
        ]);

        if ($validated['accion'] === 'aprobar') {
            $this->aprobarSolicitud($solicitud);
        } else {
            $solicitud->update([
                'estado' => SolicitudCambio::ESTADO_RECHAZADA,
                'motivo_rechazo' => $validated['motivo_rechazo'],
                'procesado_por' => auth()->id(),
                'fecha_procesamiento' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Solicitud procesada.');
    }

    /**
     * Aprobar solicitud de cambio
     */
    private function aprobarSolicitud(SolicitudCambio $solicitud)
    {
        $inscripcion = $solicitud->inscripcion;

        switch ($solicitud->tipo) {
            case 'comision':
                // Cambiar de comisión
                $inscripcionComision = $inscripcion->inscripcionesComision()
                    ->where('comision_id', $solicitud->comision_origen_id)
                    ->first();

                if ($inscripcionComision) {
                    $inscripcionComision->update([
                        'comision_id' => $solicitud->comision_destino_id,
                    ]);

                    // Actualizar cupos
                    if ($solicitud->comisionOrigen) {
                        $solicitud->comisionOrigen->decrement('cupo_actual');
                    }
                    if ($solicitud->comisionDestino) {
                        $solicitud->comisionDestino->increment('cupo_actual');
                    }
                }
                break;

            case 'modalidad':
                $inscripcion->update(['modalidad' => $solicitud->modalidad_destino]);
                break;

            case 'turno':
                $inscripcion->update(['turno_ingreso' => $solicitud->turno_destino]);
                break;
        }

        $solicitud->update([
            'estado' => SolicitudCambio::ESTADO_APROBADA,
            'procesado_por' => auth()->id(),
            'fecha_procesamiento' => now(),
        ]);
    }

    // ========== CONDICIONES PARTICULARES ==========

    /**
     * Agregar condición particular (RF13)
     */
    public function agregarCondicion(Request $request, Inscripcion $inscripcion)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:discapacidad,enfermedad_cronica,situacion_laboral,situacion_familiar,condicionalidad_academica,otra',
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'requiere_adecuacion' => 'boolean',
            'adecuaciones_sugeridas' => 'nullable|string',
        ]);

        CondicionParticular::create([
            'inscripcion_id' => $inscripcion->id,
            'tipo' => $validated['tipo'],
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'requiere_adecuacion' => $validated['requiere_adecuacion'] ?? false,
            'adecuaciones_sugeridas' => $validated['adecuaciones_sugeridas'] ?? null,
            'fecha_inicio' => now(),
            'activa' => true,
            'registrado_por' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Condición particular registrada.');
    }

    /**
     * Desactivar condición particular
     */
    public function desactivarCondicion(CondicionParticular $condicion)
    {
        $condicion->update([
            'activa' => false,
            'fecha_fin' => now(),
        ]);

        return redirect()->back()->with('success', 'Condición desactivada.');
    }
}
