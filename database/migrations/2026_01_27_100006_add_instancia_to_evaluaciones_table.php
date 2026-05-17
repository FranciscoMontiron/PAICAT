<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agregar instancia y cuenta_promedio a evaluaciones.
     * Instancia: 1, 2, 3 para primera, segunda, tercera instancia de parciales/recuperatorios.
     * cuenta_promedio: si la evaluación se incluye en el cálculo del promedio.
     */
    public function up(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            // Instancia de evaluación (1er parcial, 2do parcial, 3er parcial, etc.)
            $table->unsignedTinyInteger('instancia')->nullable()->after('tipo')
                ->comment('Instancia de evaluación: 1, 2, 3...');

            // Indica si esta evaluación cuenta para el promedio
            $table->boolean('cuenta_promedio')->default(true)->after('instancia')
                ->comment('Si esta evaluación cuenta para el promedio');

            // Relación con evaluación padre (para recuperatorios que recuperan un parcial específico)
            $table->foreignId('evaluacion_padre_id')->nullable()->after('cuenta_promedio')
                ->constrained('evaluaciones')->nullOnDelete()
                ->comment('Evaluación que recupera este recuperatorio');

            // Índices
            $table->index('instancia');
        });

        // Actualizar el enum de tipo para incluir 'integrador'
        // En MySQL se hace así:
        DB::statement("ALTER TABLE evaluaciones MODIFY COLUMN tipo ENUM('parcial', 'recuperatorio', 'examen_final', 'trabajo_practico', 'integrador') DEFAULT 'parcial'");
    }

    public function down(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->dropForeign(['evaluacion_padre_id']);
            $table->dropIndex(['instancia']);
            $table->dropColumn(['instancia', 'cuenta_promedio', 'evaluacion_padre_id']);
        });

        // Revertir el enum
        DB::statement("ALTER TABLE evaluaciones MODIFY COLUMN tipo ENUM('parcial', 'recuperatorio', 'examen_final', 'trabajo_practico') DEFAULT 'parcial'");
    }
};
