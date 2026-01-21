<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Desacopla notas de la comisión - ahora pertenecen a inscripcion + evaluación.
     * Esto permite que si un alumno cambia de comisión, mantenga sus notas.
     */
    public function up(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            // Agregar inscripcion_id - la nota pertenece a la inscripción del alumno
            $table->foreignId('inscripcion_id')
                ->nullable()
                ->after('id')
                ->constrained('inscripciones')
                ->cascadeOnDelete();

            // Mantener inscripcion_comision_id como nullable para compatibilidad
            $table->foreignId('inscripcion_comision_id')
                ->nullable()
                ->change();

            // Nuevos índices
            $table->index('inscripcion_id');
            $table->index(['inscripcion_id', 'evaluacion_id']);
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            $table->dropIndex(['inscripcion_id', 'evaluacion_id']);
            $table->dropForeign(['inscripcion_id']);
            $table->dropColumn('inscripcion_id');
        });
    }
};
