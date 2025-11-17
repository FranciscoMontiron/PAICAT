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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->cascadeOnDelete();
            $table->foreignId('inscripcion_comision_id')->constrained('inscripcion_comisiones')->cascadeOnDelete();
            $table->decimal('nota', 4, 2)->comment('Nota de 0.00 a 10.00');
            $table->dateTime('fecha_carga')->default(now());
            $table->foreignId('cargado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['evaluacion_id', 'inscripcion_comision_id']);
            $table->index('nota');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
