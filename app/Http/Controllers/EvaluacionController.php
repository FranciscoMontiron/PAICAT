<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreEvaluacionRequest;
use App\Http\Requests\UpdateEvaluacionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Evaluacion;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\Nota;
use App\Models\AcademicoDato;


class EvaluacionController extends Controller
{

      /**
     * Mostrar evaluaciones
     */
        public function index(): View
        {
            $evaluaciones = Evaluacion::orderBy('created_at', 'desc')
                ->paginate(15);

            $comisiones = Comision::orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('evaluaciones.index',compact('evaluaciones','comisiones'));
        }


       /**
     * Mostrar formulario para crear nueva evaluacion
     */
        public function create(): View
        {
    
            return view('evaluaciones.create');
        }

    /**
     * Guardar nueva evaluacion
     */
        public function store(StoreEvaluacionRequest $request): RedirectResponse
        {
            $data = $request->validated();
            // Crear Evaluacion
        $evaluacion = Evaluacion::create([
                'nombre' => $data['name'],
                'descripcion' => $data['descripcion'] ?? null,
                'tipo' => $data['tipo'],
                'fecha' => $data['fecha'],
                'peso_porcentual' => $data['porcentual'] ,
                'comision' => $data['comision'],
                #'comision_id' => $data['comision'],
                'anio' => $data['anio'],
            ]);

    
            return redirect()
                ->route('evaluaciones.index')
                ->with('success', 'Evaluacion creada exitosamente.');
        }

    /**
     * Editar Evaluacion- Formulario
     */
        public function edit(Evaluacion $evaluacion): View
        {
        
            return view('evaluaciones.edit',compact('evaluacion'));

        }

    /**
     * Editar Evaluacion- Formulario
     */
        public function update(UpdateEvaluacionRequest $request,Evaluacion $evaluacion): RedirectResponse
        {
             $data = $request->validated();

            $evaluacion->update([
                'nombre' => $data['name'],
                'descripcion' => $data['descripcion'] ?? null,
                'tipo' => $data['tipo'],
                'fecha' => $data['fecha'],
                'peso_porcentual' => $data['porcentual'] ,
                'comision_id' => $data['comision'] ?? null,
                #'comision_id' => $data['comision'],
                'anio' => $data['anio'],
            ]);

            return redirect()
            ->route('evaluaciones.index')
            ->with('success', 'Evalu actualizado exitosamente.');

        }


     /**
     * Eliminar Evaluacion (soft delete)
     */
    public function destroy(Evaluacion $evaluacion): RedirectResponse
    {
        $evaluacion->delete();

        return redirect()
            ->route('evaluaciones.index')
            ->with('success', 'Evaluacion eliminada exitosamente.');
    }

    
     /**
     * ESTA SECCION LE PERTENECE A NOTAS
     */
    



    public function indexnota(Comision $comision): View
        {
            //Traigo todas las inscripciones ya que contiene las comisiones, por lo que voy a buscar solo las notas pertenecientes a mi comision
            $inscripcion_comision= InscripcionComision::where('comision_id',$comision->id)
            ->pluck('id');

            //Filtro todas las notas pertenecientes a las inscripciones de la comision
            $notas = Nota::whereIn('inscripcion_comision_id',$inscripcion_comision)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

                
                
            return view('notas.index',compact('notas','comision'));
        }

           

    
    public function createnota(Comision $comision): View
        {                
                //Me traigo las incripciones de las comision para cargar la nota 
                $academico_datos= InscripcionComision::where('comision_id',$comision->id)
                ->where('estado','confirmado')
                ->pluck('academico_dato_id');

                $user_id=AcademicoDato::whereIn('id',$academico_datos)
                ->pluck('user_id');
                

                $evaluaciones = Evaluacion::orderBy('created_at', 'desc')
                ->paginate(15);

            return view('notas.create',compact('comision','evaluaciones'));
        }




   
}
