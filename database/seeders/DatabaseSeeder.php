<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Sysacad\SysacadDataSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Importar datos de Sysacad desde Excel
        $this->call([
            SysacadDataSeeder::class,
            RolesAndPermissionsSeeder::class,
            CreateAdminUserSeeder::class,
        ]);
    }
}
