<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalcularCupos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paicat:recalcular-cupos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula el cupo_actual de todas las comisiones basándose en las inscripciones activas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculando cupos de comisiones...');

        $comisiones = \App\Models\Comision::all();

        foreach ($comisiones as $comision) {
            $cantidadInscriptos = $comision->inscripciones()
                ->whereIn('estado', ['inscripto', 'confirmado']) // Solo inscripciones válidas ocupan cupo
                ->count();

            $this->line("Comisión {$comision->codigo}: Cupo actual {$comision->cupo_actual} -> Nuevo: {$cantidadInscriptos}");

            $comision->cupo_actual = $cantidadInscriptos;
            $comision->save();
        }

        $this->info('Recálculo completado.');
    }
}
