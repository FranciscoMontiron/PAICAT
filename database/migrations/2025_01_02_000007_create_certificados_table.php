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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academico_dato_id')->constrained('academico_datos')->cascadeOnDelete();
            $table->enum('tipo', ['aprobacion', 'asistencia', 'inscripcion'])->default('inscripcion');
            $table->string('numero_certificado', 50)->unique();
            $table->date('fecha_emision');
            $table->foreignId('emitido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('url_pdf', 255)->nullable();
            $table->string('hash_verificacion', 64)->unique()->comment('Hash SHA256 para verificación');
            $table->timestamps();

            $table->index(['academico_dato_id', 'tipo']);
            $table->index('fecha_emision');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
