<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_final_materias', function (Blueprint $table) {
            // Snapshot de la nota mínima de aprobación vigente al momento
            // en que el docente cargó la nota final. Garantiza que cambios
            // posteriores en la configuración no alteren el estado aprobado/desaprobado.
            $table->decimal('nota_aprobacion_snapshot', 4, 2)->nullable()->after('nota_final');
        });
    }

    public function down(): void
    {
        Schema::table('nota_final_materias', function (Blueprint $table) {
            $table->dropColumn('nota_aprobacion_snapshot');
        });
    }
};
