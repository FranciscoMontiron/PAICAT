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
        Schema::create('sysacad_provincias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->foreignId('pais_id')->nullable()->constrained('sysacad_paises')->nullOnDelete();
            $table->integer('id_sysacad')->unique()->comment('ID original de Sysacad');
            $table->integer('pais_id_sysacad')->nullable()->comment('ID del país en Sysacad');
            $table->timestamps();

            $table->index('nombre');
            $table->index('pais_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysacad_provincias');
    }
};
