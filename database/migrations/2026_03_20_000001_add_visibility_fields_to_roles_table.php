<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('solo_contenido_asignado')->default(true)->after('descripcion');
            $table->boolean('visibilidad_general')->default(false)->after('solo_contenido_asignado');
        });

        // Admin y Coordinador tienen visibilidad general
        DB::table('roles')->whereIn('slug', ['admin', 'coordinador'])->update([
            'solo_contenido_asignado' => false,
            'visibilidad_general' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['solo_contenido_asignado', 'visibilidad_general']);
        });
    }
};
