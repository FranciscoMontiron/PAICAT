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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['asistencia', 'notas', 'estadisticas', 'inscripciones', 'otro'])->default('estadisticas');
            $table->string('nombre', 200);
            $table->json('parametros')->nullable()->comment('Parámetros utilizados para generar el reporte');
            $table->foreignId('generado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('url_archivo', 255)->nullable();
            $table->timestamps();

            $table->index(['tipo', 'created_at']);
            $table->index('generado_por');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
