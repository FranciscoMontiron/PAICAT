<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('nombre')->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('slug')->get()->groupBy(function ($p) {
            return Str::before($p->slug, '.');
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'nombre' => $data['nombre'],
            'slug' => Str::slug($data['nombre']),
            'descripcion' => $data['descripcion'] ?? null,
            'solo_contenido_asignado' => $request->boolean('solo_contenido_asignado'),
            'visibilidad_general' => $request->boolean('visibilidad_general'),
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function show(Role $role): View
    {
        $role->load(['permissions', 'users']);

        $permissionsByModule = $role->permissions->groupBy(function ($p) {
            return Str::before($p->slug, '.');
        });

        return view('roles.show', compact('role', 'permissionsByModule'));
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        $permissions = Permission::orderBy('slug')->get()->groupBy(function ($p) {
            return Str::before($p->slug, '.');
        });

        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('roles', 'nombre')->ignore($role->id)],
            'descripcion' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'nombre' => $data['nombre'],
            'slug' => Str::slug($data['nombre']),
            'descripcion' => $data['descripcion'] ?? null,
            'solo_contenido_asignado' => $request->boolean('solo_contenido_asignado'),
            'visibilidad_general' => $request->boolean('visibilidad_general'),
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente.');
    }

    /**
     * Sincronizar permisos: asegura que todos los permisos definidos en el sistema existan en la BD.
     */
    public function syncPermissions(): RedirectResponse
    {
        $definedPermissions = [
            ['nombre' => 'Ver usuarios', 'slug' => 'usuarios.ver', 'descripcion' => 'Puede ver la lista de usuarios'],
            ['nombre' => 'Crear usuarios', 'slug' => 'usuarios.crear', 'descripcion' => 'Puede crear nuevos usuarios'],
            ['nombre' => 'Editar usuarios', 'slug' => 'usuarios.editar', 'descripcion' => 'Puede editar usuarios existentes'],
            ['nombre' => 'Eliminar usuarios', 'slug' => 'usuarios.eliminar', 'descripcion' => 'Puede eliminar usuarios'],
            ['nombre' => 'Ver inscripciones', 'slug' => 'inscripciones.ver', 'descripcion' => 'Puede ver inscripciones'],
            ['nombre' => 'Crear inscripciones', 'slug' => 'inscripciones.crear', 'descripcion' => 'Puede crear inscripciones'],
            ['nombre' => 'Editar inscripciones', 'slug' => 'inscripciones.editar', 'descripcion' => 'Puede editar inscripciones'],
            ['nombre' => 'Eliminar inscripciones', 'slug' => 'inscripciones.eliminar', 'descripcion' => 'Puede eliminar inscripciones'],
            ['nombre' => 'Ver comisiones', 'slug' => 'comisiones.ver', 'descripcion' => 'Puede ver comisiones'],
            ['nombre' => 'Crear comisiones', 'slug' => 'comisiones.crear', 'descripcion' => 'Puede crear comisiones'],
            ['nombre' => 'Editar comisiones', 'slug' => 'comisiones.editar', 'descripcion' => 'Puede editar comisiones'],
            ['nombre' => 'Eliminar comisiones', 'slug' => 'comisiones.eliminar', 'descripcion' => 'Puede eliminar comisiones'],
            ['nombre' => 'Ver asistencias', 'slug' => 'asistencias.ver', 'descripcion' => 'Puede ver asistencias'],
            ['nombre' => 'Crear asistencias', 'slug' => 'asistencias.crear', 'descripcion' => 'Puede registrar asistencias'],
            ['nombre' => 'Editar asistencias', 'slug' => 'asistencias.editar', 'descripcion' => 'Puede editar asistencias'],
            ['nombre' => 'Eliminar asistencias', 'slug' => 'asistencias.eliminar', 'descripcion' => 'Puede eliminar asistencias'],
            ['nombre' => 'Ver evaluaciones', 'slug' => 'evaluaciones.ver', 'descripcion' => 'Puede ver evaluaciones'],
            ['nombre' => 'Crear evaluaciones', 'slug' => 'evaluaciones.crear', 'descripcion' => 'Puede crear evaluaciones'],
            ['nombre' => 'Editar evaluaciones', 'slug' => 'evaluaciones.editar', 'descripcion' => 'Puede editar evaluaciones'],
            ['nombre' => 'Eliminar evaluaciones', 'slug' => 'evaluaciones.eliminar', 'descripcion' => 'Puede eliminar evaluaciones'],
            ['nombre' => 'Ver reportes', 'slug' => 'reportes.ver', 'descripcion' => 'Puede ver reportes'],
            ['nombre' => 'Generar reportes', 'slug' => 'reportes.generar', 'descripcion' => 'Puede generar reportes'],
            ['nombre' => 'Gestionar roles', 'slug' => 'roles.gestionar', 'descripcion' => 'Puede gestionar roles y permisos'],
            ['nombre' => 'Solicitar cambio de comisión', 'slug' => 'solicitud-cambio.ver', 'descripcion' => 'Puede solicitar cambio de comisión para alumnos desde el acceso rápido'],
        ];

        $created = 0;
        foreach ($definedPermissions as $perm) {
            $exists = Permission::where('slug', $perm['slug'])->exists();
            if (!$exists) {
                Permission::create($perm);
                $created++;
            }
        }

        $msg = $created > 0
            ? "Se sincronizaron {$created} permiso(s) nuevo(s)."
            : "Todos los permisos ya estaban sincronizados.";

        return redirect()->route('roles.index')->with('success', $msg);
    }
}
