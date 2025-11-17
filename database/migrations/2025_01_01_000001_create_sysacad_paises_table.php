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
        Schema::create('sysacad_paises', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique()->comment('ID original de Sysacad');
            $table->string('nombre', 100);
            $table->timestamps();

            $table->index('nombre');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysacad_paises');
    }
};
