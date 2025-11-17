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
        Schema::create('cronograma_actividades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['clase', 'evaluacion', 'evento', 'feriado', 'otro'])->default('clase');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->foreignId('comision_id')->nullable()->constrained('comisiones')->cascadeOnDelete();
            $table->year('anio')->nullable()->comment('Año académico');
            $table->timestamps();

            $table->index(['anio', 'tipo']);
            $table->index('fecha_inicio');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('cronograma_actividades');
    }
};
