<?php

namespace Database\Seeders;

use Database\Seeders\Sysacad\SysacadDataSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * INSTRUCCIONES DE INSTALACIÓN DESDE CERO:
     *
     * 1. Ejecutar migraciones y seeders base:
     *    docker compose exec app php artisan migrate:fresh --seed
     *
     * 2. Importar datos de Sysacad (requiere el archivo Excel en database/data/Datos Sysacad.xlsx):
     *    El SysacadDataSeeder se ejecuta automáticamente en el paso 1.
     *
     * 3. Importar datos de alumnos (alumnos_utn) - requiere backup SQL externo:
     *    docker compose exec mariadb mysql -u paicat -ppaicat alumnos_utn < bases_externas/seed_datos.sql
     *
     * 4. (Opcional - solo desarrollo) Datos de prueba:
     *    docker compose exec app php artisan db:seed --class=AlumnosPruebaSeeder
     *    docker compose exec app php artisan db:seed --class=AsistenciaTestSeeder
     *    docker compose exec app php artisan db:seed --class=ReInscripcionTestSeeder
     *
     * CREDENCIALES ADMIN: admin@paicat.utn.edu.ar / admin123
     *
     * DATOS NO RECUPERABLES SIN BACKUP:
     *   - Municipios, aulas y comisiones (no tienen seeder de producción)
     *   - Inscripciones y cursadas reales
     */
    public function run(): void
    {
        // BD principal: roles, permisos y usuario admin
        $this->call([
            RolesAndPermissionsSeeder::class,
            CreateAdminUserSeeder::class,
            ConfiguracionSeeder::class,
        ]);

        // Datos maestros de Sysacad (requiere database/data/Datos Sysacad.xlsx)
        $this->call(SysacadDataSeeder::class);
    }
}
