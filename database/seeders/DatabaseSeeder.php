<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Sysacad\SysacadDataSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Las 3 bases de datos del sistema:
     * 1. paicat - BD principal (roles, permisos, usuarios admin)
     * 2. sysacad - Datos maestros desde Excel (países, provincias, escuelas)
     * 3. alumnos_utn - Datos de alumnos desde backup SQL (persons, academico_datos)
     */
    public function run(): void
    {
        // Datos de la BD principal (paicat)
        $this->call([
            RolesAndPermissionsSeeder::class,
            CreateAdminUserSeeder::class,
        ]);
        
        // Datos maestros de Sysacad (sysacad) - desde Excel
        // Ejecutar por separado: php artisan db:seed --class="Database\Seeders\Sysacad\SysacadDataSeeder"
        // $this->call(SysacadDataSeeder::class);
        
        // Datos de alumnos (alumnos_utn) - desde backup SQL
        // Importar manualmente: cat backup_27-10-2025.sql | docker exec -i paicat_mariadb mariadb -u root -proot alumnos_utn
    }
}
