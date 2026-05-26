<?php

namespace App\Http\Controllers;

use App\Models\AlumnosUtn\Person;
use App\Models\Comision;
use App\Models\Inscripcion;
use App\Models\SolicitudCambio;
use App\Services\ConfiguracionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudRapidaController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $resultados = collect();
        $personas = collect();
        $busqueda = $request->input('buscar', '');
        $esDocente = $user->soloContenidoAsignado();

        if ($request->filled('buscar')) {
            $termino = trim($request->input('buscar'));

            $personIds = Person::on('alumnos_utn')
                ->where(function ($q) use ($termino) {
                    $q->where('documento', 'like', "%{$termino}%")
                      ->orWhere('apellido', 'like', "%{$termino}%")
                      ->orWhere('nombre', 'like', "%{$termino}%");
                })
                ->pluck('id')
                ->toArray();

            if (!empty($personIds)) {
                $query = Inscripcion::whereIn('person_id', $personIds)
                    ->whereHas('inscripcionesComision', function ($q) {
                        $q->whereIn('estado', ['inscripto', 'confirmado']);
                    })
                    ->with(['inscripcionesComision' => function ($q) {
                        $q->whereIn('estado', ['inscripto', 'confirmado'])->with('comision');
                    }]);

                // Docentes solo ven alumnos de sus comisiones asignadas
                if ($esDocente) {
                    $misComisionIds = DB::table('comision_docente')
                        ->where('user_id', $user->id)
                        ->where('activo', true)
                        ->pluck('comision_id')
                        ->toArray();

                    $query->whereHas('inscripcionesComision', function ($q) use ($misComisionIds) {
                        $q->whereIn('estado', ['inscripto', 'confirmado'])
                          ->whereIn('comision_id', $misComisionIds);
                    });
                }

                $resultados = $query->get();
                $personas = Person::on('alumnos_utn')
                    ->whereIn('id', $resultados->pluck('person_id')->unique())
                    ->get()
                    ->keyBy('id');
            }
        }

        return view('solicitudes.rapida.index', compact('resultados', 'personas', 'busqueda', 'esDocente'));
    }

    public function form(Inscripcion $inscripcion): View
    {
        $user = auth()->user();

        // Verificar que el usuario tiene visibilidad sobre este alumno
        if ($user->soloContenidoAsignado()) {
            $misComisionIds = DB::table('comision_docente')
                ->where('user_id', $user->id)
                ->where('activo', true)
                ->pluck('comision_id')
                ->toArray();

            $tieneAcceso = $inscripcion->inscripcionesComision()
                ->whereIn('estado', ['inscripto', 'confirmado'])
                ->whereIn('comision_id', $misComisionIds)
                ->exists();

            abort_unless($tieneAcceso, 403, 'No tenés acceso a este alumno.');
        }

        $persona = Person::on('alumnos_utn')->find($inscripcion->person_id);

        $inscripcionComision = $inscripcion->inscripcionesComision()
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->with('comision')
            ->first();

        $comisionActual = $inscripcionComision?->comision;

        $tieneSolicitudPendiente = $inscripcion->solicitudesCambio()
            ->where('tipo', 'comision')
            ->whereIn('estado', [
                SolicitudCambio::ESTADO_PENDIENTE,
                SolicitudCambio::ESTADO_EN_REVISION,
                SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
            ])
            ->exists();

        $comisionesDisponibles = Comision::activas()
            ->conCuposDisponibles()
            ->where('anio', date('Y'))
            ->when($comisionActual, fn($q) => $q->where('id', '!=', $comisionActual->id))
            ->orderBy('turno')
            ->orderBy('modalidad')
            ->orderBy('nombre')
            ->get();

        return view('solicitudes.rapida.form', compact(
            'inscripcion',
            'persona',
            'comisionActual',
            'comisionesDisponibles',
            'tieneSolicitudPendiente',
        ));
    }

    public function store(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $user = auth()->user();

        if ($user->soloContenidoAsignado()) {
            $misComisionIds = DB::table('comision_docente')
                ->where('user_id', $user->id)
                ->where('activo', true)
                ->pluck('comision_id')
                ->toArray();

            $tieneAcceso = $inscripcion->inscripcionesComision()
                ->whereIn('estado', ['inscripto', 'confirmado'])
                ->whereIn('comision_id', $misComisionIds)
                ->exists();

            abort_unless($tieneAcceso, 403, 'No tenés acceso a este alumno.');
        }

        $request->validate([
            'tipo' => 'required|in:comision',
            'motivo' => 'required|string|min:10',
            'comision_destino_id' => 'required|exists:comisiones,id',
        ]);

        $maxSolicitudes = ConfiguracionService::get('max_solicitudes_cambio', 3);
        if ($maxSolicitudes > 0) {
            $solicitudesAnio = $inscripcion->solicitudesCambio()
                ->whereYear('created_at', now()->year)
                ->whereNotIn('estado', [SolicitudCambio::ESTADO_CANCELADA])
                ->count();

            if ($solicitudesAnio >= $maxSolicitudes) {
                return back()->withInput()->with('error',
                    "Se alcanzó el límite de {$maxSolicitudes} solicitudes de cambio por año para este alumno (ya tiene {$solicitudesAnio})."
                );
            }
        }

        $inscripcionComision = $inscripcion->inscripcionesComision()
            ->whereIn('estado', ['inscripto', 'confirmado'])
            ->with('comision')
            ->first();

        $comisionActual = $inscripcionComision?->comision;

        if (!$comisionActual) {
            return back()->withInput()->with('error', 'El alumno debe estar asignado a una comisión para solicitar un cambio.');
        }

        if ($comisionActual->id == $request->comision_destino_id) {
            return back()->withInput()->with('error', 'No puede solicitar cambio a la misma comisión en la que ya está inscripto.');
        }

        $solicitudPendiente = $inscripcion->solicitudesCambio()
            ->where('tipo', 'comision')
            ->whereIn('estado', [
                SolicitudCambio::ESTADO_PENDIENTE,
                SolicitudCambio::ESTADO_EN_REVISION,
                SolicitudCambio::ESTADO_TRUEQUE_DETECTADO,
            ])
            ->exists();

        if ($solicitudPendiente) {
            return back()->withInput()->with('error', 'Ya existe una solicitud de cambio pendiente para este alumno.');
        }

        $comisionDestino = Comision::find($request->comision_destino_id);

        $solicitud = $inscripcion->solicitudesCambio()->create([
            'tipo' => 'comision',
            'motivo' => $request->motivo,
            'comision_origen_id' => $comisionActual->id,
            'comision_destino_id' => $request->comision_destino_id,
            'modalidad_origen' => $comisionActual->modalidad,
            'modalidad_destino' => $comisionDestino?->modalidad,
            'turno_origen' => $comisionActual->turno,
            'turno_destino' => $comisionDestino?->turno,
            'estado' => SolicitudCambio::ESTADO_PENDIENTE,
        ]);

        $mensaje = 'Solicitud de cambio de comisión creada correctamente.';
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
            $mensaje = '¡Trueque detectado! Se encontró una solicitud inversa compatible. Ambas solicitudes requieren aprobación.';
        }

        return redirect()->route('solicitud-cambio.index')->with('success', $mensaje);
    }
}
