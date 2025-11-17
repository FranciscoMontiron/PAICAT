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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_comision_id')->constrained('inscripcion_comisiones')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'justificado'])->default('ausente');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['inscripcion_comision_id', 'fecha']);
            $table->index('fecha');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
