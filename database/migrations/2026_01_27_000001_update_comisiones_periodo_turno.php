<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Cambios:
     * - periodo: Verano/Invierno/Anual -> Intensivo/Extensivo
     * - turno: ENUM -> VARCHAR(50) para valores dinámicos de sysacad_turnos
     * - Remover fecha_inicio y fecha_fin
     */
    public function up(): void
    {
        // Paso 1: Migrar valores existentes de periodo
        // Verano/Invierno/Anual -> Extensivo (valor por defecto)
        DB::table('comisiones')->update(['periodo' => 'Extensivo']);

        // Paso 2: Modificar columna periodo a nuevos valores
        Schema::table('comisiones', function (Blueprint $table) {
            // En MySQL/MariaDB necesitamos recrear el ENUM
            $table->dropColumn('periodo');
        });

        Schema::table('comisiones', function (Blueprint $table) {
            $table->enum('periodo', ['Intensivo', 'Extensivo'])
                ->default('Extensivo')
                ->after('anio');
        });

        // Paso 3: Guardar valores actuales de turno antes de cambiar tipo
        $comisiones = DB::table('comisiones')->select('id', 'turno')->get();

        // Paso 4: Modificar columna turno de ENUM a VARCHAR
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropColumn('turno');
        });

        Schema::table('comisiones', function (Blueprint $table) {
            $table->string('turno', 50)->nullable()->after('periodo');
        });

        // Paso 5: Restaurar valores de turno
        foreach ($comisiones as $comision) {
            DB::table('comisiones')
                ->where('id', $comision->id)
                ->update(['turno' => $comision->turno]);
        }

        // Paso 6: Remover columnas de fecha
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Restaurar fecha_inicio y fecha_fin
        Schema::table('comisiones', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('docente_id');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
        });

        // Guardar valores actuales de turno
        $comisiones = DB::table('comisiones')->select('id', 'turno')->get();

        // Restaurar turno como ENUM
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropColumn('turno');
        });

        Schema::table('comisiones', function (Blueprint $table) {
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche'])
                ->default('Mañana')
                ->after('periodo');
        });

        // Restaurar valores de turno mapeando a valores válidos
        foreach ($comisiones as $comision) {
            $turnoValido = in_array($comision->turno, ['Mañana', 'Tarde', 'Noche'])
                ? $comision->turno
                : 'Mañana';
            DB::table('comisiones')
                ->where('id', $comision->id)
                ->update(['turno' => $turnoValido]);
        }

        // Restaurar periodo como ENUM original
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropColumn('periodo');
        });

        Schema::table('comisiones', function (Blueprint $table) {
            $table->enum('periodo', ['Verano', 'Invierno', 'Anual'])
                ->default('Verano')
                ->after('anio');
        });

        // Mapear Extensivo/Intensivo a Verano
        DB::table('comisiones')->update(['periodo' => 'Verano']);
    }
};
