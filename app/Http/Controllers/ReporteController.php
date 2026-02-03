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
    //Seccion para informacion general
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


    // Fin Seccion para informacion general
    
    //Seccion para informacion detalle

        /*
       $asistenciadetalle = (clone $baseQuery)
    ->join('alumnos_utn.persons as pers', 'pers.id', '=', 'i.person_id')
    ->join('paicat.sysacad_especialidades as sesp', 'sesp.id_sysacad', '=', 'i.especialidad_id_sysacad')
    ->join('paicat.comisiones as com', 'com.id', '=', 'ic.comision_id')
    ->join('paicat.materias as mat', 'mat.id', '=', 'a.materia_id')
    ->select([
        'a.fecha',
        'a.materia_id',
        'mat.nombre as materia_nombre',
        'a.estado',
        'ic.comision_id',
        'com.nombre as comision_nombre',
        'com.turno',
        'i.especialidad_id_sysacad',
        'pers.nombre',
        'pers.apellido',
        'sesp.nombre as especialidad_nombre',
    ])
    ->paginate(10)
    ->appends(['vista' => 'detalle']);
*/
    $asistenciadetallefiltro = (clone $baseQuery)
    ->join('alumnos_utn.persons as pers', 'pers.id', '=', 'i.person_id')
    ->join('paicat.sysacad_especialidades as sesp', 'sesp.id_sysacad', '=', 'i.especialidad_id_sysacad')
    ->join('paicat.comisiones as com', 'com.id', '=', 'ic.comision_id')
    ->join('paicat.materias as mat', 'mat.id', '=', 'a.materia_id')
    ->select([
        'a.fecha',
        'a.materia_id',
        'mat.nombre as materia_nombre',
        'a.estado',
        'ic.comision_id',
        'com.nombre as comision_nombre',
        'com.turno',
        'i.especialidad_id_sysacad',
        'pers.nombre',
        'pers.apellido',
        'sesp.nombre as especialidad_nombre',
    ]);
    $asistenciadetalle = (clone $asistenciadetallefiltro)
    ->orderBy('a.fecha')
    ->paginate(10)
    ->appends(['vista' => 'detalle']);
    
    $asistenciasPorFecha = (clone $asistenciadetallefiltro)
    ->select(
        'a.fecha',
        DB::raw("COUNT(*) AS total"),
        DB::raw("COUNT(CASE WHEN a.estado = 'presente' THEN 1 END) AS presentes"),
        DB::raw("COUNT(CASE WHEN a.estado = 'ausente' THEN 1 END) AS ausentes"),
        DB::raw("COUNT(CASE WHEN a.estado = 'tardanza' THEN 1 END) AS tardanzas"),
        DB::raw("COUNT(CASE WHEN a.estado = 'justificado' THEN 1 END) AS justificados")
    )
    ->groupBy('a.fecha')
    ->orderBy('a.fecha')
    ->get();


    //Fin detalle


    return view(
        'reportes.reporteasistencias',
        compact(
            'materias',
            'comisiones',
            'especialidades',
            'totales',
            'especialidadesEstados',
            'asistenciadetalle',
            'asistenciasPorFecha'
        )
    );
}

public function reporteinscriones(Request $request)
{
    $datos = DB::table('paicat.inscripciones as ins')
    ->join('alumnos_utn.persons as per', 'per.id', '=', 'ins.person_id')
    ->join('paicat.sysacad_especialidades as espe', 'espe.id_sysacad', '=', 'ins.especialidad_id_sysacad')
    ->join('paicat.sysacad_generos as gen', 'gen.id_sysacad', '=', 'per.genero')
    ->select([
        'ins.anio_ingreso',
        'espe.nombre as especialidad',
        'gen.id as genero_id',
        'gen.nombre as genero_nombre',
        'ins.modalidad',
        DB::raw('TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) as edad')
    ]);

    
    //Datos para modalidad
        $modalidades = (clone $datos)
        ->select('ins.modalidad')
        ->distinct()
        ->pluck('modalidad');

    $tot_modalidad = (clone $datos)
        ->select(
            'ins.modalidad',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('ins.modalidad')
        ->get();
        $labelsModalidad = $tot_modalidad->pluck('modalidad');
        $totalesModalidad = $tot_modalidad->pluck('total');   

            //Resumen modalidad
            $resumenCarreraModalidad = (clone $datos)
            ->select(
                'espe.nombre as especialidad',
                'ins.modalidad',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('espe.nombre', 'ins.modalidad')
            ->get()
            ->groupBy('especialidad');


     //datos para especialidad
        $especialidades = (clone $datos)
        ->select(
            'espe.nombre as especialidad',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('especialidad')
        ->get();

        $labelsEspecialidad  = $especialidades->pluck('especialidad');
        $totalesEspecialidad = $especialidades->pluck('total');

        //cantidad de personas por genero y especialidad
            $resumenEspecialidadGenero = (clone $datos)
            ->select(
                'espe.nombre as especialidad',
                'gen.nombre as genero',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('espe.nombre', 'gen.nombre')
            ->get()
            ->groupBy('especialidad');

         $generos = (clone $datos)
        ->select('gen.nombre')
        ->distinct()
        ->pluck('gen.nombre');  

        //cantidad or Rango-especialidad

      $edadPorEspecialidadRango = (clone $datos)
    ->select(
        'espe.nombre as especialidad',
        DB::raw("
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) BETWEEN 18 AND 22 THEN 1 ELSE 0 END) as r18_22,
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) BETWEEN 23 AND 27 THEN 1 ELSE 0 END) as r23_27,
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) BETWEEN 28 AND 32 THEN 1 ELSE 0 END) as r28_32,
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) > 32 THEN 1 ELSE 0 END) as r32_mas,
            SUM(CASE WHEN per.nacimiento_fecha IS NULL OR TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) < 18 THEN 1 ELSE 0 END) as otros
        ")
    )
    ->groupBy('espe.nombre')
    ->orderBy('espe.nombre')
    ->get();


    //Años de ingresos
        $anio_ingreso = DB::table('paicat.inscripciones')
        ->select('anio_ingreso as anio_ingreso') 
        ->when($request->filled('materia_id'), fn ($q) =>
            $q->where('anio_ingreso', $request->anio_ingreso)
        )
        ->distinct()
        ->orderBy('anio_ingreso', 'desc')
        ->get();

    return view('reportes.reporteinscripciones',compact('labelsModalidad','totalesModalidad','labelsEspecialidad','totalesEspecialidad',
    'modalidades','resumenCarreraModalidad','resumenEspecialidadGenero','generos','edadPorEspecialidadRango','anio_ingreso'));

}


}
