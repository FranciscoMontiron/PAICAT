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
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->unique();
            $table->text('descripcion')->nullable();
            $table->year('anio')->comment('Año académico');
            $table->enum('periodo', ['Verano', 'Invierno', 'Anual'])->default('Verano');
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche'])->default('Mañana');
            $table->enum('modalidad', ['Presencial', 'Virtual', 'Semipresencial'])->default('Presencial');
            $table->integer('cupo_maximo')->default(40);
            $table->integer('cupo_actual')->default(0);
            $table->foreignId('docente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['activa', 'cerrada', 'finalizada', 'cancelada'])->default('activa');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['anio', 'periodo']);
            $table->index('estado');
            $table->index('docente_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};

