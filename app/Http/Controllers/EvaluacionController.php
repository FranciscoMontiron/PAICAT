<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreEvaluacionRequest;
use App\Http\Requests\UpdateEvaluacionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Evaluacion;


class EvaluacionController extends Controller
{

      /**
     * Mostrar evaluaciones
     */
        public function index(): View
        {
            $evaluaciones = Evaluacion::orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('evaluaciones.index',compact('evaluaciones'));
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
   
}
