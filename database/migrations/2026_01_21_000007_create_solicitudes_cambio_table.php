<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Tabla para gestionar solicitudes de cambio de comisión/modalidad/carrera.
     */
    public function up(): void
    {
        Schema::create('solicitudes_cambio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();

            // Tipo de solicitud
            $table->enum('tipo', ['comision', 'modalidad', 'carrera', 'turno']);

            // Para cambio de comisión
            $table->foreignId('comision_origen_id')->nullable()->constrained('comisiones')->nullOnDelete();
            $table->foreignId('comision_destino_id')->nullable()->constrained('comisiones')->nullOnDelete();

            // Para cambio de modalidad
            $table->string('modalidad_origen', 50)->nullable();
            $table->string('modalidad_destino', 50)->nullable();

            // Para cambio de turno
            $table->string('turno_origen', 50)->nullable();
            $table->string('turno_destino', 50)->nullable();

            // Para cambio de carrera/especialidad
            $table->integer('especialidad_origen_id')->nullable();
            $table->integer('especialidad_destino_id')->nullable();

            // Detalles de la solicitud
            $table->text('motivo');
            $table->enum('estado', [
                'pendiente',
                'en_revision',
                'aprobada',
                'rechazada',
                'cancelada',
                'trueque_detectado' // RF11: Cuando se detecta match inverso
            ])->default('pendiente');

            // Para detección de trueques (RF11)
            $table->foreignId('solicitud_trueque_id')->nullable()
                ->comment('ID de la solicitud con la que hace trueque');

            // Respuesta
            $table->text('motivo_rechazo')->nullable();
            $table->foreignId('procesado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_procesamiento')->nullable();

            // Auditoría
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['inscripcion_id', 'estado']);
            $table->index(['tipo', 'estado']);
            $table->index('comision_destino_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_cambio');
    }
};
