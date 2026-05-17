<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursadas', function (Blueprint $table) {
            // Los parámetros de configuración vigentes al momento de la finalización.
            // NULL = cursada todavía activa o creada antes de esta migración.
            $table->decimal('nota_aprobacion_snapshot', 4, 2)->nullable()->after('nota_final');
            $table->decimal('nota_regular_snapshot', 4, 2)->nullable()->after('nota_aprobacion_snapshot');
            $table->decimal('asistencia_minima_snapshot', 5, 2)->nullable()->after('nota_regular_snapshot');
        });

        // Las cursadas ya finalizadas que NO tienen snapshot se dejan con NULL a propósito.
        // El modelo las tratará con los valores originales del sistema (6 / 4 / 75),
        // que son los únicos valores que existían antes de que la configuración fuera
        // expuesta al usuario.
    }

    public function down(): void
    {
        Schema::table('cursadas', function (Blueprint $table) {
            $table->dropColumn(['nota_aprobacion_snapshot', 'nota_regular_snapshot', 'asistencia_minima_snapshot']);
        });
    }
};
