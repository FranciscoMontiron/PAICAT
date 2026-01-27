<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Evaluacion;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\Nota;
use App\Models\AcademicoDato;
use Illuminate\Support\Facades\DB;


class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }


public function reporteasistenciascomision(Request $request)
{
  
    $materias = DB::table('materias')
        ->orderBy('nombre')
        ->get();

    $comisiones = DB::table('comisiones')
        ->orderBy('nombre')
        ->get();

    $especialidades = DB::table('sysacad_especialidades')
        ->orderBy('nombre')
        ->get();

    
    $baseQuery = DB::table('asistencias as a')
        ->join('inscripcion_comisiones as ic', 'a.inscripcion_comision_id', '=', 'ic.id')
        ->join('inscripciones as i', 'ic.inscripcion_id', '=', 'i.id')

        // 👉 Filtros
        ->when($request->filled('materia_id'), fn ($q) =>
            $q->where('a.materia_id', $request->materia_id)
        )
        ->when($request->filled('comision_id'), fn ($q) =>
            $q->where('ic.comision_id', $request->comision_id)
        )
        ->when($request->filled('estado'), fn ($q) =>
            $q->where('a.estado', $request->estado)
        )
        ->when($request->filled('especialidad_id'), fn ($q) =>
            $q->where('i.especialidad_id_sysacad', $request->especialidad_id)
        )
        ->when($request->filled('fecha_desde'), fn ($q) =>
            $q->whereDate('a.fecha', '>=', $request->fecha_desde)
        )
        ->when($request->filled('fecha_hasta'), fn ($q) =>
            $q->whereDate('a.fecha', '<=', $request->fecha_hasta)
        );

    // 🔹 Totales por estado (para gráfico de torta)
    $totales = (clone $baseQuery)
        ->select('a.estado', DB::raw('COUNT(DISTINCT a.id) as total'))
        ->groupBy('a.estado')
        ->pluck('total', 'a.estado');
    

    // 🔹 Tabla: Especialidad × Estado
    $especialidadesEstados = (clone $baseQuery)
        ->join(
            'sysacad_especialidades as sesp',
            'i.especialidad_id_sysacad',
            '=',
            'sesp.id_sysacad'
        )
        ->select(
            'sesp.nombre as especialidad',
            'a.estado',
            DB::raw('COUNT(DISTINCT a.id) as total')
        )
        ->groupBy('sesp.nombre', 'a.estado')
        ->get()
        ->groupBy('especialidad');

    return view(
        'reportes.reporteasistencias',
        compact(
            'materias',
            'comisiones',
            'especialidades',
            'totales',
            'especialidadesEstados'
        )
    );
}


}
