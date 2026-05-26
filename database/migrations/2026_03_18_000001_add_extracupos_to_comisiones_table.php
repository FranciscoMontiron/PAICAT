<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            // Hacer cupo_maximo nullable (virtual = null = sin limite)
            $table->unsignedInteger('cupo_maximo')->nullable()->change();

            // Agregar campos de extracupos
            $table->boolean('extracupos_habilitados')->default(false)->after('cupo_actual');
            $table->unsignedInteger('extracupos')->default(0)->after('extracupos_habilitados');
        });

        // Migrar comisiones virtuales: cambiar cupo_maximo 9999 a NULL
        DB::table('comisiones')
            ->where('modalidad', 'Virtual')
            ->where('cupo_maximo', '>=', 9999)
            ->update(['cupo_maximo' => null]);
    }

    public function down(): void
    {
        // Restaurar virtuales a 9999
        DB::table('comisiones')
            ->where('modalidad', 'Virtual')
            ->whereNull('cupo_maximo')
            ->update(['cupo_maximo' => 9999]);

        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropColumn(['extracupos_habilitados', 'extracupos']);
            $table->unsignedInteger('cupo_maximo')->nullable(false)->default(0)->change();
        });
    }
};
