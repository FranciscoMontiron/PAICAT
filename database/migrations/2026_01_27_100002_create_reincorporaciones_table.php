<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla reincorporaciones: permite reincorporar estudiantes a cursadas.
     */
    public function up(): void
    {
        Schema::create('reincorporaciones', function (Blueprint $table) {
            $table->id();

            // Relación con cursada
            $table->foreignId('cursada_id')->constrained('cursadas')->cascadeOnDelete();

            // Fecha de reincorporación
            $table->date('fecha');

            // Motivo de la reincorporación
            $table->string('motivo', 255)->nullable();

            // Estado anterior (para historial)
            $table->string('estado_anterior', 50)->nullable();

            // Usuario que autorizó
            $table->foreignId('autorizado_por_id')->nullable()->constrained('users')->nullOnDelete();

            // Observaciones
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reincorporaciones');
    }
};
