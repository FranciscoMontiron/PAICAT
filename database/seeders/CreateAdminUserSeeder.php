<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{

    public function run(): void
    {
        // La contraseña del admin se toma de ADMIN_PASSWORD (.env).
        // En producción es obligatoria: no se usa una contraseña por defecto.
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            if (app()->environment('production')) {
                $this->command->error('   ADMIN_PASSWORD no está definida en el .env.');
                $this->command->error('   Definila antes de ejecutar los seeders en producción.');

                return;
            }

            $password = 'admin123';
        }

        // Buscar o crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@paicat.utn.edu.ar')],
            [
                'name' => 'Admin',
                'apellido' => 'PAICAT',
                'dni' => '00000000',
                'telefono' => null,
                'password' => Hash::make($password),
                'estado' => 'activo',
            ]
        );

        // Asignar rol de admin
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        $this->command->info('   Usuario admin creado/actualizado:');
        $this->command->info('   Email: ' . $admin->email);
        $this->command->info('   Password: definida en ADMIN_PASSWORD (.env)');
        $this->command->info('   Rol: ' . ($adminRole ? $adminRole->nombre : 'Sin rol'));
    }
}
