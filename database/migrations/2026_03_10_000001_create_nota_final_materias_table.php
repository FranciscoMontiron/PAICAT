<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_final_materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();
            $table->foreignId('comision_id')->constrained('comisiones')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->decimal('nota_final', 4, 2)->nullable()->comment('Nota final puesta por el docente (0-10)');
            $table->foreignId('cargado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['inscripcion_id', 'comision_id', 'materia_id'], 'nfm_inscripcion_comision_materia_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_final_materias');
    }
};
