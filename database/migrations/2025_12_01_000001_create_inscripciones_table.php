<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();

            // Referencia al alumno en alumnos_utn.persons (solo ID, sin FK por ser otra BD)
            $table->unsignedBigInteger('person_id')->comment('ID del alumno en alumnos_utn.persons');

            // Referencia a datos académicos en alumnos_utn.academico_datos
            $table->unsignedBigInteger('academico_dato_id')->nullable()->comment('ID en alumnos_utn.academico_datos');

            // Datos de la inscripción en PAICAT
            $table->year('anio_ingreso');
            $table->integer('especialidad_id_sysacad')->comment('ID de especialidad en sysacad');
            $table->integer('especialidad_alternativa_id_sysacad')->nullable()->comment('ID de especialidad alternativa en sysacad');
            $table->enum('modalidad', ['Presencial', 'Virtual', 'Semipresencial'])->default('Presencial');
            $table->string('turno_ingreso', 50)->nullable();
            $table->string('turno_carrera', 50)->nullable();
            $table->enum('tipo_ingreso', ['Intensivo', 'Extensivo'])->default('Extensivo');
            $table->integer('sede_id_sysacad')->nullable()->comment('ID de sede en sysacad');

            // Estado de la inscripción
            $table->enum('estado', [
                'pendiente',        // Inscripción iniciada, documentación pendiente
                'documentacion_ok', // Documentación validada
                'confirmado',       // Inscripción confirmada
                'cancelado',        // Inscripción cancelada
                'baja'              // Baja del curso
            ])->default('pendiente');

            // Validación de documentación
            $table->boolean('doc_dni_validado')->default(false);
            $table->boolean('doc_titulo_validado')->default(false);
            $table->boolean('doc_analitico_validado')->default(false);
            $table->text('observaciones_documentacion')->nullable();

            // Auditoría
            $table->foreignId('usuario_registro_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_validacion_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_validacion')->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->unique(['person_id', 'anio_ingreso'], 'inscripcion_persona_anio_unique');
            $table->index('estado');
            $table->index('anio_ingreso');
            $table->index('especialidad_id_sysacad');
            $table->index('modalidad');
            $table->index('tipo_ingreso');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
