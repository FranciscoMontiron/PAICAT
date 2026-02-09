<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArreglarTiposOrigen extends Command
{
    protected $signature = 'fix:typos-origen';
    protected $description = 'Arreglar typos en la base de datos de origen (alumnos_utn)';

    public function handle()
    {
        $this->info('Arreglando typos en base de datos de origen...');

        // Arreglar turno "manaña" -> "mañana" en academico_datos (origen)
        $academico = DB::connection('alumnos_utn')->table('academico_datos')
            ->where('turno_ingreso', 'manaña')
            ->update(['turno_ingreso' => 'mañana']);
        $this->line("✓ Academico_datos (origen): {$academico} registros actualizados");

        // Arreglar turno "manaña" -> "mañana" en academico_datos tabla turno_carrera también
        $turnoCarrera = DB::connection('alumnos_utn')->table('academico_datos')
            ->where('turno_carrera', 'manaña')
            ->update(['turno_carrera' => 'mañana']);
        $this->line("✓ Turno carrera (origen): {$turnoCarrera} registros actualizados");

        $this->newLine();
        $this->info('✓ Base de datos de origen corregida');
        $this->warn('⚠ Las inscripciones ya importadas deben corregirse con: php artisan fix:typos');

        return 0;
    }
}
