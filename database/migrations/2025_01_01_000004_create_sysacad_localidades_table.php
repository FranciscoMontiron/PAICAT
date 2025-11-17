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
        Schema::create('sysacad_localidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->foreignId('provincia_id')->nullable()->constrained('sysacad_provincias')->nullOnDelete();
            $table->foreignId('partido_id')->nullable()->constrained('sysacad_partidos')->nullOnDelete();
            $table->integer('provincia_id_sysacad')->nullable()->comment('ID de la provincia en Sysacad');
            $table->timestamps();

            $table->index('nombre');
            $table->index('provincia_id');
            $table->index('partido_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysacad_localidades');
    }
};
