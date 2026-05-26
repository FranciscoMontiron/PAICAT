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
        Schema::create('difusiones', function (Blueprint $table) {
            $table->id();
            $table->string('asunto');
            $table->longText('mensaje');
            $table->string('modalidad')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->integer('enviado_a')->default(0)->comment('Cantidad de destinatarios');
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('difusiones');
    }
};
