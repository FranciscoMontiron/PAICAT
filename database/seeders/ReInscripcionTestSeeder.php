<?php

namespace Database\Seeders;

use App\Models\AlumnosUtn\AcademicoDato;
use App\Models\AlumnosUtn\FormularioDato;
use App\Models\AlumnosUtn\Person;
use App\Models\Inscripcion;
use App\Models\Trayectoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder para crear datos de prueba de reinscripción.
 *
 * Crea alumnos ficticios en alumnos_utn con UNA inscripción cancelada en PAICAT,
 * y datos académicos para el nuevo año (2026) simulando que volvieron a llenar
 * el formulario de preinscripción.
 *
 * Al importarlos, la inscripción existente se ACTUALIZA (no se crea nueva)
 * y se agrega una trayectoria de reinscripción.
 */
class ReInscripcionTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando datos de prueba para reinscripciones...');

        // Person IDs altos para evitar conflictos con datos reales
        $basePersonId = 9000;

        // Especialidades reales del sistema: 5=Sistemas, 7=Eléctrica, 17=Mecánica, 24=Industrial, 27=Química, 31=Civil
        $alumnos = [
            [
                'person_id' => $basePersonId + 1,
                'nombre' => 'Martín',
                'apellido' => 'Aguirre',
                'documento' => '40111222',
                'cuit' => '20401112228',
                'email' => 'martin.aguirre.test@ejemplo.com',
                'nacimiento_fecha' => '2002-03-15',
                'sexo' => 'Masculino',
                'genero' => 'Masculino',
                'nacionalidad' => 'Argentina',
                'estado_civil' => 'Soltero/a',
                'direccion' => 'Calle 7 N° 1234',
                'loc_residencia' => 1,
                'prov_residencia' => 1,
                'pais_residencia' => 1,
                'codigo_postal' => '1900',
                'telefono_celular' => '2211234567',
                'cancelacion_anio' => 2025,
                'cancelacion_esp' => 5,
                'cancelacion_motivo' => 'No se presentó a cursar',
                'nuevo_anio' => 2026,
                'nuevo_esp' => 5,
                'nuevo_esp_alt' => 7,
                'modalidad' => 'Presencial',
                'turno_ingreso' => 'Mañana',
                'turno_carrera' => 'Mañana',
                'tipo_ingreso' => 'Extensivo',
                'egreso_secundaria' => 2021,
            ],
            [
                'person_id' => $basePersonId + 2,
                'nombre' => 'Lucía',
                'apellido' => 'Benítez',
                'documento' => '41222333',
                'cuit' => '27412223336',
                'email' => 'lucia.benitez.test@ejemplo.com',
                'nacimiento_fecha' => '2003-07-22',
                'sexo' => 'Femenino',
                'genero' => 'Femenino',
                'nacionalidad' => 'Argentina',
                'estado_civil' => 'Soltero/a',
                'direccion' => 'Av. 44 N° 567',
                'loc_residencia' => 1,
                'prov_residencia' => 1,
                'pais_residencia' => 1,
                'codigo_postal' => '1900',
                'telefono_celular' => '2212345678',
                'cancelacion_anio' => 2025,
                'cancelacion_esp' => 24,
                'cancelacion_motivo' => 'Baja voluntaria por motivos personales',
                'nuevo_anio' => 2026,
                'nuevo_esp' => 24,
                'nuevo_esp_alt' => 17,
                'modalidad' => 'Presencial',
                'turno_ingreso' => 'Tarde',
                'turno_carrera' => 'Tarde',
                'tipo_ingreso' => 'Extensivo',
                'egreso_secundaria' => 2022,
            ],
            [
                'person_id' => $basePersonId + 3,
                'nombre' => 'Tomás',
                'apellido' => 'Cardozo',
                'documento' => '42333444',
                'cuit' => '20423334441',
                'email' => 'tomas.cardozo.test@ejemplo.com',
                'nacimiento_fecha' => '2004-01-10',
                'sexo' => 'Masculino',
                'genero' => 'Masculino',
                'nacionalidad' => 'Argentina',
                'estado_civil' => 'Soltero/a',
                'direccion' => 'Calle 13 N° 890',
                'loc_residencia' => 1,
                'prov_residencia' => 1,
                'pais_residencia' => 1,
                'codigo_postal' => '1900',
                'telefono_celular' => '2213456789',
                'cancelacion_anio' => 2024,
                'cancelacion_esp' => 7,
                'cancelacion_motivo' => 'Cancelación por inasistencias reiteradas',
                'nuevo_anio' => 2026,
                'nuevo_esp' => 5,
                'nuevo_esp_alt' => null,
                'modalidad' => 'Semipresencial',
                'turno_ingreso' => 'Mañana',
                'turno_carrera' => 'Mañana',
                'tipo_ingreso' => 'Intensivo',
                'egreso_secundaria' => 2022,
            ],
            [
                'person_id' => $basePersonId + 4,
                'nombre' => 'Valentina',
                'apellido' => 'Domínguez',
                'documento' => '39444555',
                'cuit' => '27394445553',
                'email' => 'valentina.dominguez.test@ejemplo.com',
                'nacimiento_fecha' => '2001-11-05',
                'sexo' => 'Femenino',
                'genero' => 'Femenino',
                'nacionalidad' => 'Argentina',
                'estado_civil' => 'Soltero/a',
                'direccion' => 'Diagonal 73 N° 2345',
                'loc_residencia' => 1,
                'prov_residencia' => 1,
                'pais_residencia' => 1,
                'codigo_postal' => '1900',
                'telefono_celular' => '2214567890',
                'cancelacion_anio' => 2024,
                'cancelacion_esp' => 27,
                'cancelacion_motivo' => 'No completó documentación requerida',
                'nuevo_anio' => 2026,
                'nuevo_esp' => 27,
                'nuevo_esp_alt' => 31,
                'modalidad' => 'Presencial',
                'turno_ingreso' => 'Tardenoche',
                'turno_carrera' => 'Tardenoche',
                'tipo_ingreso' => 'Extensivo',
                'egreso_secundaria' => 2019,
            ],
            [
                'person_id' => $basePersonId + 5,
                'nombre' => 'Santiago',
                'apellido' => 'Espinoza',
                'documento' => '43555666',
                'cuit' => '20435556664',
                'email' => 'santiago.espinoza.test@ejemplo.com',
                'nacimiento_fecha' => '2005-05-20',
                'sexo' => 'Masculino',
                'genero' => 'Masculino',
                'nacionalidad' => 'Argentina',
                'estado_civil' => 'Soltero/a',
                'direccion' => 'Calle 50 N° 678',
                'loc_residencia' => 1,
                'prov_residencia' => 1,
                'pais_residencia' => 1,
                'codigo_postal' => '1900',
                'telefono_celular' => '2215678901',
                'cancelacion_anio' => 2025,
                'cancelacion_esp' => 17,
                'cancelacion_motivo' => 'Cancelación por falta de documentación',
                'nuevo_anio' => 2026,
                'nuevo_esp' => 17,
                'nuevo_esp_alt' => 24,
                'modalidad' => 'Virtual',
                'turno_ingreso' => 'Tardenoche',
                'turno_carrera' => 'Tardenoche',
                'tipo_ingreso' => 'Extensivo',
                'egreso_secundaria' => 2023,
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($alumnos as $data) {
                // 1. Crear persona en alumnos_utn con datos completos
                $person = Person::on('alumnos_utn')->updateOrCreate(
                    ['documento' => $data['documento']],
                    [
                        'nombre' => $data['nombre'],
                        'apellido' => $data['apellido'],
                        'cuit' => $data['cuit'],
                        'email' => $data['email'],
                        'nacimiento_fecha' => $data['nacimiento_fecha'],
                        'sexo' => $data['sexo'],
                        'genero' => $data['genero'],
                        'nacionalidad' => $data['nacionalidad'],
                        'estado_civil' => $data['estado_civil'],
                        'tipo_documento' => 'DNI',
                        'pais_documento' => 'Argentina',
                        'pais_origen' => 'Argentina',
                        'direccion' => $data['direccion'],
                        'loc_residencia' => $data['loc_residencia'],
                        'prov_residencia' => $data['prov_residencia'],
                        'pais_residencia' => $data['pais_residencia'],
                        'codigo_postal' => $data['codigo_postal'],
                        'telefono_celular' => $data['telefono_celular'],
                        'contacto_emergencia' => 'Familiar',
                        'telefono_emergencia' => '221' . rand(1000000, 9999999),
                        '__proceso' => 'Alta',
                        '__estado' => 'Verificado',
                        '__usuario' => 'seeder',
                    ]
                );

                // 2. Crear datos académicos para el NUEVO año de ingreso
                AcademicoDato::on('alumnos_utn')->updateOrCreate(
                    ['person_id' => $person->id, 'ingreso_carrera' => $data['nuevo_anio']],
                    [
                        'especialidad_id' => $data['nuevo_esp'],
                        'especialidad_alternativa_id' => $data['nuevo_esp_alt'],
                        'egreso_secundaria' => $data['egreso_secundaria'],
                        'modalidad' => $data['modalidad'],
                        'turno_ingreso' => $data['turno_ingreso'],
                        'turno_carrera' => $data['turno_carrera'],
                        'tipo_ingreso' => $data['tipo_ingreso'],
                        'sede' => 1,
                    ]
                );

                // 3. Crear formulario_dato como "Completo"
                FormularioDato::on('alumnos_utn')->updateOrCreate(
                    ['person_id' => $person->id],
                    ['estado' => 'Completo']
                );

                // 4. Crear UNA inscripción cancelada en PAICAT (una por alumno)
                $existe = Inscripcion::withTrashed()
                    ->where('person_id', $person->id)
                    ->exists();

                if (!$existe) {
                    $inscripcion = Inscripcion::create([
                        'person_id' => $person->id,
                        'anio_ingreso' => $data['cancelacion_anio'],
                        'especialidad_id_sysacad' => $data['cancelacion_esp'],
                        'modalidad' => $data['modalidad'],
                        'turno_ingreso' => $data['turno_ingreso'],
                        'turno_carrera' => $data['turno_carrera'],
                        'tipo_ingreso' => $data['tipo_ingreso'],
                        'estado' => Inscripcion::ESTADO_CANCELADO,
                        'estado_documentacion' => Inscripcion::DOC_PENDIENTE,
                        'estado_ingreso' => Inscripcion::INGRESO_CANCELADO,
                        'usuario_registro_id' => 1,
                        'observaciones' => 'Inscripción de prueba - seeder reinscripción',
                    ]);

                    // Trayectoria: activo → cancelado
                    $aniosDiff = 2026 - $data['cancelacion_anio'];
                    Trayectoria::create([
                        'inscripcion_id' => $inscripcion->id,
                        'estado' => Trayectoria::ESTADO_ACTIVO,
                        'fecha_inicio' => now()->subYears($aniosDiff)->startOfYear(),
                        'fecha_fin' => now()->subYears($aniosDiff)->addMonths(3),
                        'motivo' => 'Inscripción al curso de ingreso ' . $data['cancelacion_anio'],
                        'registrado_por' => 1,
                    ]);

                    Trayectoria::create([
                        'inscripcion_id' => $inscripcion->id,
                        'estado' => Trayectoria::ESTADO_CANCELADO,
                        'fecha_inicio' => now()->subYears($aniosDiff)->addMonths(3),
                        'fecha_fin' => null,
                        'motivo' => $data['cancelacion_motivo'],
                        'es_voluntaria' => str_contains(strtolower($data['cancelacion_motivo']), 'voluntaria'),
                        'registrado_por' => 1,
                    ]);
                }

                $this->command->info("  - {$data['apellido']}, {$data['nombre']} (DNI: {$data['documento']}) " .
                    "- Cancelada {$data['cancelacion_anio']}, formulario nuevo {$data['nuevo_anio']}");
            }

            DB::commit();

            $this->command->info('');
            $this->command->info('Se crearon ' . count($alumnos) . ' alumnos de prueba con inscripciones canceladas.');
            $this->command->info('Al importarlos, la inscripción se actualizará con los nuevos datos y se registrará la reinscripción en trayectorias.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al crear datos de prueba: ' . $e->getMessage());
            throw $e;
        }
    }
}
