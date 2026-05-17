<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('paicat')->table('comisiones', function (Blueprint $table) {
            $table->boolean('archivada')->default(false)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::connection('paicat')->table('comisiones', function (Blueprint $table) {
            $table->dropColumn('archivada');
        });
    }
};
