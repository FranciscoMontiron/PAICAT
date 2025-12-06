<?php

/**
 * Script para configurar migraciones de forma segura
 * 
 * Ejecutar con: docker exec paicat_php php database/scripts/setup_migrations.php
 * 
 * Este script:
 * 1. Verifica si las tablas ya existen
 * 2. Si existen, marca las migraciones como ejecutadas sin correrlas
 * 3. Si no existen, ejecuta las migraciones normalmente
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║         PAICAT - Configuración de Migraciones                ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Lista de migraciones y sus tablas correspondientes
$migrations = [
    '0001_01_01_000000_create_users_table' => ['users', 'password_reset_tokens', 'sessions'],
    '0001_01_01_000001_create_cache_table' => ['cache', 'cache_locks'],
    '0001_01_01_000002_create_jobs_table' => ['jobs', 'job_batches', 'failed_jobs'],
    '2025_01_01_000001_create_sysacad_paises_table' => ['sysacad_paises'],
    '2025_01_01_000002_create_sysacad_provincias_table' => ['sysacad_provincias'],
    '2025_01_01_000003_create_sysacad_partidos_table' => ['sysacad_partidos'],
    '2025_01_01_000004_create_sysacad_localidades_table' => ['sysacad_localidades'],
    '2025_01_01_000005_create_sysacad_escuelas_table' => ['sysacad_escuelas'],
    '2025_01_01_000006_create_sysacad_catalogos_table' => ['sysacad_catalogos'],
    '2025_01_02_000001_create_inscripciones_table' => ['inscripciones'],
    '2025_01_02_000002_create_inscripcion_comisiones_table' => ['inscripcion_comisiones'],
    '2025_01_02_000003_create_asistencias_table' => ['asistencias'],
    '2025_01_02_000004_create_evaluaciones_table' => ['evaluaciones'],
    '2025_01_02_000005_create_notas_table' => ['notas'],
];

// Obtener el último batch
$lastBatch = DB::table('migrations')->max('batch');
$lastBatch = $lastBatch ? $lastBatch : 0;
$newBatch = $lastBatch + 1;

$migrationsMarked = 0;
$migrationsToRun = [];

echo "Verificando estado de migraciones...\n\n";

foreach ($migrations as $migrationName => $tables) {
    // Verificar si la migración ya está registrada
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    
    if ($exists) {
        echo "  ✓ {$migrationName} - Ya registrada\n";
        continue;
    }
    
    // Verificar si las tablas existen
    $allTablesExist = true;
    foreach ($tables as $table) {
        if (!Schema::hasTable($table)) {
            $allTablesExist = false;
            break;
        }
    }
    
    if ($allTablesExist) {
        // Las tablas existen pero la migración no está registrada - marcarla como ejecutada
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $newBatch
        ]);
        echo "  ⚡ {$migrationName} - Tablas existen, marcada como ejecutada\n";
        $migrationsMarked++;
    } else {
        // La migración necesita ejecutarse
        $migrationsToRun[] = $migrationName;
        echo "  ⏳ {$migrationName} - Pendiente de ejecutar\n";
    }
}

echo "\n";

if ($migrationsMarked > 0) {
    echo "Se marcaron {$migrationsMarked} migraciones como ejecutadas.\n";
}

if (count($migrationsToRun) > 0) {
    echo "\nEjecutando migraciones pendientes...\n\n";
    
    // Ejecutar migraciones pendientes
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    
    if ($exitCode === 0) {
        echo "\n✅ Migraciones ejecutadas correctamente.\n";
    } else {
        echo "\n❌ Error al ejecutar migraciones.\n";
        exit(1);
    }
} else {
    echo "✅ Todas las migraciones están al día.\n";
}

echo "\n";
echo "Estado final de migraciones:\n";
echo "─────────────────────────────\n";

$allMigrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
foreach ($allMigrations as $m) {
    echo "  [Batch {$m->batch}] {$m->migration}\n";
}

echo "\n✅ Configuración completada.\n\n";
