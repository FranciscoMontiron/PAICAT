<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Evaluacion;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\Inscripcion;
use App\Models\Nota;
use App\Models\AcademicoDato;
use App\Services\ConfiguracionService;
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
    //  Totales por estado (para gráfico de torta)
    $totales = (clone $baseQuery)
        ->select('a.estado', DB::raw('COUNT(DISTINCT a.id) as total'))
        ->groupBy('a.estado')
        ->pluck('total', 'a.estado');
    

    //  Tabla: Especialidad × Estado
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
    ->orderBy('a.id')
    ->paginate(10, ['*'], 'page_detalle')
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

    //Detalle alumno

        $asistenciasPorAlumno = (clone $baseQuery)
    ->join('alumnos_utn.persons as pers', 'pers.id', '=', 'i.person_id')
    ->join('paicat.sysacad_especialidades as sesp', 'sesp.id_sysacad', '=', 'i.especialidad_id_sysacad')
    ->join('paicat.comisiones as com', 'com.id', '=', 'ic.comision_id')
    ->join('paicat.materias as mat', 'mat.id', '=', 'a.materia_id')
    ->select(
        'pers.apellido',
        'pers.nombre',
        'sesp.nombre as especialidad',
        'com.nombre as comision',
        'mat.nombre as materia',

        DB::raw("SUM(CASE WHEN a.estado = 'presente' THEN 1 ELSE 0 END) as asistencias"),
        DB::raw("SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) as faltas"),
        DB::raw("SUM(CASE WHEN a.estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas"),
        DB::raw("SUM(CASE WHEN a.estado = 'justificado' THEN 1 ELSE 0 END) as justificados")
    )
    ->when($request->filled('search'), function ($q) use ($request) {
        $search = $request->search;

        $q->where(function ($q) use ($search) {
            $q->where('pers.apellido', 'like', "%{$search}%")
            ->orWhere('pers.nombre', 'like', "%{$search}%")
            ->orWhere('com.nombre', 'like', "%{$search}%")
            ->orWhere('mat.nombre', 'like', "%{$search}%");
        });
    })
    ->groupBy(
        'pers.apellido',
        'pers.nombre',
        'sesp.nombre',
        'com.nombre',
        'mat.nombre'
    )
    ->orderBy('pers.apellido')
    ->orderBy('pers.nombre')
    ->paginate(10, ['*'], 'page_alumnos')
    ->appends($request->query());

    //Fin detalle alumno


    $alumnosFiltro = (clone $baseQuery)
    ->join('alumnos_utn.persons as pers', 'pers.id', '=', 'i.person_id')
    ->select(
        'pers.id',
        DB::raw("CONCAT(pers.apellido, ', ', pers.nombre) as nombre")
    )
    ->distinct()
    ->orderBy('pers.apellido')
    ->orderBy('pers.nombre')
    ->get();

    return view(
        'reportes.reporteasistencias',
        compact(
            'materias',
            'comisiones',
            'especialidades',
            'totales',
            'especialidadesEstados',
            'asistenciadetalle',
            'asistenciasPorFecha',
            'asistenciasPorAlumno',
            'alumnosFiltro'
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
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) BETWEEN 17 AND 22 THEN 1 ELSE 0 END) as r17_22,
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) BETWEEN 23 AND 27 THEN 1 ELSE 0 END) as r23_27,
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) BETWEEN 28 AND 32 THEN 1 ELSE 0 END) as r28_32,
            SUM(CASE WHEN per.nacimiento_fecha IS NOT NULL AND TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) > 32 THEN 1 ELSE 0 END) as r32_mas,
            SUM(CASE WHEN per.nacimiento_fecha IS NULL OR TIMESTAMPDIFF(YEAR, per.nacimiento_fecha, CURDATE()) < 17 THEN 1 ELSE 0 END) as otros
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


