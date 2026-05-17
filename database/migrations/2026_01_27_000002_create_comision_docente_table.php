<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Tabla pivot para gestionar múltiples docentes por comisión
     * con historial de asignaciones (activo/inactivo).
     */
    public function up(): void
    {
        Schema::create('comision_docente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comision_id')->constrained('comisiones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->timestamp('fecha_baja')->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            // Índices
            $table->index(['comision_id', 'activo']);
            $table->index(['user_id', 'activo']);

            // Un docente solo puede estar activo una vez por comisión
            $table->unique(['comision_id', 'user_id', 'activo'], 'comision_docente_activo_unique');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('comision_docente');
    }
};
