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
        Schema::create('inscripcion_comisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academico_dato_id')->constrained('academico_datos')->cascadeOnDelete();
            $table->foreignId('comision_id')->constrained('comisiones')->cascadeOnDelete();
            $table->dateTime('fecha_inscripcion')->default(now());
            $table->enum('estado', ['inscripto', 'confirmado', 'cancelado', 'trasladado'])->default('inscripto');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academico_dato_id', 'comision_id']);
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion_comisiones');
    }
};
