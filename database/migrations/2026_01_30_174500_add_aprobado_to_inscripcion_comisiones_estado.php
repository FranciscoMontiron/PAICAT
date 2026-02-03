<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        // Modificar el enum para incluir 'aprobado'
        DB::statement("ALTER TABLE inscripcion_comisiones MODIFY COLUMN estado ENUM('inscripto', 'confirmado', 'cancelado', 'trasladado', 'aprobado') DEFAULT 'inscripto'");
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Revertir al enum original
        DB::statement("ALTER TABLE inscripcion_comisiones MODIFY COLUMN estado ENUM('inscripto', 'confirmado', 'cancelado', 'trasladado') DEFAULT 'inscripto'");
    }
};