public function reporterendimiento(Request $request)
{

    $notaMinimaRegular = intval(ConfiguracionService::get('nota_minima_regular', 4));

    $materias = DB::table('materias')
        ->orderBy('nombre')
        ->get();

    $comisiones = DB::table('comisiones')
        ->orderBy('nombre')
        ->get();

    $especialidades = DB::table('sysacad_especialidades')
        ->orderBy('nombre')
        ->get();

$tablaRendimiento = DB::table('paicat.notas as n')
    ->join('paicat.evaluaciones as e', 'n.evaluacion_id', '=', 'e.id')
    ->join('paicat.materias as mat', 'e.materia_id', '=', 'mat.id')
    ->join('paicat.inscripcion_comisiones as ic', 'ic.id', '=', 'n.inscripcion_comision_id')
    ->join('paicat.comisiones as c', 'c.id', '=', 'ic.comision_id')
    ->whereNotNull('n.nota')

    // filtros
    ->when($request->filled('materia_id'), fn ($q) =>
        $q->where('mat.id', $request->materia_id)
    )
    ->when($request->filled('comision_id'), fn ($q) =>
        $q->where('c.id', $request->comision_id)
    )
    ->when($request->filled('fecha_desde'), fn ($q) =>
        $q->whereDate('e.fecha', '>=', $request->fecha_desde)
    )
    ->when($request->filled('fecha_hasta'), fn ($q) =>
        $q->whereDate('e.fecha', '<=', $request->fecha_hasta)
    )

    ->select([
        'c.nombre as comision',
        'mat.nombre as materia',
        'e.nombre as parcial',
        'e.tipo',
        'e.fecha',
        DB::raw("SUM(CASE WHEN n.nota >= {$notaMinimaRegular} THEN 1 ELSE 0 END) as aprobados"),
        DB::raw("SUM(CASE WHEN n.nota < {$notaMinimaRegular} THEN 1 ELSE 0 END) as desaprobados"),
    ])

    ->groupBy(
        'c.nombre',
        'mat.nombre',
        'e.nombre',
        'e.tipo',
        'e.fecha'
    )

    ->orderBy('c.nombre')
    ->orderBy('e.fecha')
    ->get();

     $graficoPila = DB::table('paicat.notas as n')
    ->join('paicat.evaluaciones as e', 'n.evaluacion_id', '=', 'e.id')
    ->join('paicat.materias as mat', 'e.materia_id', '=', 'mat.id')
    ->join('paicat.inscripcion_comisiones as ic', 'ic.id', '=', 'n.inscripcion_comision_id')
    ->join('paicat.comisiones as c', 'c.id', '=', 'ic.comision_id')
    ->whereNotNull('n.nota')

    // mismos filtros que la tabla
    ->when($request->filled('materia_id'), fn ($q) =>
        $q->where('mat.id', $request->materia_id)
    )
    ->when($request->filled('comision_id'), fn ($q) =>
        $q->where('c.id', $request->comision_id)
    )
    ->when($request->filled('fecha_desde'), fn ($q) =>
        $q->whereDate('e.fecha', '>=', $request->fecha_desde)
    )
    ->when($request->filled('fecha_hasta'), fn ($q) =>
        $q->whereDate('e.fecha', '<=', $request->fecha_hasta)
    )

    ->select(
        'mat.nombre as materia',
        DB::raw("SUM(CASE WHEN n.nota >= {$notaMinimaRegular} THEN 1 ELSE 0 END) as aprobados"),
        DB::raw("SUM(CASE WHEN n.nota < {$notaMinimaRegular} THEN 1 ELSE 0 END) as desaprobados")
    )
    ->groupBy('mat.nombre')
    ->orderBy('mat.nombre')
    ->get();   

    $labelsMaterias = $graficoPila->pluck('materia');
    $dataAprobados = $graficoPila->pluck('aprobados')->map(fn($v) => (int) $v);
    $dataDesaprobados = $graficoPila->pluck('desaprobados')->map(fn($v) => (int) $v);

    


    return view('reportes.reportesrendimientos',compact('materias','comisiones','especialidades','tablaRendimiento','labelsMaterias','dataAprobados','dataDesaprobados'));



}


