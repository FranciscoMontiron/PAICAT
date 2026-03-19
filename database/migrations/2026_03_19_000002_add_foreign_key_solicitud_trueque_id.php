<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_cambio', function (Blueprint $table) {
            $table->foreign('solicitud_trueque_id')
                ->references('id')
                ->on('solicitudes_cambio')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cambio', function (Blueprint $table) {
            $table->dropForeign(['solicitud_trueque_id']);
        });
    }
};
