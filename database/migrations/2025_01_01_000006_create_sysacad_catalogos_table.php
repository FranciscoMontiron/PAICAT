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
        // Tabla de especialidades
        Schema::create('sysacad_especialidades', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        // Tabla de títulos secundarios
        Schema::create('sysacad_titulos_secundarios', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 150);
            $table->timestamps();
        });

        // Tabla de estados civiles
        Schema::create('sysacad_estados_civiles', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        // Tabla de nacionalidades
        Schema::create('sysacad_nacionalidades', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        // Tabla de géneros
        Schema::create('sysacad_generos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        // Tabla de sexos
        Schema::create('sysacad_sexos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        // Tabla de turnos
        Schema::create('sysacad_turnos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        // Tabla de modalidades
        Schema::create('sysacad_modalidades', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        // Tabla de tipos de ingreso
        Schema::create('sysacad_tipos_ingreso', function (Blueprint $table) {
            $table->id();
            $table->integer('id_sysacad')->unique();
            $table->string('nombre', 50);
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysacad_tipos_ingreso');
        Schema::dropIfExists('sysacad_modalidades');
        Schema::dropIfExists('sysacad_turnos');
        Schema::dropIfExists('sysacad_sexos');
        Schema::dropIfExists('sysacad_generos');
        Schema::dropIfExists('sysacad_nacionalidades');
        Schema::dropIfExists('sysacad_estados_civiles');
        Schema::dropIfExists('sysacad_titulos_secundarios');
        Schema::dropIfExists('sysacad_especialidades');
    }
};
