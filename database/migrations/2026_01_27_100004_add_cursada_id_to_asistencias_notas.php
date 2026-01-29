<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar cursada_id a asistencias y notas para relacionarlas
     * con la cursada específica (permite histórico por cursada).
     */
    public function up(): void
    {
        // Agregar cursada_id a asistencias
        if (Schema::hasTable('asistencias') && !Schema::hasColumn('asistencias', 'cursada_id')) {
            Schema::table('asistencias', function (Blueprint $table) {
                $table->foreignId('cursada_id')->nullable()->after('id')->constrained('cursadas')->nullOnDelete();
                $table->index('cursada_id');
            });
        }

        // Agregar cursada_id a notas
        if (Schema::hasTable('notas') && !Schema::hasColumn('notas', 'cursada_id')) {
            Schema::table('notas', function (Blueprint $table) {
                $table->foreignId('cursada_id')->nullable()->after('id')->constrained('cursadas')->nullOnDelete();
                $table->index('cursada_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('asistencias', 'cursada_id')) {
            Schema::table('asistencias', function (Blueprint $table) {
                $table->dropForeign(['cursada_id']);
                $table->dropColumn('cursada_id');
            });
        }

        if (Schema::hasColumn('notas', 'cursada_id')) {
            Schema::table('notas', function (Blueprint $table) {
                $table->dropForeign(['cursada_id']);
                $table->dropColumn('cursada_id');
            });
        }
    }
};
