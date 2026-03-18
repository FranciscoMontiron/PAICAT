<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_variables', function (Blueprint $table) {
            $table->id();
            $table->string('grupo', 50)->index();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->string('tipo', 20)->default('texto'); // numero, texto, json, booleano
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('origen', 50)->default('manual'); // manual, config, sysacad, alumnos_utn
            $table->string('tabla_sincronizacion')->nullable();
            $table->boolean('editable')->default(true);
            $table->boolean('requerida')->default(false);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('configuracion_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuracion_variable_id')
                ->constrained('configuracion_variables')
                ->cascadeOnDelete();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('motivo')->nullable();
            $table->string('tipo_cambio', 30)->default('manual'); // manual, sincronizacion, seed
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_historial');
        Schema::dropIfExists('configuracion_variables');
    }
};
