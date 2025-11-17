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
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['parcial', 'recuperatorio', 'examen_final', 'trabajo_practico'])->default('parcial');
            $table->date('fecha');
            $table->decimal('peso_porcentual', 5, 2)->default(100.00)->comment('Peso % de la evaluación en la nota final');
            $table->foreignId('comision_id')->nullable()->constrained('comisiones')->cascadeOnDelete();
            $table->year('anio')->comment('Año académico');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['anio', 'tipo']);
            $table->index('fecha');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
