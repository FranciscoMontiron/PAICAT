<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla aulas: espacios físicos asociados a comisiones.
     */
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->nullable();
            $table->integer('capacidad')->nullable();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();
            $table->string('ubicacion', 255)->nullable(); // Edificio, piso, etc.
            $table->boolean('activa')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('activa');
            $table->unique(['codigo', 'municipio_id']);
        });

        // Agregar campo aula_id a comisiones
        Schema::table('comisiones', function (Blueprint $table) {
            $table->foreignId('aula_id')->nullable()->after('municipio_id')->constrained('aulas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropForeign(['aula_id']);
            $table->dropColumn('aula_id');
        });

        Schema::dropIfExists('aulas');
    }
};