public function reportealumnos(Request $request){
    $alumnos = DB::table('alumnos_utn.persons as per')
    ->join('paicat.inscripciones as ins', 'per.id', '=', 'ins.person_id')
    ->join('paicat.inscripcion_comisiones as inscom', 'ins.id', '=', 'inscom.inscripcion_id')
    ->join('paicat.comisiones as com', 'inscom.comision_id', '=', 'com.id')
    ->select([
        'per.id as persona_id',
        'per.nombre',
        'per.apellido',
        'per.documento',

        'ins.id as inscripcion_id',
        'ins.anio_ingreso',

        'com.id as comision_id',
        'com.nombre as comision_nombre',
    ])
    ->when(request()->filled('comision_id'), function ($q) {
        $q->where('com.id', request('comision_id'));
    })
    ->paginate(15);

     $anio_ingreso = DB::table('paicat.inscripciones')
        ->select('anio_ingreso as anio_ingreso') 
        ->distinct()
        ->orderBy('anio_ingreso', 'desc')
        ->get();

    $comisiones = DB::table('comisiones')
    ->orderBy('nombre')
    ->get();

    return view('reportes.reportesalumnos',compact('alumnos','anio_ingreso','comisiones'));

}



public function reportealumnosdetalle(Request $request,$inscripcion_id){

$inscripcion = Inscripcion::find($inscripcion_id);

    if (!$inscripcion) {
        return redirect()
            ->back()
            ->with('error', 'El alumno no existe o fue eliminado');
    }

   $persona = DB::table('alumnos_utn.persons as per')
    ->join('paicat.inscripciones as ins', 'per.id', '=', 'ins.person_id')
    ->join('paicat.inscripcion_comisiones as inscom', 'ins.id', '=', 'inscom.inscripcion_id')
    ->join('paicat.comisiones as com', 'inscom.comision_id', '=', 'com.id')
    ->select(
        'per.id as persona_id',
        'per.nombre',
        'per.apellido',
        'per.documento',
        'ins.id as inscripcion_id',
        'ins.anio_ingreso',
        'com.id as comision_id',
        'com.nombre as comision_nombre'
    )
    ->where('ins.id', $inscripcion_id)
    ->first();

    //PANTALLA ASISTENCIAS
    $asistencias = DB::table('asistencias as a')
    ->join('inscripcion_comisiones as ic', 'a.inscripcion_comision_id', '=', 'ic.id')
    ->join('inscripciones as i', 'ic.inscripcion_id', '=', 'i.id')
    ->join('materias as m', 'a.materia_id', '=', 'm.id')
    ->where('i.id', $inscripcion_id)
    ->select([
        'a.fecha',
        'a.estado',
        'm.nombre as materia',
    ])
    ->when($request->filled('materia_id'), fn ($q) =>
        $q->where('m.id', $request->materia_id))
    ->orderBy('a.fecha', 'desc')
    ->paginate(10);

$resumenPorMateria = DB::table('asistencias as a')
    ->join('inscripcion_comisiones as ic', 'a.inscripcion_comision_id', '=', 'ic.id')
    ->join('inscripciones as i', 'ic.inscripcion_id', '=', 'i.id')
    ->join('paicat.materias as m', 'a.materia_id', '=', 'm.id')
    ->where('i.id', $inscripcion_id)
    ->select(
        'm.id as materia_id',
        'm.nombre as materia',

        DB::raw("COUNT(*) as total_clases"),

        DB::raw("SUM(CASE WHEN a.estado IN ('presente','tardanza','justificado') THEN 1 ELSE 0 END) as asistencias"),
        DB::raw("SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) as ausencias"),

        DB::raw("SUM(CASE WHEN a.estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas"),

        DB::raw("SUM(CASE WHEN a.estado = 'justificado' THEN 1 ELSE 0 END) as justificados"),

        DB::raw("ROUND((SUM(CASE WHEN a.estado IN ('presente','tardanza','justificado') THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2
            ) as porcentaje_asistencia
        ")
    )
    ->groupBy('m.id', 'm.nombre')
    ->orderBy('m.nombre')
    ->get();

    $resumenPorMateria = $resumenPorMateria->map(function ($row) {

        $row->estado = 'Activo';

        if ($row->porcentaje_asistencia < ConfiguracionService::get('asistencia_minima', 75)) {
            $row->estado = 'En riesgo';
        }

        return $row;
    });



    //FIN PANTALLA ASISTENCIAS




    $evaluaciones = DB::table('paicat.notas as n')
    ->join('paicat.inscripciones as ins', 'ins.id', '=', 'n.inscripcion_id')
    ->join('paicat.evaluaciones as ev', 'ev.id', '=', 'n.evaluacion_id')
    ->join('paicat.materias as m', 'm.id', '=', 'ev.materia_id')
    ->where('ins.id', $inscripcion_id)
    ->select([
        'ev.fecha',
        'ev.nombre as evaluacion_nombre',
        'ev.tipo',
        'm.nombre as materia_nombre',
        'n.nota',
    ])
    ->orderBy('ev.fecha', 'desc')
    ->paginate(10);

$materias = DB::table('materias')
        ->orderBy('nombre')
        ->get();

    
return view('reportes.showreportealumno',compact('persona','asistencias','evaluaciones','resumenPorMateria','materias'));

}


