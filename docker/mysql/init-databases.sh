#!/bin/bash
# Script para inicializar las 3 bases de datos del sistema PAICAT
# Ejecutar desde el contenedor de MariaDB o directamente con docker compose exec

set -e

echo "==============================================="
echo "Inicializando bases de datos del sistema PAICAT"
echo "==============================================="
echo ""

# Crear base de datos PAICAT (principal)
echo "1. Creando base de datos 'paicat' (principal)..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS paicat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON paicat.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
EOSQL
echo "✓ Base de datos 'paicat' creada exitosamente"
echo ""

# Crear base de datos alumnos_utn (solo lectura desde la app)
echo "2. Creando base de datos 'alumnos_utn' (sistema anterior)..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS alumnos_utn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON alumnos_utn.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
EOSQL
echo "✓ Base de datos 'alumnos_utn' creada exitosamente"
echo ""

# Crear base de datos sysacad (solo lectura desde la app)
echo "3. Creando base de datos 'sysacad' (datos maestros)..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS sysacad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON sysacad.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
EOSQL
echo "✓ Base de datos 'sysacad' creada exitosamente"
echo ""

echo "==============================================="
echo "✓ Todas las bases de datos han sido creadas"
echo "==============================================="
echo ""
echo "Bases de datos disponibles:"
echo "  - paicat: Base de datos principal (Lectura/Escritura)"
echo "  - alumnos_utn: Datos del sistema anterior (Solo Lectura desde app)"
echo "  - sysacad: Datos maestros de Sysacad (Solo Lectura desde app)"
echo ""
echo "Para restaurar el backup en alumnos_utn ejecuta:"
echo "  docker compose exec mariadb mysql -u root -p alumnos_utn < backup_27-10-2025.sql"
echo ""
