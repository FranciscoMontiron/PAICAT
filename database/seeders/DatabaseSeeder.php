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

        // Datos maestros de Sysacad - desde Excel (database/data/Datos Sysacad.xlsx)
        $this->call(SysacadDataSeeder::class);

        // Datos de alumnos (alumnos_utn) - desde backup SQL externo
        // Importar manualmente: docker compose exec mariadb mysql -u paicat -ppaicat paicat < bases_externas/seed_datos.sql
    }
}
