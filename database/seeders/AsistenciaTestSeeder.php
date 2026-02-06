<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Comision;
use App\Models\InscripcionComision;
use App\Models\Asistencia;
use App\Models\AcademicoDato;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AsistenciaTestSeeder extends Seeder
{
    private $personIds = [];
    private $academicoDatosIds = [];

    public function run(): void
    {
        $this->command->info('🎯 Creando datos de prueba para asistencias...');

        // 1. Obtener roles
        $roleDocente = Role::where('nombre', 'Docente')->first();
        $roleAlumno = Role::where('nombre', 'Alumno')->first();
        
        if (!$roleDocente || !$roleAlumno) {
            $this->command->error('❌ No se encontraron los roles necesarios.');
            return;
        }

        // 2. Crear docentes
        $this->command->info('👨‍🏫 Creando docentes...');
        $docentes = $this->crearDocentes($roleDocente);

        // 3. Gestionar datos en alumnos_utn
        $this->gestionarAlumnosUtn();

        // 4. NUEVO: Crear usuarios y academico_datos en paicat
        $this->command->info('👥 Creando usuarios y academico_datos en paicat...');
        $this->crearUsuariosYAcademicoDatos($roleAlumno);

        // 5. Crear comisiones
        $this->command->info('📚 Creando comisiones...');
        $comisiones = $this->crearComisiones($docentes);

        // 6. Inscribir alumnos en comisiones
        $this->command->info('📝 Inscribiendo alumnos en comisiones...');
        $this->inscribirAlumnos($comisiones);

        // 7. Crear asistencias
        $this->command->info('📊 Generando asistencias...');
        $this->crearAsistencias($comisiones[0], 20, 20);
        $this->crearAsistenciasConRiesgo($comisiones[1], 25);
        $this->crearAsistencias($comisiones[2], 5, 5);
        $this->crearAsistencias($comisiones[3], 60, 90);

        $this->command->info('');
        $this->command->info('✨ ¡Datos creados exitosamente!');
        $this->command->info('🔑 Credenciales:');
        $this->command->info('   Docentes: docente1-3@test.com / password123');
        $this->command->info('   Alumnos: alumno1-30@test.com / password123');
    }

    private function crearDocentes($roleDocente)
    {
        $docentes = [];
        
        $docentes[] = User::firstOrCreate(['email' => 'docente1@test.com'], [
            'name' => 'María', 'apellido' => 'García',
            'password' => Hash::make('password123'), 
            'email_verified_at' => now(),
            'estado' => 'activo',
        ]);
        $docentes[0]->roles()->syncWithoutDetaching([$roleDocente->id]);

        $docentes[] = User::firstOrCreate(['email' => 'docente2@test.com'], [
            'name' => 'Juan', 'apellido' => 'Pérez',
            'password' => Hash::make('password123'), 
            'email_verified_at' => now(),
            'estado' => 'activo',
        ]);
        $docentes[1]->roles()->syncWithoutDetaching([$roleDocente->id]);

        $docentes[] = User::firstOrCreate(['email' => 'docente3@test.com'], [
            'name' => 'Ana', 'apellido' => 'Martínez',
            'password' => Hash::make('password123'), 
            'email_verified_at' => now(),
            'estado' => 'activo',
        ]);
        $docentes[2]->roles()->syncWithoutDetaching([$roleDocente->id]);

        $this->command->info("✅ Creados " . count($docentes) . " docentes");
        
        return $docentes;
    }

    private function gestionarAlumnosUtn()
    {
        $this->command->info('📋 Gestionando datos académicos en alumnos_utn...');
        
        // Verificar si la tabla academico_datos existe
        $tableExists = Schema::connection('alumnos_utn')->hasTable('academico_datos');
        
        if (!$tableExists) {
            $this->crearTablaAcademicoDatos();
        }
        
        // Obtener columnas existentes
        $columns = DB::connection('alumnos_utn')->getSchemaBuilder()->getColumnListing('academico_datos');
        
        // Verificar si existen personas
        $personsCount = DB::connection('alumnos_utn')->table('persons')->count();
        
        if ($personsCount === 0) {
            $this->personIds = $this->crearPersonas();
        } else {
            $this->personIds = DB::connection('alumnos_utn')->table('persons')
                ->orderBy('id')
                ->limit(30)
                ->pluck('id')
                ->toArray();
            $this->command->info("✅ Usando " . count($this->personIds) . " personas existentes");
        }
        
        // Crear academico_datos en alumnos_utn
        $this->academicoDatosIds = $this->crearAcademicoDatosUtn($columns);
    }

    private function crearTablaAcademicoDatos()
    {
        $this->command->warn('⚠️ Creando tabla academico_datos en alumnos_utn...');
        
        try {
            DB::connection('alumnos_utn')->statement("
                CREATE TABLE IF NOT EXISTS academico_datos (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    person_id BIGINT UNSIGNED,
                    especialidad_id INT NOT NULL,
                    especialidad_alternativa_id INT NULL,
                    ingreso_carrera YEAR(4) NOT NULL,
                    egreso_secundaria YEAR(4) NOT NULL,
                    modalidad ENUM('Presencial', 'Virtual', 'Semipresencial') NOT NULL DEFAULT 'Presencial',
                    turno_ingreso VARCHAR(255) NOT NULL,
                    turno_carrera VARCHAR(255) NOT NULL,
                    tipo_ingreso ENUM('Intensivo', 'Extensivo') NOT NULL DEFAULT 'Extensivo',
                    sede INT NOT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            $this->command->info('✅ Tabla creada');
        } catch (\Exception $e) {
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function crearPersonas()
    {
        $this->command->info('👤 Creando personas en alumnos_utn...');
        
        $personIds = [];
        for ($i = 1; $i <= 30; $i++) {
            $personId = DB::connection('alumnos_utn')->table('persons')->insertGetId([
                'nombre' => 'Alumno' . $i,
                'apellido' => 'Test' . $i,
                'documento' => 'DNI' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'alumno' . $i . '@test.com',
                'created_at' => now(),
                'updated_at' => now(),
                '__proceso' => 'Alta',
                '__estado' => 'Verificado',
                '__usuario' => 'seeder'
            ]);
            $personIds[] = $personId;
        }
        
        $this->command->info("✅ Creadas " . count($personIds) . " personas");
        return $personIds;
    }

    private function crearAcademicoDatosUtn($columns)
    {
        $academicoDatosIds = [];
        
        for ($i = 0; $i < 30; $i++) {
            $data = [
                'person_id' => $this->personIds[$i] ?? ($i + 1),
                'especialidad_id' => rand(1, 10),
                'especialidad_alternativa_id' => rand(1, 10),
                'ingreso_carrera' => 2024,
                'egreso_secundaria' => 2023,
                'modalidad' => 'Presencial',
                'turno_ingreso' => ['Mañana', 'Tarde', 'Noche'][rand(0, 2)],
                'turno_carrera' => ['Mañana', 'Tarde', 'Noche'][rand(0, 2)],
                'tipo_ingreso' => ['Intensivo', 'Extensivo'][rand(0, 1)],
                'sede' => rand(1, 3),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $filteredData = array_intersect_key($data, array_flip($columns));
            
            try {
                $academicoDatoId = DB::connection('alumnos_utn')->table('academico_datos')->insertGetId($filteredData);
                $academicoDatosIds[] = $academicoDatoId;
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $existing = DB::connection('alumnos_utn')->table('academico_datos')
                        ->where('person_id', $data['person_id'])
                        ->first();
                    if ($existing) {
                        $academicoDatosIds[] = $existing->id;
                    }
                }
            }
        }

        if (empty($academicoDatosIds)) {
            $academicoDatosIds = DB::connection('alumnos_utn')->table('academico_datos')
                ->orderBy('id')
                ->limit(30)
                ->pluck('id')
                ->toArray();
        }

        $this->command->info("✅ Disponibles " . count($academicoDatosIds) . " academico_datos");
        return $academicoDatosIds;
    }

    private function crearUsuariosYAcademicoDatos($roleAlumno)
    {
        $creados = 0;
        
        foreach ($this->academicoDatosIds as $index => $academicoUtnId) {
            // Obtener person desde alumnos_utn
            $academicoUtn = DB::connection('alumnos_utn')->table('academico_datos')
                ->where('id', $academicoUtnId)
                ->first();
            
            if (!$academicoUtn) continue;
            
            $person = DB::connection('alumnos_utn')->table('persons')
                ->where('id', $academicoUtn->person_id)
                ->first();
            
            if (!$person) continue;

            try {
                // Crear usuario en paicat
                $user = User::firstOrCreate(
                    ['email' => $person->email],
                    [
                        'name' => $person->nombre ?? 'Sin nombre',
                        'apellido' => $person->apellido ?? 'Sin apellido',
                        'dni' => $person->documento ?? null,
                        'password' => Hash::make('password123'),
                        'email_verified_at' => now(),
                        'estado' => 'activo',
                    ]
                );

                // Asignar rol
                if (!$user->roles()->where('role_id', $roleAlumno->id)->exists()) {
                    $user->roles()->attach($roleAlumno->id);
                }

                // Crear academico_dato en paicat vinculado al user
                AcademicoDato::firstOrCreate(
                    ['id' => $academicoUtnId],
                    [
                        'user_id' => $user->id,
                        'especialidad_id' => $academicoUtn->especialidad_id ?? 1,
                        'especialidad_alternativa_id' => $academicoUtn->especialidad_alternativa_id ?? null,
                        'ingreso_carrera' => $academicoUtn->ingreso_carrera ?? date('Y'),
                        'egreso_secundaria' => $academicoUtn->egreso_secundaria ?? date('Y'),
                        'modalidad' => $academicoUtn->modalidad ?? 'Presencial',
                        'turno_ingreso' => $academicoUtn->turno_ingreso ?? 'Mañana',
                        'turno_carrera' => $academicoUtn->turno_carrera ?? 'Mañana',
                        'tipo_ingreso' => $academicoUtn->tipo_ingreso ?? 'Extensivo',
                        'sede' => $academicoUtn->sede ?? 1,
                        'estado' => 'activo',
                    ]
                );

                $creados++;
                
                if ($creados % 10 == 0) {
                    $this->command->info("  ✅ {$creados} alumnos procesados...");
                }

            } catch (\Exception $e) {
                $this->command->warn("  ⚠️  Error con {$person->email}: " . $e->getMessage());
            }
        }

        $this->command->info("✅ Creados {$creados} usuarios y academico_datos en paicat");
    }

    private function crearComisiones($docentes)
    {
        $comisiones = [];

        $comisiones[] = Comision::firstOrCreate(['codigo' => 'ING-2025-INT-01'], [
            'nombre' => 'Ingreso 2025 - Intensivo - Comisión 1',
            'anio' => 2025, 'periodo' => 'Intensivo', 'turno' => 'Mañana',
            'modalidad' => 'Presencial', 'docente_id' => $docentes[0]->id,
            'cupo_maximo' => 40, 'cupo_actual' => 0, 'estado' => 'activa',
        ]);

        $comisiones[] = Comision::firstOrCreate(['codigo' => 'ING-2025-INT-02'], [
            'nombre' => 'Ingreso 2025 - Intensivo - Comisión 2',
            'anio' => 2025, 'periodo' => 'Intensivo', 'turno' => 'Tarde',
            'modalidad' => 'Presencial', 'docente_id' => $docentes[1]->id,
            'cupo_maximo' => 35, 'cupo_actual' => 0, 'estado' => 'activa',
        ]);

        $comisiones[] = Comision::firstOrCreate(['codigo' => 'ING-2025-INT-03'], [
            'nombre' => 'Ingreso 2025 - Intensivo - Comisión 3',
            'anio' => 2025, 'periodo' => 'Intensivo', 'turno' => 'Noche',
            'modalidad' => 'Presencial', 'docente_id' => $docentes[2]->id,
            'cupo_maximo' => 30, 'cupo_actual' => 0, 'estado' => 'activa',
        ]);

        $comisiones[] = Comision::firstOrCreate(['codigo' => 'ING-2024-EXT-01'], [
            'nombre' => 'Ingreso 2024 - Extensivo - Comisión 1',
            'anio' => 2024, 'periodo' => 'Extensivo', 'turno' => 'Mañana',
            'modalidad' => 'Presencial', 'docente_id' => $docentes[0]->id,
            'cupo_maximo' => 40, 'cupo_actual' => 0, 'estado' => 'finalizada',
        ]);

        $this->command->info("✅ Creadas " . count($comisiones) . " comisiones");
        return $comisiones;
    }

    private function inscribirAlumnos($comisiones)
    {
        DB::connection('paicat')->statement('SET FOREIGN_KEY_CHECKS=0');
        
        try {
            // Comisión 1: primeros 15 alumnos
            for ($i = 0; $i < 15 && $i < count($this->academicoDatosIds); $i++) {
                InscripcionComision::firstOrCreate([
                    'academico_dato_id' => $this->academicoDatosIds[$i],
                    'comision_id' => $comisiones[0]->id,
                ], [
                    'estado' => 'confirmado', 
                    'fecha_inscripcion' => Carbon::now()->subDays(20)
                ]);
            }

            // Comisión 2: alumnos 15-26
            for ($i = 15; $i < 27 && $i < count($this->academicoDatosIds); $i++) {
                InscripcionComision::firstOrCreate([
                    'academico_dato_id' => $this->academicoDatosIds[$i],
                    'comision_id' => $comisiones[1]->id,
                ], [
                    'estado' => 'confirmado', 
                    'fecha_inscripcion' => Carbon::now()->subDays(25)
                ]);
            }

            // Comisión 3: primeros 8 alumnos
            for ($i = 0; $i < 8 && $i < count($this->academicoDatosIds); $i++) {
                InscripcionComision::firstOrCreate([
                    'academico_dato_id' => $this->academicoDatosIds[$i],
                    'comision_id' => $comisiones[2]->id,
                ], [
                    'estado' => 'confirmado', 
                    'fecha_inscripcion' => Carbon::now()->subDays(5)
                ]);
            }

            // Comisión 4: alumnos 20-29
            for ($i = 20; $i < 30 && $i < count($this->academicoDatosIds); $i++) {
                InscripcionComision::firstOrCreate([
                    'academico_dato_id' => $this->academicoDatosIds[$i],
                    'comision_id' => $comisiones[3]->id,
                ], [
                    'estado' => 'confirmado', 
                    'fecha_inscripcion' => Carbon::now()->subMonths(3)
                ]);
            }
            
            $this->command->info("✅ Alumnos inscritos en comisiones");
        } catch (\Exception $e) {
            $this->command->error('❌ Error: ' . $e->getMessage());
        } finally {
            DB::connection('paicat')->statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function crearAsistencias($comision, $diasAtras, $numClases)
    {
        $inscripciones = $comision->inscripciones;
        $fecha = Carbon::now()->subDays($diasAtras);
        $diasClase = 0;
        
        while ($diasClase < $numClases) {
            if ($fecha->isWeekday()) {
                foreach ($inscripciones as $inscripcion) {
                    $rand = rand(1, 100);
                    $estado = $rand <= 80 ? 'presente' : ($rand <= 90 ? 'ausente' : ($rand <= 95 ? 'tardanza' : 'justificado'));
                    
                    Asistencia::firstOrCreate([
                        'inscripcion_comision_id' => $inscripcion->id,
                        'fecha' => $fecha->format('Y-m-d'),
                    ], [
                        'estado' => $estado,
                        'registrado_por' => $comision->docente_id,
                    ]);
                }
                $diasClase++;
            }
            $fecha->addDay();
        }
    }

    private function crearAsistenciasConRiesgo($comision, $numClases)
    {
        $inscripciones = $comision->inscripciones;
        $fecha = Carbon::now()->subDays(25);
        $diasClase = 0;
        
        while ($diasClase < $numClases) {
            if ($fecha->isWeekday()) {
                foreach ($inscripciones as $index => $inscripcion) {
                    if ($index < 3) {
                        $rand = rand(1, 100);
                        $estado = $rand <= 40 ? 'ausente' : ($rand <= 70 ? 'presente' : 'tardanza');
                    } else {
                        $rand = rand(1, 100);
                        $estado = $rand <= 85 ? 'presente' : ($rand <= 92 ? 'ausente' : 'tardanza');
                    }
                    
                    Asistencia::firstOrCreate([
                        'inscripcion_comision_id' => $inscripcion->id,
                        'fecha' => $fecha->format('Y-m-d'),
                    ], [
                        'estado' => $estado,
                        'registrado_por' => $comision->docente_id,
                    ]);
                }
                $diasClase++;
            }
            $fecha->addDay();
        }
    }
}