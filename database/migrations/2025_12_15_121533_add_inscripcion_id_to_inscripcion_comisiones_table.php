<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscripcion_comisiones', function (Blueprint $table) {
            // Agregar inscripcion_id como columna nullable
            // Hacerla nullable para no romper datos existentes
            $table->unsignedBigInteger('inscripcion_id')->nullable()->after('id');

            // Agregar foreign key
            $table->foreign('inscripcion_id')
                  ->references('id')
                  ->on('inscripciones')
                  ->onDelete('cascade');

            // Hacer academico_dato_id nullable ya que ahora es opcional
            $table->unsignedBigInteger('academico_dato_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripcion_comisiones', function (Blueprint $table) {
            $table->dropForeign(['inscripcion_id']);
            $table->dropColumn('inscripcion_id');

            // Revertir academico_dato_id a NOT NULL
            $table->unsignedBigInteger('academico_dato_id')->nullable(false)->change();
        });
    }
};
