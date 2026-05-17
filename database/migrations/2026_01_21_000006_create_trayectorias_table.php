<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Tabla para gestionar el historial de trayectorias estudiantiles.
     */
    public function up(): void
    {
        Schema::create('trayectorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();

            // Estado de la trayectoria
            $table->enum('estado', [
                'activo',       // Cursando normalmente
                'pausado',      // Pausa temporal (ej: por razones personales)
                'libre',        // Condición de libre por inasistencias
                'baja',         // Baja del curso
                'reincorporado', // Reincorporado tras baja/libre
                'aprobado',     // Aprobó el curso
                'desaprobado'   // No aprobó el curso
            ])->default('activo');

            // Fechas
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            // Información de baja/cambio
            $table->text('motivo')->nullable()->comment('Motivo del cambio de estado');
            $table->boolean('es_voluntaria')->nullable()->comment('Si es baja, fue voluntaria?');

            // Auditoría
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['inscripcion_id', 'estado']);
            $table->index('fecha_inicio');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('trayectorias');
    }
};
