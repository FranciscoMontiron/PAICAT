<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluacionRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para hacer esta solicitud.
     */

        public function authorize(): bool
        {
            return true;
        }

     /**
     * Obtener las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
        {
            return [
                'name' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',
                'tipo' => 'required|in:parcial,recuperatorio,examen_final',
                'fecha' => 'required|date',
                'porcentual' => 'required|numeric|min:0|max:100',
                'comision' => 'nullable|integer|min:1|max:20',
                'anio' => 'required|integer|min:1900|max:2100',
            ];
        }
    /**
     * Obtener nombres de atributos personalizados para errores del validador.
     *
     * @return array<string, string>
     */
        public function attributes(): array
        {
            return [
                'name' => 'nombre',
                'descripcion' => 'descripcion',
                'tipo' => 'tipo',
                'fecha' => 'fecha',
                'porcentual' => 'porcentual',
                'comision' => 'comision',
                'anio' => 'anio',
            ];
        }

}