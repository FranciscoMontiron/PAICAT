<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cambia el constraint único de (person_id, anio_ingreso) a solo person_id.
     * Solo puede haber UNA inscripción por alumno. Al reinscribirse se actualiza
     * la existente y se registra en trayectorias.
     */
    public function up(): void
    {
        Schema::connection('paicat')->table('inscripciones', function (Blueprint $table) {
            $table->dropUnique('inscripcion_persona_anio_unique');
            $table->unique('person_id', 'inscripcion_persona_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('paicat')->table('inscripciones', function (Blueprint $table) {
            $table->dropUnique('inscripcion_persona_unique');
            $table->unique(['person_id', 'anio_ingreso'], 'inscripcion_persona_anio_unique');
        });
    }
};
