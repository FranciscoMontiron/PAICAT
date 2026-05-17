<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AcademicoDato;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Hash;

class AlumnosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alumnos = [
            [
                'name' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan.perez@ejemplo.com',
                'dni' => '12345678',
            ],
            [
                'name' => 'María',
                'apellido' => 'González',
                'email' => 'maria.gonzalez@ejemplo.com',
                'dni' => '23456789',
            ],
            [
                'name' => 'Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'carlos.rodriguez@ejemplo.com',
                'dni' => '34567890',
            ],
            [
                'name' => 'Ana',
                'apellido' => 'Martínez',
                'email' => 'ana.martinez@ejemplo.com',
                'dni' => '45678901',
            ],
            [
                'name' => 'Luis',
                'apellido' => 'Fernández',
                'email' => 'luis.fernandez@ejemplo.com',
                'dni' => '56789012',
            ],
            [
                'name' => 'Laura',
                'apellido' => 'López',
                'email' => 'laura.lopez@ejemplo.com',
                'dni' => '67890123',
            ],
            [
                'name' => 'Diego',
                'apellido' => 'Sánchez',
                'email' => 'diego.sanchez@ejemplo.com',
                'dni' => '78901234',
            ],
            [
                'name' => 'Sofía',
                'apellido' => 'Ramírez',
                'email' => 'sofia.ramirez@ejemplo.com',
                'dni' => '89012345',
            ],
        ];

        $personIdInicial = 1000; // Usar IDs altos para evitar conflictos
        $index = 0;

        foreach ($alumnos as $alumnoData) {
            $index++;

            // Crear usuario
            $user = User::create([
                'name' => $alumnoData['name'],
                'apellido' => $alumnoData['apellido'],
                'email' => $alumnoData['email'],
                'dni' => $alumnoData['dni'],
                'password' => Hash::make('password123'),
                'telefono' => '1234567890',
                'estado' => 'activo',
            ]);

            // Crear datos académicos
            $academicoDato = AcademicoDato::create([
                'user_id' => $user->id,
                'especialidad_id' => null,
                'especialidad_alternativa_id' => null,
                'ingreso_carrera' => 2025,
                'egreso_secundaria' => 2024,
                'modalidad' => 'Presencial',
                'turno_ingreso' => 'Mañana',
                'turno_carrera' => 'Mañana',
                'tipo_ingreso' => 'Extensivo',
                'sede' => null,
                'estado' => 'activo',
            ]);

            // Crear inscripción confirmada al curso de ingreso PAICAT
            // IMPORTANTE: Necesitamos un person_id de la tabla alumnos_utn.persons
            // Por ahora, vamos a usar person_ids únicos para evitar conflictos
            // En producción, este valor vendría de la base de datos alumnos_utn
            Inscripcion::create([
                'person_id' => $personIdInicial + $index, // IDs únicos para prueba
                'academico_dato_id' => $academicoDato->id,
                'anio_ingreso' => 2025,
                'especialidad_id_sysacad' => 1, // TODO: Debe ser un ID válido de sysacad_especialidades
                'especialidad_alternativa_id_sysacad' => null,
                'modalidad' => 'Presencial',
                'turno_ingreso' => 'Mañana',
                'turno_carrera' => 'Mañana',
                'tipo_ingreso' => 'Extensivo',
                'sede_id_sysacad' => null,
                'estado' => Inscripcion::ESTADO_CONFIRMADO, // Estado confirmado para que aparezcan disponibles
                'doc_dni_validado' => true,
                'doc_titulo_validado' => true,
                'doc_analitico_validado' => true,
                'observaciones' => 'Inscripción de prueba generada por seeder',
                'usuario_registro_id' => 1, // Usuario administrador
            ]);
        }

        $this->command->info('Se crearon ' . count($alumnos) . ' alumnos de prueba con inscripciones confirmadas.');
    }
}
