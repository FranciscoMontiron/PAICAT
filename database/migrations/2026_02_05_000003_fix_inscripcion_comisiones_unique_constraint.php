<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Corregir el constraint único de inscripcion_comisiones para usar inscripcion_id
     */
    public function up(): void
    {
        Schema::table('inscripcion_comisiones', function (Blueprint $table) {
            // Asegurar índice individual en academico_dato_id para no romper FKs
            $table->index('academico_dato_id', 'inscripcion_comisiones_academico_dato_id_index');
            // Eliminar el constraint único obsoleto (academico_dato_id, comision_id)
            $table->dropUnique('inscripcion_comisiones_academico_dato_id_comision_id_unique');
        });

        // Eliminar registros duplicados si existen (mismo inscripcion_id y comision_id activos)
        DB::statement("
            DELETE ic1 FROM inscripcion_comisiones ic1
            INNER JOIN inscripcion_comisiones ic2 
            WHERE ic1.id > ic2.id 
            AND ic1.inscripcion_id = ic2.inscripcion_id 
            AND ic1.comision_id = ic2.comision_id
            AND ic1.estado IN ('inscripto', 'confirmado', 'aprobado')
            AND ic2.estado IN ('inscripto', 'confirmado', 'aprobado')
            AND ic1.deleted_at IS NULL
            AND ic2.deleted_at IS NULL
        ");

        Schema::table('inscripcion_comisiones', function (Blueprint $table) {
            // Agregar el nuevo constraint único correcto (inscripcion_id, comision_id)
            // Solo para registros activos (no cancelados ni trasladados)
            // MySQL no permite índices condicionales nativamente, así que usamos único simple
            $table->unique(['inscripcion_id', 'comision_id'], 'inscripcion_comisiones_inscripcion_comision_unique');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('inscripcion_comisiones', function (Blueprint $table) {
            // Eliminar el constraint nuevo
            $table->dropUnique('inscripcion_comisiones_inscripcion_comision_unique');
            
            // Restaurar el constraint antiguo
            $table->unique(['academico_dato_id', 'comision_id'], 'inscripcion_comisiones_academico_dato_id_comision_id_unique');
        });
    }
};
