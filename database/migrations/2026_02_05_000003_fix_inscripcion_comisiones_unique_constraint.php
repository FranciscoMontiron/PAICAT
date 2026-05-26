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
        $conn = DB::connection('paicat');
        
        Schema::table('inscripcion_comisiones', function (Blueprint $table) {
            // Asegurar índice individual en academico_dato_id para no romper FKs
            $table->index('academico_dato_id', 'inscripcion_comisiones_academico_dato_id_index');
            // Eliminar el constraint único obsoleto (academico_dato_id, comision_id)
            $table->dropUnique('inscripcion_comisiones_academico_dato_id_comision_id_unique');
        });

        // Verificar si el unique antiguo aún existe (puede haber sido eliminado en una ejecución parcial)
        $indexExists = $conn->select("
            SHOW INDEX FROM inscripcion_comisiones
            WHERE Key_name = 'inscripcion_comisiones_academico_dato_id_comision_id_unique'
        ");

        if (!empty($indexExists)) {
            Schema::connection('paicat')->table('inscripcion_comisiones', function (Blueprint $table) {
                // Eliminar la FK que depende del índice único
                $table->dropForeign('inscripcion_comisiones_academico_dato_id_foreign');
                // Eliminar el constraint único obsoleto
                $table->dropUnique('inscripcion_comisiones_academico_dato_id_comision_id_unique');
                // Re-crear la FK con un índice simple
                $table->foreign('academico_dato_id')
                      ->references('id')
                      ->on('academico_datos')
                      ->onDelete('cascade');
            });
        }

        // Hard-delete registros soft-deleted que generan duplicados en (inscripcion_id, comision_id)
        $conn->statement("
            DELETE ic1 FROM inscripcion_comisiones ic1
            INNER JOIN inscripcion_comisiones ic2
            ON ic1.inscripcion_id = ic2.inscripcion_id
            AND ic1.comision_id = ic2.comision_id
            AND ic1.id <> ic2.id
            WHERE ic1.deleted_at IS NOT NULL
        ");

        // Eliminar duplicados activos restantes (conservar el más reciente)
        $conn->statement("
            DELETE ic1 FROM inscripcion_comisiones ic1
            INNER JOIN inscripcion_comisiones ic2
            ON ic1.inscripcion_id = ic2.inscripcion_id
            AND ic1.comision_id = ic2.comision_id
            AND ic1.id < ic2.id
            WHERE ic1.deleted_at IS NULL
            AND ic2.deleted_at IS NULL
        ");

        // Verificar si el nuevo unique ya existe (ejecución parcial previa)
        $newIndexExists = $conn->select("
            SHOW INDEX FROM inscripcion_comisiones
            WHERE Key_name = 'inscripcion_comisiones_inscripcion_comision_unique'
        ");

        if (empty($newIndexExists)) {
            Schema::connection('paicat')->table('inscripcion_comisiones', function (Blueprint $table) {
                $table->unique(['inscripcion_id', 'comision_id'], 'inscripcion_comisiones_inscripcion_comision_unique');
            });
        }
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::connection('paicat')->table('inscripcion_comisiones', function (Blueprint $table) {
            $table->dropUnique('inscripcion_comisiones_inscripcion_comision_unique');
            $table->dropForeign('inscripcion_comisiones_academico_dato_id_foreign');
            $table->unique(['academico_dato_id', 'comision_id'], 'inscripcion_comisiones_academico_dato_id_comision_id_unique');
            $table->foreign('academico_dato_id')
                  ->references('id')
                  ->on('academico_datos')
                  ->onDelete('cascade');
        });
    }
};
