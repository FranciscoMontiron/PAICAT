<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'alumnos_utn';

    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        // Tabla persons - Datos personales de los alumnos
        Schema::connection('alumnos_utn')->create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('cuit')->unique()->nullable();
            $table->string('cuit_captura')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('documento')->unique();
            $table->string('documento_captura')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('pais_documento')->nullable();
            $table->string('pais_origen')->nullable();
            $table->date('nacimiento_fecha')->nullable();
            $table->string('sexo')->nullable();
            $table->string('genero')->nullable();
            $table->string('numero_casa')->nullable();
            $table->integer('pais_residencia')->nullable();
            $table->integer('prov_residencia')->nullable();
            $table->integer('loc_residencia')->nullable();
            $table->integer('part_residencia')->nullable();
            $table->string('piso')->nullable();
            $table->string('departamento')->nullable();
            $table->string('direccion')->nullable();
            $table->string('barrio')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('telefono_fijo')->nullable();
            $table->string('telefono_celular')->nullable();
            $table->string('telefono_emergencia')->nullable();
            $table->string('contacto_emergencia')->nullable();
            $table->string('email')->unique();
            $table->enum('__proceso', ['Alta', 'Modificacion'])->default('Alta');
            $table->enum('__estado', ['Pendiente', 'Verificado'])->default('Pendiente');
            $table->string('__usuario');
            $table->timestamps();
        });

        // Tabla academico_datos - Datos académicos del ingreso
        Schema::connection('alumnos_utn')->create('academico_datos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->integer('especialidad_id');
            $table->integer('especialidad_alternativa_id')->nullable();
            $table->year('ingreso_carrera');
            $table->year('egreso_secundaria');
            $table->enum('modalidad', ['Presencial', 'Virtual', 'Semipresencial'])->default('Presencial');
            $table->string('turno_ingreso');
            $table->string('turno_carrera');
            $table->enum('tipo_ingreso', ['Intensivo', 'Extensivo'])->default('Extensivo');
            $table->integer('sede');
            $table->timestamps();

            $table->index('person_id');
        });

        // Tabla secundaria_datos - Datos del secundario
        Schema::connection('alumnos_utn')->create('secundaria_datos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->string('titulo')->nullable();
            $table->string('institucion')->nullable();
            $table->string('anio_egreso')->nullable();
            $table->string('tipo_titulo')->nullable();
            $table->string('promedio')->nullable();
            $table->integer('pais_estudio')->nullable();
            $table->integer('prov_estudio')->nullable();
            $table->integer('loc_estudio')->nullable();
            $table->integer('part_estudio')->nullable();
            $table->string('tipo_certificado')->nullable();
            $table->string('certificado_captura')->nullable();
            $table->timestamps();

            $table->index('person_id');
        });

        // Tabla formulario_datos - Estado del formulario de inscripción
        Schema::connection('alumnos_utn')->create('formulario_datos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->enum('estado', ['Incompleto', 'Completo'])->default('Incompleto');
            $table->timestamps();

            $table->index('person_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::connection('alumnos_utn')->dropIfExists('formulario_datos');
        Schema::connection('alumnos_utn')->dropIfExists('secundaria_datos');
        Schema::connection('alumnos_utn')->dropIfExists('academico_datos');
        Schema::connection('alumnos_utn')->dropIfExists('persons');
    }
};
