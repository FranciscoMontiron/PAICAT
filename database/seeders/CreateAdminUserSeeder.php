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
        // Buscar o crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@paicat.utn.edu.ar'],
            [
                'name' => 'Admin',
                'apellido' => 'PAICAT',
                'dni' => '00000000',
                'telefono' => null,
                'password' => Hash::make('admin123'),
                'estado' => 'activo',
            ]
        );

        // Asignar rol de admin
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        $this->command->info('   Usuario admin creado/actualizado:');
        $this->command->info('   Email: admin@paicat.utn.edu.ar');
        $this->command->info('   Password: admin123');
        $this->command->info('   Rol: ' . ($adminRole ? $adminRole->nombre : 'Sin rol'));
    }
}