public function reporteDesercion(Request $request)
{
    $diasLimite = $request->input('dias', 14);

    $comisiones = DB::table('comisiones')->orderBy('nombre')->get();
    $especialidades = DB::table('sysacad_especialidades')->orderBy('nombre')->get();

    $query = DB::table('inscripcion_comisiones as ic')
        ->join('inscripciones as i', 'ic.inscripcion_id', '=', 'i.id')
        ->join('alumnos_utn.persons as pers', 'pers.id', '=', 'i.person_id')
        ->join('paicat.comisiones as com', 'com.id', '=', 'ic.comision_id')
        ->join('paicat.sysacad_especialidades as espe', 'espe.id_sysacad', '=', 'i.especialidad_id_sysacad')
        ->leftJoin(DB::raw('(SELECT inscripcion_comision_id, MAX(fecha) as ultima_fecha FROM asistencias GROUP BY inscripcion_comision_id) as ult'), 'ult.inscripcion_comision_id', '=', 'ic.id')
        ->where('com.estado', 'activa')
        ->where(function ($q) use ($diasLimite) {
            $q->whereNull('ult.ultima_fecha')
              ->orWhereRaw('DATEDIFF(CURDATE(), ult.ultima_fecha) >= ?', [$diasLimite]);
        })
        ->when($request->filled('comision_id'), fn ($q) =>
            $q->where('com.id', $request->comision_id)
        )
        ->when($request->filled('especialidad_id'), fn ($q) =>
            $q->where('i.especialidad_id_sysacad', $request->especialidad_id)
        )
        ->select([
            'pers.apellido',
            'pers.nombre',
            'pers.documento',
            'com.nombre as comision',
            'espe.nombre as especialidad',
            'ult.ultima_fecha',
            DB::raw('DATEDIFF(CURDATE(), ult.ultima_fecha) as dias_ausente'),
        ])
        ->orderByRaw('ult.ultima_fecha IS NULL DESC')
        ->orderBy('ult.ultima_fecha')
        ->paginate(20)
        ->appends($request->query());

    // Stats
    $totalDesertores = DB::table('inscripcion_comisiones as ic')
        ->join('paicat.comisiones as com', 'com.id', '=', 'ic.comision_id')
        ->leftJoin(DB::raw('(SELECT inscripcion_comision_id, MAX(fecha) as ultima_fecha FROM asistencias GROUP BY inscripcion_comision_id) as ult'), 'ult.inscripcion_comision_id', '=', 'ic.id')
        ->where('com.estado', 'activa')
        ->where(function ($q) use ($diasLimite) {
            $q->whereNull('ult.ultima_fecha')
              ->orWhereRaw('DATEDIFF(CURDATE(), ult.ultima_fecha) >= ?', [$diasLimite]);
        })
        ->count();

    $nuncaAsistieron = DB::table('inscripcion_comisiones as ic')
        ->join('paicat.comisiones as com', 'com.id', '=', 'ic.comision_id')
        ->leftJoin(DB::raw('(SELECT DISTINCT inscripcion_comision_id FROM asistencias) as asist'), 'asist.inscripcion_comision_id', '=', 'ic.id')
        ->where('com.estado', 'activa')
        ->whereNull('asist.inscripcion_comision_id')
        ->count();

    $stats = [
        'total_desertores' => $totalDesertores,
        'nunca_asistieron' => $nuncaAsistieron,
        'dias_limite' => $diasLimite,
    ];

    return view('reportes.desercion', compact('query', 'comisiones', 'especialidades', 'stats'));
}


