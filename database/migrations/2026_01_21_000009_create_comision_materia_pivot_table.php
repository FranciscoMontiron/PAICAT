<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear tabla pivote para relación N:M entre comisiones y materias.
     * Una comisión puede tener varias materias.
     */
    public function up(): void
    {
        Schema::create('comision_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comision_id')->constrained('comisiones')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['comision_id', 'materia_id']);
        });

        // Migrar datos existentes de materia_id en comisiones a la tabla pivote
        $comisiones = \DB::table('comisiones')->whereNotNull('materia_id')->get();
        foreach ($comisiones as $comision) {
            \DB::table('comision_materia')->insert([
                'comision_id' => $comision->id,
                'materia_id' => $comision->materia_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Eliminar columna materia_id de comisiones (ya no se necesita)
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropForeign(['materia_id']);
            $table->dropColumn('materia_id');
        });
    }

    /**
     * Revertir: recrear columna materia_id y eliminar tabla pivote.
     */
    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->foreignId('materia_id')->nullable()->after('id')->constrained('materias')->nullOnDelete();
        });

        // Restaurar datos (tomar la primera materia de cada comisión)
        $pivotData = \DB::table('comision_materia')
            ->select('comision_id', \DB::raw('MIN(materia_id) as materia_id'))
            ->groupBy('comision_id')
            ->get();

        foreach ($pivotData as $row) {
            \DB::table('comisiones')
                ->where('id', $row->comision_id)
                ->update(['materia_id' => $row->materia_id]);
        }

        Schema::dropIfExists('comision_materia');
    }
};
