<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Mostrar listado de usuarios
     */
    public function index(): View
    {
        $usuarios = User::with('roles')
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Mostrar formulario para crear nuevo usuario
     */
    public function create(): View
    {
        $roles = Role::orderBy('nombre')->get();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Crear usuario
        $usuario = User::create([
            'name' => $data['name'],
            'apellido' => $data['apellido'],
            'dni' => $data['dni'] ?? null,
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'password' => $data['password'],
            'estado' => $data['estado'],
        ]);

        // Asignar roles
        if (!empty($data['roles'])) {
            $usuario->roles()->sync($data['roles']);
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar detalle de un usuario
     */
    public function show(User $usuario): View
    {
        $usuario->load('roles.permissions');
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function edit(User $usuario): View
    {
        $roles = Role::orderBy('nombre')->get();
        $usuario->load('roles');
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualizar usuario existente
     */
    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $data = $request->validated();

        // Actualizar datos del usuario
        $usuario->update([
            'name' => $data['name'],
            'apellido' => $data['apellido'],
            'dni' => $data['dni'] ?? null,
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'estado' => $data['estado'],
        ]);

        // Actualizar contraseña solo si se proporcionó
        if (!empty($data['password'])) {
            $usuario->update(['password' => $data['password']]);
        }

        // Sincronizar roles
        if (isset($data['roles'])) {
            $usuario->roles()->sync($data['roles']);
        } else {
            $usuario->roles()->sync([]);
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function destroy(User $usuario): RedirectResponse
    {
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Restaurar usuario eliminado
     */
    public function restore(int $id): RedirectResponse
    {
        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario restaurado exitosamente.');
    }
}
