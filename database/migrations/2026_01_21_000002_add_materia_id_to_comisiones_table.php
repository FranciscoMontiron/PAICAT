<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Agrega materia_id a comisiones para vincular comisiones con materias.
     */
    public function up(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->foreignId('materia_id')
                ->nullable()
                ->after('id')
                ->constrained('materias')
                ->nullOnDelete();

            $table->index('materia_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropForeign(['materia_id']);
            $table->dropColumn('materia_id');
        });
    }
};
