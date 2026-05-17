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
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('anio_cursado')->default(1)->comment('1=primer año, 2=segundo, etc.');
            $table->integer('especialidad_id_sysacad')->nullable()->comment('NULL = común a todas las especialidades');
            $table->boolean('es_nivelacion')->default(false)->comment('Si es materia de nivelación/ingreso');
            $table->integer('carga_horaria')->nullable()->comment('Horas semanales');
            $table->enum('tipo', ['obligatoria', 'optativa', 'nivelacion'])->default('obligatoria');
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('especialidad_id_sysacad');
            $table->index('es_nivelacion');
            $table->index('activa');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
