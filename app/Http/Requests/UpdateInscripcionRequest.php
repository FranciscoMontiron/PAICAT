<?php

namespace App\Http\Requests;

use App\Models\Inscripcion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInscripcionRequest extends FormRequest
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
            'especialidad_id_sysacad' => 'required|integer',
            'especialidad_alternativa_id_sysacad' => 'nullable|integer',
            'modalidad' => ['required', Rule::in(array_keys(Inscripcion::MODALIDADES))],
            'turno_ingreso' => 'nullable|string|max:50',
            'turno_carrera' => 'nullable|string|max:50',
            'tipo_ingreso' => ['required', Rule::in(array_keys(Inscripcion::TIPOS_INGRESO))],
            'sede_id_sysacad' => 'nullable|integer',
            'estado' => ['nullable', Rule::in(array_keys(Inscripcion::ESTADOS))],
            'observaciones' => 'nullable|string|max:1000',
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
            'especialidad_id_sysacad' => 'especialidad',
            'especialidad_alternativa_id_sysacad' => 'especialidad alternativa',
            'modalidad' => 'modalidad',
            'turno_ingreso' => 'turno de ingreso',
            'turno_carrera' => 'turno de carrera',
            'tipo_ingreso' => 'tipo de ingreso',
            'sede_id_sysacad' => 'sede',
            'estado' => 'estado',
            'observaciones' => 'observaciones',
        ];
    }
}
