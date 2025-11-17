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
        Schema::create('sysacad_escuelas', function (Blueprint $table) {
            $table->id();
            $table->string('cue', 20)->unique()->comment('Clave Única de Establecimiento');
            $table->string('nombre', 250);
            $table->enum('gestion', ['Estatal', 'Privado'])->default('Estatal');
            $table->enum('ambito', ['Urbano', 'Rural'])->default('Urbano');
            $table->enum('tecnica', ['SI', 'NO'])->default('NO');
            $table->string('domicilio_localidad', 200)->nullable();
            $table->foreignId('localidad_id')->nullable()->constrained('sysacad_localidades')->nullOnDelete();
            $table->timestamps();

            $table->index('nombre');
            $table->index('gestion');
            $table->index('localidad_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysacad_escuelas');
    }
};
