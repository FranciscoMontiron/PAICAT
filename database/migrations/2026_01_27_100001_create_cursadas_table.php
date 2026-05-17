<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla cursadas: representa cada instancia de un estudiante cursando una comisión.
     * Permite histórico y repetidores (mismo alumno puede tener múltiples cursadas).
     */
    public function up(): void
    {
        Schema::create('cursadas', function (Blueprint $table) {
            $table->id();

            // Relación con inscripción (estudiante)
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();

            // Relación con comisión
            $table->foreignId('comision_id')->constrained('comisiones')->cascadeOnDelete();

            // Año de la cursada
            $table->integer('anio');

            // Estado de la cursada
            $table->enum('estado', [
                'cursando',      // Actualmente cursando
                'aprobado',      // Aprobó
                'desaprobado',   // Desaprobó
                'libre',         // Quedó libre (por faltas o no rindió)
                'abandono',      // Abandonó
                'baja',          // Baja administrativa
            ])->default('cursando');

            // Modalidad de cursada (puede diferir de la comisión en casos especiales)
            $table->string('modalidad', 50)->nullable();

            // Resultado final (nota promedio, observaciones finales)
            $table->string('resultado', 100)->nullable();

            // Nota final numérica (para promedios)
            $table->decimal('nota_final', 4, 2)->nullable();

            // Es recursante (está repitiendo)
            $table->boolean('es_recursante')->default(false);

            // Fechas
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            // Quién registró el cambio de estado
            $table->foreignId('usuario_cambio_estado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_cambio_estado')->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('estado');
            $table->index('anio');
            $table->index(['inscripcion_id', 'anio']);

            // NO tiene unique en inscripcion_id + comision_id para permitir repetidores
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursadas');
    }
};