public function reporteResumenComisiones(Request $request)
{
    $asistenciaMinima = ConfiguracionService::get('asistencia_minima', 75);
    $notaMinima = intval(ConfiguracionService::get('nota_minima_regular', 4));

    $comisiones = DB::table('comisiones as c')
        ->leftJoin('inscripcion_comisiones as ic', 'ic.comision_id', '=', 'c.id')
        ->leftJoin(DB::raw("(
            SELECT ic2.comision_id,
                COUNT(DISTINCT a.id) as total_asistencias,
                SUM(CASE WHEN a.estado IN ('presente','tardanza','justificado') THEN 1 ELSE 0 END) as asistencias_ok
            FROM asistencias a
            JOIN inscripcion_comisiones ic2 ON ic2.id = a.inscripcion_comision_id
            GROUP BY ic2.comision_id
        ) as asist"), 'asist.comision_id', '=', 'c.id')
        ->leftJoin(DB::raw("(
            SELECT ic3.comision_id,
                COUNT(DISTINCT n.id) as total_notas,
                SUM(CASE WHEN n.nota >= {$notaMinima} THEN 1 ELSE 0 END) as aprobados
            FROM notas n
            JOIN inscripcion_comisiones ic3 ON ic3.id = n.inscripcion_comision_id
            WHERE n.nota IS NOT NULL
            GROUP BY ic3.comision_id
        ) as notas"), 'notas.comision_id', '=', 'c.id')
        ->where('c.estado', 'activa')
        ->when($request->filled('periodo'), fn ($q) =>
            $q->where('c.periodo', $request->periodo)
        )
        ->when($request->filled('anio'), fn ($q) =>
            $q->where('c.anio', $request->anio)
        )
        ->select([
            'c.id',
            'c.nombre',
            'c.turno',
            'c.modalidad',
            'c.cupo_maximo',
            DB::raw('COUNT(DISTINCT ic.id) as alumnos'),
            DB::raw('COALESCE(asist.total_asistencias, 0) as total_asistencias'),
            DB::raw('COALESCE(asist.asistencias_ok, 0) as asistencias_ok'),
            DB::raw('COALESCE(notas.total_notas, 0) as total_notas'),
            DB::raw('COALESCE(notas.aprobados, 0) as aprobados'),
        ])
        ->groupBy('c.id', 'c.nombre', 'c.turno', 'c.modalidad', 'c.cupo_maximo',
                  'asist.total_asistencias', 'asist.asistencias_ok',
                  'notas.total_notas', 'notas.aprobados')
        ->orderBy('c.nombre')
        ->get()
        ->map(function ($c) {
            $c->pct_asistencia = $c->total_asistencias > 0
                ? round(($c->asistencias_ok / $c->total_asistencias) * 100, 1)
                : null;
            $c->pct_aprobacion = $c->total_notas > 0
                ? round(($c->aprobados / $c->total_notas) * 100, 1)
                : null;
            $c->pct_ocupacion = $c->cupo_maximo > 0
                ? round(($c->alumnos / $c->cupo_maximo) * 100, 1)
                : null;
            return $c;
        });

    $anios = DB::table('comisiones')->select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio');
    $periodos = DB::table('comisiones')->select('periodo')->distinct()->orderBy('periodo')->pluck('periodo');

    return view('reportes.resumen-comisiones', compact('comisiones', 'anios', 'periodos'));
}

}
