<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->after('name');
            $table->string('dni', 20)->unique()->nullable()->after('apellido');
            $table->string('telefono', 20)->nullable()->after('email');
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo')->after('telefono');
            $table->softDeletes();

            $table->index('dni');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['dni']);
            $table->dropIndex(['estado']);
            $table->dropSoftDeletes();
            $table->dropColumn(['apellido', 'dni', 'telefono', 'estado']);
        });
    }
};
