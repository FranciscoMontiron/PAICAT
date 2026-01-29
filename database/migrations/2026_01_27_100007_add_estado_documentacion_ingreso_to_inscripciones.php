<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Separar estado en estado_documentacion y estado_ingreso.
     *
     * estado_documentacion: pendiente, validada, incompleta, rechazada
     * estado_ingreso: inscripto, cursando, aprobado, desaprobado, libre, baja, cancelado
     */
    public function up(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            // Estado de la documentación
            $table->string('estado_documentacion', 20)->default('pendiente')->after('estado')
                ->comment('pendiente, validada, incompleta, rechazada');

            // Estado del ingreso/cursada
            $table->string('estado_ingreso', 20)->default('inscripto')->after('estado_documentacion')
                ->comment('inscripto, cursando, aprobado, desaprobado, libre, baja, cancelado');

            // Indices
            $table->index('estado_documentacion');
            $table->index('estado_ingreso');
        });

        // Migrar datos del campo estado actual a los nuevos campos
        DB::table('inscripciones')->where('estado', 'pendiente')->update([
            'estado_documentacion' => 'pendiente',
            'estado_ingreso' => 'inscripto',
        ]);

        DB::table('inscripciones')->where('estado', 'documentacion_ok')->update([
            'estado_documentacion' => 'validada',
            'estado_ingreso' => 'inscripto',
        ]);

        DB::table('inscripciones')->where('estado', 'confirmado')->update([
            'estado_documentacion' => 'validada',
            'estado_ingreso' => 'cursando',
        ]);

        DB::table('inscripciones')->where('estado', 'cancelado')->update([
            'estado_documentacion' => 'pendiente', // no importa
            'estado_ingreso' => 'cancelado',
        ]);

        DB::table('inscripciones')->where('estado', 'baja')->update([
            'estado_documentacion' => 'validada', // asumimos que tenía docs
            'estado_ingreso' => 'baja',
        ]);
    }

    public function down(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropIndex(['estado_documentacion']);
            $table->dropIndex(['estado_ingreso']);
            $table->dropColumn(['estado_documentacion', 'estado_ingreso']);
        });
    }
};
