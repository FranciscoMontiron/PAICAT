<?php

namespace App\Http\Requests;

use App\Models\Inscripcion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInscripcionRequest extends FormRequest
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
            'person_id' => [
                'required',
                'integer',
            ],
            'academico_dato_id' => 'nullable|integer',
            'anio_ingreso' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'especialidad_id_sysacad' => 'required|integer',
            'especialidad_alternativa_id_sysacad' => 'nullable|integer',
            'modalidad' => ['required', Rule::in(array_keys(Inscripcion::MODALIDADES))],
            'turno_ingreso' => 'nullable|string|max:50',
            'turno_carrera' => 'nullable|string|max:50',
            'tipo_ingreso' => ['required', Rule::in(array_keys(Inscripcion::TIPOS_INGRESO))],
            'sede_id_sysacad' => 'nullable|integer',
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
            'person_id' => 'alumno',
            'academico_dato_id' => 'datos académicos',
            'anio_ingreso' => 'año de ingreso',
            'especialidad_id_sysacad' => 'especialidad',
            'especialidad_alternativa_id_sysacad' => 'especialidad alternativa',
            'modalidad' => 'modalidad',
            'turno_ingreso' => 'turno de ingreso',
            'turno_carrera' => 'turno de carrera',
            'tipo_ingreso' => 'tipo de ingreso',
            'sede_id_sysacad' => 'sede',
            'observaciones' => 'observaciones',
        ];
    }

    /**
     * Obtener mensajes personalizados para errores del validador.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'person_id.required' => 'Debe seleccionar un alumno.',
            'anio_ingreso.required' => 'El año de ingreso es obligatorio.',
            'especialidad_id_sysacad.required' => 'Debe seleccionar una especialidad.',
            'modalidad.required' => 'Debe seleccionar una modalidad.',
            'tipo_ingreso.required' => 'Debe seleccionar un tipo de ingreso.',
        ];
    }

    /**
     * Preparar los datos para la validación.
     */
    protected function prepareForValidation(): void
    {
        // Si no se proporciona año de ingreso, usar el actual
        if (!$this->has('anio_ingreso') || empty($this->anio_ingreso)) {
            $this->merge([
                'anio_ingreso' => date('Y'),
            ]);
        }
    }
}
