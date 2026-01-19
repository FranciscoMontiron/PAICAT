<?php
/**
 * Script para esperar y verificar la conexión a MariaDB
 * Evita problemas de sintaxis con código PHP inline en shell scripts
 */

$host = getenv('DB_HOST') ?: 'mariadb';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'laravel';
$pass = getenv('DB_PASSWORD') ?: 'secret';
$database = getenv('DB_DATABASE') ?: 'paicat';

$maxRetries = 30;
$retryInterval = 2;

echo "Esperando conexión a MariaDB ($host:$port)...\n";

for ($i = 0; $i < $maxRetries; $i++) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        echo "MariaDB está listo!\n";
        
        // Crear base de datos si no existe
        echo "Verificando base de datos...\n";
        $pdo->exec(
            "CREATE DATABASE IF NOT EXISTS `$database` 
             CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        echo "Base de datos verificada/creada\n";
        
        exit(0);
    } catch (PDOException $e) {
        echo "  MariaDB no está listo - esperando... (intento " . ($i + 1) . "/$maxRetries)\n";
        sleep($retryInterval);
    }
}

echo "ERROR: No se pudo conectar a MariaDB después de $maxRetries intentos\n";
exit(1);
