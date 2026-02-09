<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArreglarTipos extends Command
{
    protected $signature = 'fix:typos';
    protected $description = 'Arreglar typeos en turnos y modalidades';

    public function handle()
    {
        $this->info('Arreglando typos en datos...');

        // Arreglar turno "manaña" -> "mañana" en inscripciones
        $inscripciones = DB::connection('paicat')->table('inscripciones')
            ->where('turno_ingreso', 'manaña')
            ->update(['turno_ingreso' => 'mañana']);
        $this->line("✓ Inscripciones: {$inscripciones} registros actualizados");

        // Arreglar turno "manaña" -> "mañana" en comisiones
        $comisiones = DB::connection('paicat')->table('comisiones')
            ->where('turno', 'manaña')
            ->update(['turno' => 'mañana']);
        $this->line("✓ Comisiones turno: {$comisiones} registros actualizados");

        // Arreglar modalidad de comisión SEMI-EXT-M
        $modalidades = DB::connection('paicat')->table('comisiones')
            ->where('codigo', 'SEMI-EXT-M')
            ->update(['modalidad' => 'Semipresencial']);
        $this->line("✓ Comisiones modalidad: {$modalidades} registros actualizados");

        $this->newLine();
        $this->info('✓ Datos corregidos exitosamente');

        return 0;
    }
}
