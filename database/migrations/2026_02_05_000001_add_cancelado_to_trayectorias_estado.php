<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Añadir estado 'cancelado' a la tabla trayectorias.
     */
    public function up(): void
    {
        // Modificar el enum para incluir 'cancelado'
        DB::statement("ALTER TABLE trayectorias MODIFY COLUMN estado ENUM('activo', 'pausado', 'libre', 'baja', 'reincorporado', 'aprobado', 'desaprobado', 'cancelado') DEFAULT 'activo'");
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Revertir al enum original
        DB::statement("ALTER TABLE trayectorias MODIFY COLUMN estado ENUM('activo', 'pausado', 'libre', 'baja', 'reincorporado', 'aprobado', 'desaprobado') DEFAULT 'activo'");
    }
};
