<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password';
    protected $description = 'Reset admin user password to admin123';

    public function handle()
    {
        $admin = User::where('email', 'admin@paicat.utn.edu.ar')->first();

        if (!$admin) {
            $this->error('Usuario admin no encontrado. Creando...');

            $admin = User::create([
                'name' => 'Admin',
                'apellido' => 'PAICAT',
                'dni' => '00000000',
                'email' => 'admin@paicat.utn.edu.ar',
                'telefono' => null,
                'password' => Hash::make('admin123'),
                'estado' => 'activo',
            ]);

            // Asignar rol de admin
            $adminRole = Role::where('slug', 'admin')->first();
            if ($adminRole) {
                $admin->roles()->attach($adminRole->id);
            }

            $this->info('✅ Usuario admin creado exitosamente');
        } else {
            // Actualizar contraseña
            $admin->password = Hash::make('admin123');
            $admin->estado = 'activo';
            $admin->save();

            $this->info('✅ Contraseña del admin actualizada');
        }

        $this->info('Email: admin@paicat.utn.edu.ar');
        $this->info('Password: admin123');
        $this->info('Estado: ' . $admin->estado);

        // Verificar roles
        $roles = $admin->roles()->pluck('nombre')->toArray();
        $this->info('Roles: ' . (count($roles) > 0 ? implode(', ', $roles) : 'Sin roles'));

        return 0;
    }
}
