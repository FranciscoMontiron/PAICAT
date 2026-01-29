<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla municipios: para cursos presenciales por sede.
     * La Plata, Chascomús, Brandsen, Magdalena, etc.
     */
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->nullable()->unique();
            $table->string('direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('activo');
        });

        // Agregar campo municipio_id a comisiones
        Schema::table('comisiones', function (Blueprint $table) {
            $table->foreignId('municipio_id')->nullable()->after('modalidad')->constrained('municipios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
            $table->dropColumn('municipio_id');
        });

        Schema::dropIfExists('municipios');
    }
};
