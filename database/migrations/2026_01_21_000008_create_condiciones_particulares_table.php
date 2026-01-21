<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Tabla para registrar condiciones particulares de estudiantes (RF13).
     */
    public function up(): void
    {
        Schema::create('condiciones_particulares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();

            // Tipo de condición
            $table->enum('tipo', [
                'discapacidad',
                'enfermedad_cronica',
                'situacion_laboral',
                'situacion_familiar',
                'condicionalidad_academica',
                'otra'
            ]);

            // Descripción
            $table->string('titulo', 100);
            $table->text('descripcion');

            // Adecuaciones requeridas
            $table->boolean('requiere_adecuacion')->default(false);
            $table->text('adecuaciones_sugeridas')->nullable();
            $table->text('adecuaciones_aprobadas')->nullable();

            // Documentación
            $table->boolean('documentacion_presentada')->default(false);
            $table->string('archivo_documentacion')->nullable();

            // Vigencia
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable()->comment('NULL = vigencia indefinida');
            $table->boolean('activa')->default(true);

            // Auditoría
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['inscripcion_id', 'activa']);
            $table->index('tipo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('condiciones_particulares');
    }
};
