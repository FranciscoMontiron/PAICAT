<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarDocumentacionRequest extends FormRequest
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
            'doc_dni_validado' => 'nullable|boolean',
            'doc_titulo_validado' => 'nullable|boolean',
            'doc_analitico_validado' => 'nullable|boolean',
            'observaciones_documentacion' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Preparar los datos para la validación.
     * Convierte los checkboxes no marcados en false
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'doc_dni_validado' => $this->boolean('doc_dni_validado'),
            'doc_titulo_validado' => $this->boolean('doc_titulo_validado'),
            'doc_analitico_validado' => $this->boolean('doc_analitico_validado'),
        ]);
    }

    /**
     * Obtener nombres de atributos personalizados para errores del validador.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'doc_dni_validado' => 'validación de DNI',
            'doc_titulo_validado' => 'validación de título',
            'doc_analitico_validado' => 'validación de analítico',
            'observaciones_documentacion' => 'observaciones de documentación',
        ];
    }
}
