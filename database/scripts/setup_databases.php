<?php

/**
 * Script para configurar las 3 bases de datos del sistema PAICAT
 * 
 * Ejecutar con: docker exec paicat_php php database/scripts/setup_databases.php
 * 
 * Las 3 bases de datos son:
 * 1. paicat - Base de datos principal de la aplicación
 * 2. sysacad - Datos maestros (países, provincias, escuelas, etc.) desde Excel
 * 3. alumnos_utn - Datos de alumnos desde backup SQL
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
echo "║         PAICAT - Configuración de Bases de Datos             ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// =============================================================================
// 1. BASE DE DATOS PAICAT (Principal)
// =============================================================================
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│  1. PAICAT - Base de datos principal                        │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

try {
    $tables = DB::connection('paicat')->select("SHOW TABLES");
    echo "  ✓ Conexión exitosa\n";
    echo "  ✓ Tablas: " . count($tables) . "\n";
    
    // Verificar datos importantes
    $users = DB::connection('paicat')->table('users')->count();
    $roles = DB::connection('paicat')->table('roles')->count();
    echo "  ✓ Usuarios: {$users}\n";
    echo "  ✓ Roles: {$roles}\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 2. BASE DE DATOS SYSACAD (Datos maestros)
// =============================================================================
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│  2. SYSACAD - Datos maestros (desde Excel)                  │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

try {
    $tables = DB::connection('sysacad')->select("SHOW TABLES");
    echo "  ✓ Conexión exitosa\n";
    echo "  ✓ Tablas: " . count($tables) . "\n";
    
    // Verificar datos maestros
    $paises = DB::connection('sysacad')->table('sysacad_paises')->count();
    $provincias = DB::connection('sysacad')->table('sysacad_provincias')->count();
    $localidades = DB::connection('sysacad')->table('sysacad_localidades')->count();
    $escuelas = DB::connection('sysacad')->table('sysacad_escuelas')->count();
    
    echo "  ✓ Países: {$paises}\n";
    echo "  ✓ Provincias: {$provincias}\n";
    echo "  ✓ Localidades: {$localidades}\n";
    echo "  ✓ Escuelas: {$escuelas}\n";
    
    if ($paises == 0) {
        echo "\n  ⚠ Datos vacíos. Ejecutar seeder:\n";
        echo "    php artisan db:seed --class=\"Database\\Seeders\\Sysacad\\SysacadDataSeeder\"\n";
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 3. BASE DE DATOS ALUMNOS_UTN (Datos de alumnos)
// =============================================================================
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│  3. ALUMNOS_UTN - Datos de alumnos (desde backup SQL)       │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

try {
    $tables = DB::connection('alumnos_utn')->select("SHOW TABLES");
    echo "  ✓ Conexión exitosa\n";
    echo "  ✓ Tablas: " . count($tables) . "\n";
    
    // Verificar datos de alumnos
    $persons = DB::connection('alumnos_utn')->table('persons')->count();
    $academico = DB::connection('alumnos_utn')->table('academico_datos')->count();
    $secundaria = DB::connection('alumnos_utn')->table('secundaria_datos')->count();
    
    echo "  ✓ Alumnos (persons): {$persons}\n";
    echo "  ✓ Datos académicos: {$academico}\n";
    echo "  ✓ Datos secundaria: {$secundaria}\n";
    
    if ($persons == 0) {
        echo "\n  ⚠ Datos vacíos. Importar backup manualmente:\n";
        echo "    cat backup_27-10-2025.sql | docker exec -i paicat_mariadb mariadb -u root -proot alumnos_utn\n";
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                  ✅ Verificación completada                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
