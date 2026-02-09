<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trayectorias', function (Blueprint $table) {
            $table->dateTime('fecha_inicio')->change();
            $table->dateTime('fecha_fin')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trayectorias', function (Blueprint $table) {
            $table->date('fecha_inicio')->change();
            $table->date('fecha_fin')->nullable()->change();
        });
    }
};
