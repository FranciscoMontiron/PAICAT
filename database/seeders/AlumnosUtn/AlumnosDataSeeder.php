<?php

namespace Database\Seeders\AlumnosUtn;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlumnosDataSeeder extends Seeder
{
    /**
     * Ejecutar los seeders de la base de datos alumnos_utn.
     */
    public function run(): void
    {
        $backupFile = base_path('backup_27-10-2025.sql');

        if (!file_exists($backupFile)) {
            $this->command->error(" Archivo de backup no encontrado: {$backupFile}");
            return;
        }

        $this->command->info(" Importando datos de alumnos desde backup...");

        // Leer el archivo SQL
        $sql = file_get_contents($backupFile);

        // Extraer solo los INSERT de las tablas que necesitamos
        $tables = [
            'persons',
            'academico_datos',
            'secundaria_datos',
            'formulario_datos'
        ];

        foreach ($tables as $table) {
            $this->command->info(" Importando tabla: {$table}");

            // Buscar los INSERT statements para esta tabla
            if (preg_match("/INSERT INTO `{$table}` VALUES (.+?);/s", $sql, $matches)) {
                try {
                    // Ejecutar el INSERT en la base de datos alumnos_utn
                    DB::connection('alumnos_utn')->statement("INSERT INTO `{$table}` VALUES " . $matches[1]);

                    $count = DB::connection('alumnos_utn')->table($table)->count();
                    $this->command->info(" {$table}: {$count} registros importados");
                } catch (\Exception $e) {
                    $this->command->warn("  Error importando {$table}: " . $e->getMessage());
                }
            } else {
                $this->command->warn("  No se encontraron datos para {$table}");
            }
        }

        $this->command->info(" Importación de datos de alumnos completada!");
    }
}
