<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Ejecutar los seeders de la base de datos.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = $this->createPermissions();

        // Crear roles
        $roles = $this->createRoles();

        // Asignar permisos a roles
        $this->assignPermissionsToRoles($roles, $permissions);

        $this->command->info('Roles y permisos creados exitosamente!');
    }

    private function createPermissions(): array
    {
        $permissionsData = [
            // Usuarios
            ['nombre' => 'Ver usuarios', 'slug' => 'usuarios.ver', 'descripcion' => 'Puede ver la lista de usuarios'],
            ['nombre' => 'Crear usuarios', 'slug' => 'usuarios.crear', 'descripcion' => 'Puede crear nuevos usuarios'],
            ['nombre' => 'Editar usuarios', 'slug' => 'usuarios.editar', 'descripcion' => 'Puede editar usuarios existentes'],
            ['nombre' => 'Eliminar usuarios', 'slug' => 'usuarios.eliminar', 'descripcion' => 'Puede eliminar usuarios'],

            // Inscripciones
            ['nombre' => 'Ver inscripciones', 'slug' => 'inscripciones.ver', 'descripcion' => 'Puede ver inscripciones'],
            ['nombre' => 'Crear inscripciones', 'slug' => 'inscripciones.crear', 'descripcion' => 'Puede crear inscripciones'],
            ['nombre' => 'Editar inscripciones', 'slug' => 'inscripciones.editar', 'descripcion' => 'Puede editar inscripciones'],
            ['nombre' => 'Eliminar inscripciones', 'slug' => 'inscripciones.eliminar', 'descripcion' => 'Puede eliminar inscripciones'],

            // Comisiones
            ['nombre' => 'Ver comisiones', 'slug' => 'comisiones.ver', 'descripcion' => 'Puede ver comisiones'],
            ['nombre' => 'Crear comisiones', 'slug' => 'comisiones.crear', 'descripcion' => 'Puede crear comisiones'],
            ['nombre' => 'Editar comisiones', 'slug' => 'comisiones.editar', 'descripcion' => 'Puede editar comisiones'],
            ['nombre' => 'Eliminar comisiones', 'slug' => 'comisiones.eliminar', 'descripcion' => 'Puede eliminar comisiones'],

            // Asistencias
            ['nombre' => 'Ver asistencias', 'slug' => 'asistencias.ver', 'descripcion' => 'Puede ver asistencias'],
            ['nombre' => 'Crear asistencias', 'slug' => 'asistencias.crear', 'descripcion' => 'Puede registrar asistencias'],
            ['nombre' => 'Editar asistencias', 'slug' => 'asistencias.editar', 'descripcion' => 'Puede editar asistencias'],
            ['nombre' => 'Eliminar asistencias', 'slug' => 'asistencias.eliminar', 'descripcion' => 'Puede eliminar asistencias'],

            // Evaluaciones
            ['nombre' => 'Ver evaluaciones', 'slug' => 'evaluaciones.ver', 'descripcion' => 'Puede ver evaluaciones'],
            ['nombre' => 'Crear evaluaciones', 'slug' => 'evaluaciones.crear', 'descripcion' => 'Puede crear evaluaciones'],
            ['nombre' => 'Editar evaluaciones', 'slug' => 'evaluaciones.editar', 'descripcion' => 'Puede editar evaluaciones'],
            ['nombre' => 'Eliminar evaluaciones', 'slug' => 'evaluaciones.eliminar', 'descripcion' => 'Puede eliminar evaluaciones'],

            // Reportes
            ['nombre' => 'Ver reportes', 'slug' => 'reportes.ver', 'descripcion' => 'Puede ver reportes'],
            ['nombre' => 'Generar reportes', 'slug' => 'reportes.generar', 'descripcion' => 'Puede generar reportes'],
        ];

        $permissions = [];
        foreach ($permissionsData as $permData) {
            $permissions[$permData['slug']] = Permission::create($permData);
        }

        $this->command->info(count($permissions) . ' permisos creados.');

        return $permissions;
    }

    private function createRoles(): array
    {
        $rolesData = [
            [
                'nombre' => 'Administrador',
                'slug' => 'admin',
                'descripcion' => 'Acceso total al sistema'
            ],
            [
                'nombre' => 'Docente',
                'slug' => 'docente',
                'descripcion' => 'Profesor del curso de ingreso'
            ],
            [
                'nombre' => 'Coordinador',
                'slug' => 'coordinador',
                'descripcion' => 'Coordinador académico'
            ],
            [
                'nombre' => 'Alumno',
                'slug' => 'alumno',
                'descripcion' => 'Estudiante del curso de ingreso'
            ],
        ];

        $roles = [];
        foreach ($rolesData as $roleData) {
            $roles[$roleData['slug']] = Role::create($roleData);
        }

        $this->command->info(count($roles) . ' roles creados.');

        return $roles;
    }

    private function assignPermissionsToRoles(array $roles, array $permissions): void
    {
        // ADMIN: Todos los permisos
        $roles['admin']->permissions()->attach(array_column($permissions, 'id'));

        // COORDINADOR: Ver, crear y editar (sin eliminar)
        $coordinadorPerms = [
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar',
            'inscripciones.ver', 'inscripciones.crear', 'inscripciones.editar',
            'comisiones.ver', 'comisiones.crear', 'comisiones.editar',
            'asistencias.ver', 'asistencias.crear', 'asistencias.editar',
            'evaluaciones.ver', 'evaluaciones.crear', 'evaluaciones.editar',
            'reportes.ver', 'reportes.generar',
        ];
        $coordinadorPermIds = collect($permissions)->whereIn('slug', $coordinadorPerms)->pluck('id')->toArray();
        $roles['coordinador']->permissions()->attach($coordinadorPermIds);

        // DOCENTE: Ver sus comisiones, registrar asistencias y evaluaciones
        $docentePerms = [
            'comisiones.ver',
            'asistencias.ver', 'asistencias.crear', 'asistencias.editar',
            'evaluaciones.ver', 'evaluaciones.crear', 'evaluaciones.editar',
            'inscripciones.ver',
            'reportes.ver',
        ];
        $docentePermIds = collect($permissions)->whereIn('slug', $docentePerms)->pluck('id')->toArray();
        $roles['docente']->permissions()->attach($docentePermIds);

        // ALUMNO: Solo ver sus propios datos
        $alumnoPerms = [
            'inscripciones.ver',
            'asistencias.ver',
            'evaluaciones.ver',
        ];
        $alumnoPermIds = collect($permissions)->whereIn('slug', $alumnoPerms)->pluck('id')->toArray();
        $roles['alumno']->permissions()->attach($alumnoPermIds);

        $this->command->info('Permisos asignados a roles.');
    }
}
