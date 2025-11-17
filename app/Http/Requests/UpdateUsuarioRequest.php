<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
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
        $userId = $this->route('usuario')->id;

        return [
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20|unique:users,dni,' . $userId,
            'email' => 'required|email|unique:users,email,' . $userId,
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'estado' => 'required|in:activo,inactivo,suspendido',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
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
            'apellido' => 'apellido',
            'dni' => 'DNI',
            'email' => 'correo electrónico',
            'telefono' => 'teléfono',
            'password' => 'contraseña',
            'estado' => 'estado',
            'roles' => 'roles',
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
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        ];
    }
}
