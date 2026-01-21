<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Agrega materia_id a evaluaciones - las instancias de evaluación pertenecen a materias, no a comisiones.
     */
    public function up(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            // Agregar materia_id - la evaluación pertenece a la materia
            $table->foreignId('materia_id')
                ->nullable()
                ->after('comision_id')
                ->constrained('materias')
                ->nullOnDelete();

            // comision_id ahora es opcional (puede definirse evaluación a nivel materia)
            // Ya es nullable en la migración original

            $table->index('materia_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->dropForeign(['materia_id']);
            $table->dropColumn('materia_id');
        });
    }
};
