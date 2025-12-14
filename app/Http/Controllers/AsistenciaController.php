<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Asistencia;
use App\Models\InscripcionComision;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index()
    {
        $comisiones = Comision::activas()->withCount(['inscripciones' => fn($q) => $q->where('estado','confirmada')])->get();
        return view('asistencias.index', compact('comisiones'));
    }

    public function verComision(Comision $comision)
    {
        $comision->load(['inscripciones' => fn($q) => $q->where('estado','confirmada')->with('academicoDato.user'),'inscripciones.asistencias']);
        $totalAlumnos = $comision->inscripciones->count();
        $promedioAsistencia = $comision->inscripciones->avg(fn($i) => $i->calcularPorcentajeAsistencia());
        $alumnosEnRiesgo = $comision->inscripciones->filter(fn($i) => $i->estaEnRiesgo())->count();

        return view('asistencias.comision', compact('comision','totalAlumnos','promedioAsistencia','alumnosEnRiesgo'));
    }

    public function mostrarRegistro(Request $request, Comision $comision)
    {
        $inscripciones = $comision->inscripciones()->where('estado','confirmada')->with('academicoDato.user')->get();
        $fecha = $request->input('fecha', today()->format('Y-m-d'));
        $asistenciasExistentes = Asistencia::whereIn('inscripcion_comision_id',$inscripciones->pluck('id'))->whereDate('fecha',$fecha)->get()->keyBy('inscripcion_comision_id');

        return view('asistencias.registrar', compact('comision','inscripciones','fecha','asistenciasExistentes'));
    }

    public function guardarRegistro(Request $request, Comision $comision)
    {
        $validated = $request->validate([
            'fecha' => 'required|date|before_or_equal:today',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_comision_id' => 'required|exists:inscripcion_comisiones,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500',
        ]);

        foreach($validated['asistencias'] as $a){
            Asistencia::updateOrCreate(
                ['inscripcion_comision_id'=>$a['inscripcion_comision_id'],'fecha'=>$validated['fecha']],
                ['estado'=>$a['estado'],'observaciones'=>$a['observaciones']??null,'registrado_por'=>auth()->id()]
            );
        }

        return redirect()->route('asistencias.registrar',$comision->id)->with('success','Asistencias registradas correctamente');
    }

    public function mostrarJustificacion(Comision $comision)
    {
        $alumnos = $comision->inscripciones()->where('estado','confirmada')->with('academicoDato.user')->get();
        return view('asistencias.justificar', compact('comision','alumnos'));
    }

    public function guardarJustificacion(Request $request, Comision $comision)
    {
        $validated = $request->validate([
            'inscripcion_comision_id'=>'required|exists:inscripcion_comisiones,id',
            'fecha'=>'required|date|before_or_equal:today',
            'motivo'=>'required|string|max:500',
            'archivo'=>'nullable|file|max:2048'
        ]);

        $asistencia = Asistencia::updateOrCreate(
            ['inscripcion_comision_id'=>$validated['inscripcion_comision_id'],'fecha'=>$validated['fecha']],
            ['estado'=>'justificado','observaciones'=>$validated['motivo'],'registrado_por'=>auth()->id()]
        );

        if($request->hasFile('archivo')){
            $asistencia->archivo = $request->file('archivo')->store('justificaciones');
            $asistencia->save();
        }

        return redirect()->route('asistencias.comision',$comision->id)->with('success','Justificación registrada correctamente');
    }

    public function historial(InscripcionComision $inscripcionComision)
    {
        $inscripcionComision->load(['asistencias'=>fn($q)=>$q->orderBy('fecha','desc'),'academicoDato.user','comision']);
        $porcentaje = $inscripcionComision->calcularPorcentajeAsistencia();
        $estaEnRiesgo = $inscripcionComision->estaEnRiesgo();
        $estadisticas = [
            'total'=>$inscripcionComision->asistencias->count(),
            'presentes'=>$inscripcionComision->asistencias->where('estado','presente')->count(),
            'ausentes'=>$inscripcionComision->asistencias->where('estado','ausente')->count(),
            'tardanzas'=>$inscripcionComision->asistencias->where('estado','tardanza')->count(),
            'justificadas'=>$inscripcionComision->asistencias->where('estado','justificado')->count(),
        ];

        return view('asistencias.historial', compact('inscripcionComision','porcentaje','estaEnRiesgo','estadisticas'));
    }

    public function alertas(Request $request)
    {
        $comisionId = $request->input('comision_id');
        $query = InscripcionComision::with(['academicoDato.user','comision','asistencias'])->where('estado','confirmada');
        if($comisionId) $query->where('comision_id',$comisionId);

        $inscripciones = $query->get();
        $alumnosEnRiesgo = $inscripciones->filter(fn($i)=>$i->asistencias->count()>0 && $i->estaEnRiesgo())
            ->sortBy(fn($i)=>$i->calcularPorcentajeAsistencia());

        $comisiones = Comision::activas()->get();

        return view('asistencias.alertas', compact('alumnosEnRiesgo','comisiones','comisionId'));
    }
}
