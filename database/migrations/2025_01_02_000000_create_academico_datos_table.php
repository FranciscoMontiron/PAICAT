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
        Schema::create('academico_datos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('especialidad_id')->nullable();
            $table->integer('especialidad_alternativa_id')->nullable();
            $table->year('ingreso_carrera')->nullable();
            $table->year('egreso_secundaria')->nullable();
            $table->enum('modalidad', ['Presencial', 'Virtual', 'Semipresencial'])->default('Presencial');
            $table->string('turno_ingreso', 50)->nullable();
            $table->string('turno_carrera', 50)->nullable();
            $table->enum('tipo_ingreso', ['Intensivo', 'Extensivo'])->default('Extensivo');
            $table->integer('sede')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'egresado', 'desertado'])->default('activo');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('estado');
            $table->index('ingreso_carrera');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('academico_datos');
    }
};

