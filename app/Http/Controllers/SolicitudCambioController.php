<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\SolicitudCambio;
use App\Models\Trayectoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudCambioController extends Controller
{
    /**
     * Mostrar listado de solicitudes de cambio
     */
    public function index(Request $request): View
    {
        $query = SolicitudCambio::query()
            ->with([
                'inscripcion',
                'comisionOrigen',
                'comisionDestino',
                'solicitudTrueque',
                'procesadoPor',
            ])
            ->orderByRaw("CASE
                WHEN estado = 'trueque_detectado' THEN 1
                WHEN estado = 'pendiente' THEN 2
                WHEN estado = 'en_revision' THEN 3
                ELSE 4
            END")
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->whereHas('inscripcion', function ($q) use ($termino) {
                // Buscar por person_id en alumnos_utn
                $personIds = \App\Models\AlumnosUtn\Person::on('alumnos_utn')
                    ->where(function ($q2) use ($termino) {
                        $q2->where('nombre', 'like', "%{$termino}%")
                            ->orWhere('apellido', 'like', "%{$termino}%")
                            ->orWhere('documento', 'like', "%{$termino}%");
                    })
                    ->pluck('id')
                    ->toArray();
                $q->whereIn('person_id', $personIds);
            });
        }

        $solicitudes = $query->paginate(20);

        // Estadísticas
        $estadisticas = [
            'pendientes' => SolicitudCambio::where('estado', SolicitudCambio::ESTADO_PENDIENTE)->count(),
            'trueques' => SolicitudCambio::where('estado', SolicitudCambio::ESTADO_TRUEQUE_DETECTADO)->count(),
            'en_revision' => SolicitudCambio::where('estado', SolicitudCambio::ESTADO_EN_REVISION)->count(),
            'aprobadas_hoy' => SolicitudCambio::where('estado', SolicitudCambio::ESTADO_APROBADA)
                ->whereDate('fecha_procesamiento', today())
                ->count(),
        ];

        return view('solicitudes.index', compact('solicitudes', 'estadisticas'));
    }

    /**
     * Ver detalle de una solicitud
     */
    public function show(SolicitudCambio $solicitud): View
    {
        $solicitud->load([
            'inscripcion',
            'comisionOrigen.municipio',
            'comisionDestino.municipio',
            'solicitudTrueque.inscripcion',
            'procesadoPor',
        ]);

        // Verificar cupos en comisión destino
        $cuposDisponibles = null;
        $tieneCupos = true;
        if ($solicitud->comisionDestino) {
            $cuposDisponibles = $solicitud->comisionDestino->cupos_disponibles;
            $tieneCupos = $solicitud->comisionDestino->tieneCuposDisponibles();
        }

        return view('solicitudes.show', compact('solicitud', 'cuposDisponibles', 'tieneCupos'));
    }

    /**
     * Aprobar una solicitud de cambio
     */
    public function aprobar(Request $request, SolicitudCambio $solicitud): RedirectResponse
    {
        if (!$solicitud->puedeSerProcesada()) {
            return back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        // Si es un trueque, verificar que ambas solicitudes se aprueben juntas
        if ($solicitud->estado === SolicitudCambio::ESTADO_TRUEQUE_DETECTADO && $solicitud->solicitud_trueque_id) {
            return $this->aprobarTrueque($solicitud);
        }

        // Verificar cupos en comisión destino
        if ($solicitud->comision_destino_id) {
            $comisionDestino = Comision::find($solicitud->comision_destino_id);
            if ($comisionDestino && !$comisionDestino->tieneCuposDisponibles()) {
                return back()->with('error', 'No hay cupos disponibles en la comisión destino.');
            }
        }

        try {
            DB::connection('paicat')->beginTransaction();

            // Realizar el cambio según el tipo
            $this->ejecutarCambio($solicitud);

            // Actualizar estado de la solicitud
            $solicitud->update([
                'estado' => SolicitudCambio::ESTADO_APROBADA,
                'procesado_por' => auth()->id(),
                'fecha_procesamiento' => now(),
            ]);

            DB::connection('paicat')->commit();

            return redirect()
                ->route('solicitudes.index')
                ->with('success', 'Solicitud aprobada y cambio realizado correctamente.');
        } catch (\Exception $e) {
            DB::connection('paicat')->rollBack();
            return back()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar un trueque (ambas solicitudes)
     */
    protected function aprobarTrueque(SolicitudCambio $solicitud): RedirectResponse
    {
        $solicitudTrueque = $solicitud->solicitudTrueque;

        if (!$solicitudTrueque || !$solicitudTrueque->puedeSerProcesada()) {
            return back()->with('error', 'La solicitud de trueque vinculada no está disponible.');
        }

        try {
            DB::connection('paicat')->beginTransaction();

            // Ejecutar ambos cambios
            $this->ejecutarCambio($solicitud);
            $this->ejecutarCambio($solicitudTrueque);

            // Marcar ambas como aprobadas
            $now = now();
            $userId = auth()->id();

            $solicitud->update([
                'estado' => SolicitudCambio::ESTADO_APROBADA,
                'procesado_por' => $userId,
                'fecha_procesamiento' => $now,
            ]);

            $solicitudTrueque->update([
                'estado' => SolicitudCambio::ESTADO_APROBADA,
                'procesado_por' => $userId,
                'fecha_procesamiento' => $now,
            ]);

            DB::connection('paicat')->commit();

            return redirect()
                ->route('solicitudes.index')
                ->with('success', 'Trueque aprobado. Ambos alumnos han sido cambiados de comisión.');
        } catch (\Exception $e) {
            DB::connection('paicat')->rollBack();
            return back()->with('error', 'Error al procesar el trueque: ' . $e->getMessage());
        }
    }

    /**
     * Ejecutar el cambio de comisión/modalidad/turno
     */
    protected function ejecutarCambio(SolicitudCambio $solicitud): void
    {
        $inscripcion = $solicitud->inscripcion;

        // Todas las solicitudes se resuelven como cambio de comisión
        // ya que la comisión contiene turno, modalidad y periodo
        $this->cambiarComision($inscripcion, $solicitud);
    }

    /**
     * Cambiar la comisión de un alumno
     */
    protected function cambiarComision(Inscripcion $inscripcion, SolicitudCambio $solicitud): void
    {
        $comisionOrigenNombre = null;

        // Marcar la inscripción actual como trasladada
        if ($solicitud->comision_origen_id) {
            $inscripcion->inscripcionesComision()
                ->where('comision_id', $solicitud->comision_origen_id)
                ->whereIn('estado', ['inscripto', 'confirmado'])
                ->update(['estado' => 'trasladado']);

            // Decrementar cupo de comisión origen
            $comisionOrigen = Comision::find($solicitud->comision_origen_id);
            if ($comisionOrigen) {
                $comisionOrigen->decrementarCupo();
                $comisionOrigenNombre = $comisionOrigen->nombre;
            }
        }

        // Crear nueva inscripción en comisión destino
        $inscripcion->inscripcionesComision()->create([
            'comision_id' => $solicitud->comision_destino_id,
            'estado' => 'confirmado',
            'fecha_inscripcion' => now(),
        ]);

        // Incrementar cupo de comisión destino
        $comisionDestino = Comision::find($solicitud->comision_destino_id);
        $comisionDestinoNombre = $comisionDestino?->nombre ?? 'Comisión #' . $solicitud->comision_destino_id;
        if ($comisionDestino) {
            $comisionDestino->incrementarCupo();
        }

        // Sincronizar datos de la inscripción con la nueva comisión
        $cambiosInscripcion = [];
        $detallesCambios = [];

        if ($comisionDestino) {
            // Sincronizar modalidad
            if ($comisionDestino->modalidad && $comisionDestino->modalidad !== $inscripcion->modalidad) {
                $detallesCambios[] = "Modalidad: {$inscripcion->modalidad} → {$comisionDestino->modalidad}";
                $cambiosInscripcion['modalidad'] = $comisionDestino->modalidad;
            }

            // Sincronizar turno
            if ($comisionDestino->turno && $comisionDestino->turno !== $inscripcion->turno_carrera) {
                $detallesCambios[] = "Turno: {$inscripcion->turno_carrera} → {$comisionDestino->turno}";
                $cambiosInscripcion['turno_carrera'] = $comisionDestino->turno;
            }

            // Sincronizar tipo de ingreso (periodo: Intensivo/Extensivo)
            if ($comisionDestino->periodo && $comisionDestino->periodo !== $inscripcion->tipo_ingreso) {
                $detallesCambios[] = "Tipo ingreso: {$inscripcion->tipo_ingreso} → {$comisionDestino->periodo}";
                $cambiosInscripcion['tipo_ingreso'] = $comisionDestino->periodo;
            }

            if (!empty($cambiosInscripcion)) {
                $inscripcion->update($cambiosInscripcion);
            }
        }

        // Registrar en trayectoria
        $motivo = $comisionOrigenNombre
            ? "Cambio de comisión: {$comisionOrigenNombre} → {$comisionDestinoNombre}"
            : "Asignado a comisión {$comisionDestinoNombre}";

        if (!empty($detallesCambios)) {
            $motivo .= '. Datos actualizados: ' . implode(', ', $detallesCambios);
        }

        if ($solicitud->motivo) {
            $motivo .= " (Motivo: {$solicitud->motivo})";
        }

        Trayectoria::registrarEvento(
            $inscripcion->id,
            Trayectoria::ESTADO_ACTIVO,
            $motivo,
            null,
            auth()->id()
        );
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazar(Request $request, SolicitudCambio $solicitud): RedirectResponse
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        if (!$solicitud->puedeSerProcesada()) {
            return back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        // Si es un trueque, rechazar ambas solicitudes
        if ($solicitud->estado === SolicitudCambio::ESTADO_TRUEQUE_DETECTADO && $solicitud->solicitud_trueque_id) {
            $solicitud->solicitudTrueque->update([
                'estado' => SolicitudCambio::ESTADO_RECHAZADA,
                'motivo_rechazo' => 'Trueque rechazado: ' . $request->motivo_rechazo,
                'procesado_por' => auth()->id(),
                'fecha_procesamiento' => now(),
            ]);
        }

        $solicitud->update([
            'estado' => SolicitudCambio::ESTADO_RECHAZADA,
            'motivo_rechazo' => $request->motivo_rechazo,
            'procesado_por' => auth()->id(),
            'fecha_procesamiento' => now(),
        ]);

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud rechazada.');
    }

    /**
     * Detectar trueques pendientes en todas las solicitudes
     */
    public function detectarTrueques(): RedirectResponse
    {
        $solicitudesPendientes = SolicitudCambio::where('estado', SolicitudCambio::ESTADO_PENDIENTE)
            ->where('tipo', SolicitudCambio::TIPO_COMISION)
            ->whereNotNull('comision_destino_id')
            ->whereNull('solicitud_trueque_id')
            ->get();

        $truequesDetectados = 0;

        foreach ($solicitudesPendientes as $solicitud) {
            $trueque = SolicitudCambio::detectarTrueque($solicitud);
            if ($trueque && $trueque->solicitud_trueque_id === null) {
                $solicitud->update([
                    'estado' => SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                    'solicitud_trueque_id' => $trueque->id,
                ]);
                $trueque->update([
                    'estado' => SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                    'solicitud_trueque_id' => $solicitud->id,
                ]);
                $truequesDetectados++;
            }
        }

        if ($truequesDetectados > 0) {
            return back()->with('success', "Se detectaron {$truequesDetectados} trueque(s) nuevo(s).");
        }

        return back()->with('info', 'No se encontraron nuevos trueques.');
    }

    /**
     * Verificar cupos disponibles para una comisión (API)
     */
    public function verificarCupos(Comision $comision)
    {
        return response()->json([
            'tiene_cupos' => $comision->tieneCuposDisponibles(),
            'cupos_disponibles' => $comision->cupos_disponibles,
            'cupo_actual' => $comision->cupo_actual,
            'cupo_maximo' => $comision->cupo_maximo,
            'es_virtual' => $comision->esVirtual(),
        ]);
    }
}
