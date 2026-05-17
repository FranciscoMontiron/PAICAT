<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Desacopla asistencias de la comisión - ahora pertenecen a inscripcion + materia.
     * Esto permite que si un alumno cambia de comisión, mantenga su historial de asistencias.
     */
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Agregar inscripcion_id - la asistencia pertenece a la inscripción del alumno
            $table->foreignId('inscripcion_id')
                ->nullable()
                ->after('id')
                ->constrained('inscripciones')
                ->cascadeOnDelete();

            // Agregar materia_id - especifica a qué materia corresponde la asistencia
            $table->foreignId('materia_id')
                ->nullable()
                ->after('inscripcion_id')
                ->constrained('materias')
                ->cascadeOnDelete();

            // Mantener inscripcion_comision_id como nullable para compatibilidad
            // (saber en qué comisión estaba cuando se tomó la asistencia)
            $table->foreignId('inscripcion_comision_id')
                ->nullable()
                ->change();

            // Nuevos índices
            $table->index('inscripcion_id');
            $table->index('materia_id');
            $table->index(['inscripcion_id', 'materia_id', 'fecha']);
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropIndex(['inscripcion_id', 'materia_id', 'fecha']);
            $table->dropForeign(['inscripcion_id']);
            $table->dropForeign(['materia_id']);
            $table->dropColumn(['inscripcion_id', 'materia_id']);
        });
    }
};
