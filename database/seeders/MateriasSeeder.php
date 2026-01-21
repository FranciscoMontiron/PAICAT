<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriasSeeder extends Seeder
{
    /**
     * Seed the materias table.
     * Materias del curso de ingreso a la UTN.
     */
    public function run(): void
    {
        $materias = [
            // Materias de nivelación - comunes a todas las especialidades
            [
                'codigo' => 'MAT-INI',
                'nombre' => 'Matemática Inicial',
                'descripcion' => 'Nivelación en matemática básica para el ingreso universitario',
                'anio_cursado' => 0,
                'especialidad_id_sysacad' => null, // Común a todas
                'es_nivelacion' => true,
                'carga_horaria' => 6,
                'tipo' => 'nivelacion',
                'activa' => true,
            ],
            [
                'codigo' => 'FIS-INI',
                'nombre' => 'Física Inicial',
                'descripcion' => 'Nivelación en física básica para el ingreso universitario',
                'anio_cursado' => 0,
                'especialidad_id_sysacad' => null,
                'es_nivelacion' => true,
                'carga_horaria' => 4,
                'tipo' => 'nivelacion',
                'activa' => true,
            ],
            [
                'codigo' => 'QUI-INI',
                'nombre' => 'Química Inicial',
                'descripcion' => 'Nivelación en química básica para el ingreso universitario',
                'anio_cursado' => 0,
                'especialidad_id_sysacad' => null,
                'es_nivelacion' => true,
                'carga_horaria' => 4,
                'tipo' => 'nivelacion',
                'activa' => true,
            ],
            [
                'codigo' => 'INT-ING',
                'nombre' => 'Introducción a la Ingeniería',
                'descripcion' => 'Introducción a las carreras de ingeniería y vida universitaria',
                'anio_cursado' => 0,
                'especialidad_id_sysacad' => null,
                'es_nivelacion' => true,
                'carga_horaria' => 2,
                'tipo' => 'nivelacion',
                'activa' => true,
            ],
            [
                'codigo' => 'LYC-INI',
                'nombre' => 'Lectura y Comprensión de Textos',
                'descripcion' => 'Desarrollo de habilidades de lectura y comprensión de textos académicos',
                'anio_cursado' => 0,
                'especialidad_id_sysacad' => null,
                'es_nivelacion' => true,
                'carga_horaria' => 2,
                'tipo' => 'nivelacion',
                'activa' => true,
            ],
        ];

        foreach ($materias as $materia) {
            Materia::firstOrCreate(
                ['codigo' => $materia['codigo']],
                $materia
            );
        }
    }
}
