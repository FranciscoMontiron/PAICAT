<?php

namespace App\Console\Commands;

use App\Models\AcademicoDato;
use App\Models\Inscripcion;
use App\Models\AlumnosUtn\Person;
use Illuminate\Console\Command;

class CompletarDatosAcademicos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inscripciones:completar-datos-academicos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completa los datos académicos faltantes para inscripciones activas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando inscripciones sin datos académicos...');

        // Obtener inscripciones activas sin academico_dato_id
        $inscripciones = Inscripcion::whereNull('academico_dato_id')
            ->whereNotIn('estado', [Inscripcion::ESTADO_CANCELADO, Inscripcion::ESTADO_BAJA])
            ->get();

        if ($inscripciones->isEmpty()) {
            $this->info('No hay inscripciones sin datos académicos.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$inscripciones->count()} inscripciones sin datos académicos.");
        $completadas = 0;
        $errores = 0;

        foreach ($inscripciones as $inscripcion) {
            try {
                // Obtener datos de la persona desde alumnos_utn
                $person = Person::on('alumnos_utn')->find($inscripcion->person_id);

                if (!$person || !$person->user_id) {
                    $this->warn("Inscripción #{$inscripcion->id}: Person #{$inscripcion->person_id} no tiene user_id");
                    $errores++;
                    continue;
                }

                // Verificar si ya existe academico_dato para este user_id
                $academicoDato = AcademicoDato::where('user_id', $person->user_id)->first();

                if (!$academicoDato) {
                    // Crear nuevo academico_dato
                    $academicoDato = AcademicoDato::create([
                        'user_id' => $person->user_id,
                        'especialidad_id' => null,
                        'especialidad_alternativa_id' => null,
                        'ingreso_carrera' => $inscripcion->anio_ingreso,
                        'egreso_secundaria' => $inscripcion->anio_ingreso - 1,
                        'modalidad' => $inscripcion->modalidad,
                        'turno_ingreso' => $inscripcion->turno_ingreso,
                        'turno_carrera' => $inscripcion->turno_carrera,
                        'tipo_ingreso' => $inscripcion->tipo_ingreso,
                        'sede' => $inscripcion->sede_id_sysacad,
                        'estado' => 'activo',
                    ]);

                    $this->info("✓ Creado academico_dato #{$academicoDato->id} para user #{$person->user_id}");
                }

                // Actualizar inscripción con el academico_dato_id
                $inscripcion->academico_dato_id = $academicoDato->id;
                $inscripcion->save();

                $this->info("✓ Inscripción #{$inscripcion->id} completada con academico_dato_id #{$academicoDato->id}");
                $completadas++;

            } catch (\Exception $e) {
                $this->error("✗ Error en inscripción #{$inscripcion->id}: {$e->getMessage()}");
                $errores++;
            }
        }

        $this->newLine();
        $this->info("Proceso completado:");
        $this->info("  - Completadas: $completadas");
        $this->info("  - Errores: $errores");

        return Command::SUCCESS;
    }
}
