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
                'solicitudTrueque.inscripcion',
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
            // Usar subquery en la BD de alumnos para evitar traer todos los IDs a memoria
            $dbAlumnos = DB::connection('alumnos_utn')->getDatabaseName();
            $query->whereHas('inscripcion', function ($q) use ($termino, $dbAlumnos) {
                $q->whereIn('person_id', function ($subquery) use ($termino, $dbAlumnos) {
                    $subquery->from("{$dbAlumnos}.persons")
                        ->select('id')
                        ->where(function ($q2) use ($termino) {
                            $q2->where('nombre', 'like', "%{$termino}%")
                                ->orWhere('apellido', 'like', "%{$termino}%")
                                ->orWhere('documento', 'like', "%{$termino}%");
                        });
                });
            });
        }

        $solicitudes = $query->paginate(20);

        // Estadísticas globales en una sola query
        $statsRaw = SolicitudCambio::query()
            ->selectRaw("SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as pendientes", [SolicitudCambio::ESTADO_PENDIENTE])
            ->selectRaw("SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as trueques", [SolicitudCambio::ESTADO_TRUEQUE_DETECTADO])
            ->selectRaw("SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as en_revision", [SolicitudCambio::ESTADO_EN_REVISION])
            ->selectRaw("SUM(CASE WHEN estado = ? AND DATE(fecha_procesamiento) = ? THEN 1 ELSE 0 END) as aprobadas_hoy", [SolicitudCambio::ESTADO_APROBADA, today()->toDateString()])
            ->first();

        $estadisticas = [
            'pendientes' => (int) ($statsRaw->pendientes ?? 0),
            'trueques' => (int) ($statsRaw->trueques ?? 0),
            'en_revision' => (int) ($statsRaw->en_revision ?? 0),
            'aprobadas_hoy' => (int) ($statsRaw->aprobadas_hoy ?? 0),
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

        try {
            DB::connection('paicat')->beginTransaction();

            // Re-verificar estado con lock para evitar race condition
            $solicitud = SolicitudCambio::lockForUpdate()->find($solicitud->id);
            if (!$solicitud || !$solicitud->puedeSerProcesada()) {
                DB::connection('paicat')->rollBack();
                return back()->with('error', 'Esta solicitud ya fue procesada por otro usuario.');
            }

            // Verificar cupos en comisión destino con lock
            if ($solicitud->comision_destino_id) {
                $comisionDestino = Comision::lockForUpdate()->find($solicitud->comision_destino_id);

                if (!$comisionDestino) {
                    DB::connection('paicat')->rollBack();
                    return back()->with('error', 'La comision destino ya no existe.');
                }

                if (!$comisionDestino->isActiva()) {
                    DB::connection('paicat')->rollBack();
                    return back()->with('error', 'La comision destino no esta activa (estado: ' . $comisionDestino->estado . ').');
                }

                if (!$comisionDestino->tieneCuposDisponibles()) {
                    DB::connection('paicat')->rollBack();
                    return back()->with('error', 'No hay cupos disponibles en la comision destino.');
                }
            }

            // Realizar el cambio
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
        try {
            DB::connection('paicat')->beginTransaction();

            // Re-obtener ambas solicitudes con lock para evitar race conditions
            $solicitud = SolicitudCambio::lockForUpdate()->find($solicitud->id);
            if (!$solicitud || !$solicitud->puedeSerProcesada()) {
                DB::connection('paicat')->rollBack();
                return back()->with('error', 'Esta solicitud ya fue procesada por otro usuario.');
            }

            $solicitudTrueque = $solicitud->solicitud_trueque_id
                ? SolicitudCambio::lockForUpdate()->find($solicitud->solicitud_trueque_id)
                : null;

            if (!$solicitudTrueque || !$solicitudTrueque->puedeSerProcesada()) {
                DB::connection('paicat')->rollBack();
                return back()->with('error', 'La solicitud de trueque vinculada no esta disponible o ya fue procesada.');
            }

            // Verificar simetría del trueque
            if ($solicitudTrueque->solicitud_trueque_id !== $solicitud->id) {
                DB::connection('paicat')->rollBack();
                return back()->with('error', 'Error de integridad: las solicitudes de trueque no estan vinculadas correctamente.');
            }

            // Lockear las comisiones involucradas para evitar race conditions en cupos
            $comisionIds = array_filter(array_unique([
                $solicitud->comision_origen_id,
                $solicitud->comision_destino_id,
                $solicitudTrueque->comision_origen_id,
                $solicitudTrueque->comision_destino_id,
            ]));
            if (!empty($comisionIds)) {
                Comision::lockForUpdate()->whereIn('id', $comisionIds)->get();
            }

            // Verificar que las comisiones destino esten activas
            foreach ([$solicitud, $solicitudTrueque] as $sol) {
                if ($sol->comision_destino_id) {
                    $destino = Comision::find($sol->comision_destino_id);
                    if ($destino && !$destino->isActiva()) {
                        DB::connection('paicat')->rollBack();
                        return back()->with('error', "La comision destino '{$destino->nombre}' no esta activa.");
                    }
                }
            }

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
                ->with('success', 'Trueque aprobado. Ambos alumnos han sido cambiados de comision.');
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

        if (!$inscripcion) {
            throw new \RuntimeException('La inscripcion asociada a la solicitud no existe.');
        }

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

            // Sincronizar cupo de comisión origen
            $comisionOrigen = Comision::find($solicitud->comision_origen_id);
            if ($comisionOrigen) {
                $comisionOrigen->sincronizarCupo();
                $comisionOrigenNombre = $comisionOrigen->nombre;
            }
        }

        // Crear nueva inscripción en comisión destino
        $inscripcion->inscripcionesComision()->create([
            'comision_id' => $solicitud->comision_destino_id,
            'estado' => 'confirmado',
            'fecha_inscripcion' => now(),
        ]);

        // Sincronizar cupo de comisión destino
        $comisionDestino = Comision::find($solicitud->comision_destino_id);
        $comisionDestinoNombre = $comisionDestino?->nombre ?? 'Comision #' . $solicitud->comision_destino_id;
        if ($comisionDestino) {
            $comisionDestino->sincronizarCupo();
        }

        // Sincronizar datos de la inscripción con la nueva comisión
        $cambiosInscripcion = [];
        $detallesCambios = [];

        if ($comisionDestino) {
            if ($comisionDestino->modalidad && $comisionDestino->modalidad !== $inscripcion->modalidad) {
                $detallesCambios[] = "Modalidad: {$inscripcion->modalidad} → {$comisionDestino->modalidad}";
                $cambiosInscripcion['modalidad'] = $comisionDestino->modalidad;
            }

            if ($comisionDestino->turno && $comisionDestino->turno !== $inscripcion->turno_carrera) {
                $detallesCambios[] = "Turno: {$inscripcion->turno_carrera} → {$comisionDestino->turno}";
                $cambiosInscripcion['turno_carrera'] = $comisionDestino->turno;
            }

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
            ? "Cambio de comision: {$comisionOrigenNombre} → {$comisionDestinoNombre}"
            : "Asignado a comision {$comisionDestinoNombre}";

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

        try {
            DB::connection('paicat')->beginTransaction();

            // Re-verificar con lock
            $solicitud = SolicitudCambio::lockForUpdate()->find($solicitud->id);
            if (!$solicitud || !$solicitud->puedeSerProcesada()) {
                DB::connection('paicat')->rollBack();
                return back()->with('error', 'Esta solicitud ya fue procesada por otro usuario.');
            }

            // Si es un trueque, rechazar ambas solicitudes
            if ($solicitud->estado === SolicitudCambio::ESTADO_TRUEQUE_DETECTADO && $solicitud->solicitud_trueque_id) {
                $solicitudTrueque = SolicitudCambio::lockForUpdate()->find($solicitud->solicitud_trueque_id);
                if ($solicitudTrueque && $solicitudTrueque->puedeSerProcesada()) {
                    $solicitudTrueque->update([
                        'estado' => SolicitudCambio::ESTADO_RECHAZADA,
                        'motivo_rechazo' => 'Trueque rechazado: ' . $request->motivo_rechazo,
                        'procesado_por' => auth()->id(),
                        'fecha_procesamiento' => now(),
                        'solicitud_trueque_id' => null, // Limpiar referencia para permitir futura detección
                    ]);
                }
                // Limpiar referencia de trueque en la solicitud actual también
                $solicitud->update([
                    'estado' => SolicitudCambio::ESTADO_RECHAZADA,
                    'motivo_rechazo' => $request->motivo_rechazo,
                    'procesado_por' => auth()->id(),
                    'fecha_procesamiento' => now(),
                    'solicitud_trueque_id' => null,
                ]);
            } else {
                $solicitud->update([
                    'estado' => SolicitudCambio::ESTADO_RECHAZADA,
                    'motivo_rechazo' => $request->motivo_rechazo,
                    'procesado_por' => auth()->id(),
                    'fecha_procesamiento' => now(),
                ]);
            }

            DB::connection('paicat')->commit();

            return redirect()
                ->route('solicitudes.index')
                ->with('success', 'Solicitud rechazada.');
        } catch (\Exception $e) {
            DB::connection('paicat')->rollBack();
            return back()->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Detectar trueques pendientes en todas las solicitudes
     */
    public function detectarTrueques(): RedirectResponse
    {
        // Incluir solicitudes que fueron rechazadas como trueque pero aún son pendientes de re-detección
        $solicitudesPendientes = SolicitudCambio::where('estado', SolicitudCambio::ESTADO_PENDIENTE)
            ->where('tipo', SolicitudCambio::TIPO_COMISION)
            ->whereNotNull('comision_destino_id')
            ->whereNull('solicitud_trueque_id')
            ->get();

        $truequesDetectados = 0;

        foreach ($solicitudesPendientes as $solicitud) {
            $trueque = SolicitudCambio::detectarTrueque($solicitud);
            if ($trueque && $trueque->solicitud_trueque_id === null) {
                DB::connection('paicat')->beginTransaction();
                try {
                    // Lock ambas para evitar detección duplicada
                    $sol = SolicitudCambio::lockForUpdate()->find($solicitud->id);
                    $tru = SolicitudCambio::lockForUpdate()->find($trueque->id);

                    if ($sol && $tru
                        && $sol->estado === SolicitudCambio::ESTADO_PENDIENTE
                        && $tru->estado === SolicitudCambio::ESTADO_PENDIENTE
                        && $sol->solicitud_trueque_id === null
                        && $tru->solicitud_trueque_id === null
                    ) {
                        $sol->update([
                            'estado' => SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                            'solicitud_trueque_id' => $tru->id,
                        ]);
                        $tru->update([
                            'estado' => SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
                            'solicitud_trueque_id' => $sol->id,
                        ]);
                        $truequesDetectados++;
                    }

                    DB::connection('paicat')->commit();
                } catch (\Exception $e) {
                    DB::connection('paicat')->rollBack();
                }
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
