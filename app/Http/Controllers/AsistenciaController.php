<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Asistencia;
use App\Models\InscripcionComision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsistenciaController extends Controller
{
    /**
     * Vista principal de asistencias
     */
    public function index()
    {
        return view('asistencias.index');
    }

    /**
     * Mostrar formulario de registro de asistencias
     */
    public function mostrarRegistro(Request $request)
    {
        $user = Auth::user();

        $comisiones = $user->hasRole('docente')
            ? Comision::where('docente_id', $user->id)->activas()->with('inscripciones.academicoDato.user')->get()
            : Comision::activas()->with('inscripciones.academicoDato.user')->get();

        $comisionSeleccionada = null;
        $inscripciones = collect();
        $fecha = $request->input('fecha', today()->format('Y-m-d'));
        $asistenciasExistentes = [];

        if ($request->has('comision_id')) {
            $comisionSeleccionada = Comision::with('inscripciones.academicoDato.user')->findOrFail($request->comision_id);

            $inscripciones = $comisionSeleccionada->inscripciones()
                ->where('estado', 'confirmada')
                ->with('academicoDato.user')
                ->get();

            $asistenciasExistentes = Asistencia::whereIn('inscripcion_comision_id', $inscripciones->pluck('id'))
                ->whereDate('fecha', $fecha)
                ->get()
                ->keyBy('inscripcion_comision_id');
        }

        return view('asistencias.registrar', compact(
            'comisiones', 'comisionSeleccionada', 'inscripciones', 'fecha', 'asistenciasExistentes'
        ));
    }

    /**
     * Guardar registro de asistencias
     */
    public function guardarRegistro(Request $request)
    {
        $validated = $request->validate([
            'comision_id' => 'required|exists:comisiones,id',
            'fecha' => 'required|date|before_or_equal:today',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripcion_comisiones,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500',
        ]);

        foreach ($validated['asistencias'] as $asistenciaData) {
            Asistencia::updateOrCreate(
                [
                    'inscripcion_comision_id' => $asistenciaData['inscripcion_comision_id'],
                    'fecha' => $validated['fecha'],
                ],
                [
                    'estado' => $asistenciaData['estado'],
                    'observaciones' => $asistenciaData['observaciones'] ?? null,
                    'registrado_por' => Auth::id(),
                ]
            );
        }

        return redirect()->route('asistencias.registrar', [
            'comision_id' => $validated['comision_id'],
            'fecha' => $validated['fecha']
        ])->with('success', 'Asistencias registradas correctamente');
    }

    /**
     * Ver historial de asistencias de un alumno
     */
    public function historial(InscripcionComision $inscripcionComision)
    {
        $inscripcionComision->load([
            'asistencias' => fn($q) => $q->orderBy('fecha', 'desc'),
            'academicoDato.user',
            'comision'
        ]);

        $porcentaje = $inscripcionComision->calcularPorcentajeAsistencia();
        $estaEnRiesgo = $inscripcionComision->estaEnRiesgo();

        $estadisticas = [
            'total' => $inscripcionComision->asistencias->count(),
            'presentes' => $inscripcionComision->asistencias->where('estado', 'presente')->count(),
            'ausentes' => $inscripcionComision->asistencias->where('estado', 'ausente')->count(),
            'tardanzas' => $inscripcionComision->asistencias->where('estado', 'tardanza')->count(),
            'justificadas' => $inscripcionComision->asistencias->where('estado', 'justificado')->count(),
        ];

        return view('asistencias.historial', compact('inscripcionComision', 'porcentaje', 'estaEnRiesgo', 'estadisticas'));
    }

    /**
     * Alertas de alumnos en riesgo
     */
    public function alertas(Request $request)
    {
        $comisionId = $request->input('comision_id');

        $query = InscripcionComision::with(['academicoDato.user', 'comision', 'asistencias'])
            ->where('estado', 'confirmada');

        if ($comisionId) {
            $query->where('comision_id', $comisionId);
        }

        $inscripciones = $query->get();

        $alumnosEnRiesgo = $inscripciones->filter(fn($i) => $i->asistencias->count() > 0 && $i->estaEnRiesgo())
            ->sortBy(fn($i) => $i->calcularPorcentajeAsistencia());

        $comisiones = Comision::activas()->get();

        return view('asistencias.alertas', compact('alumnosEnRiesgo', 'comisiones', 'comisionId'));
    }
}
