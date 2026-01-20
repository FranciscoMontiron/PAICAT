#!/bin/bash
set -e

# Crear directorios de storage si no existen (importante para volúmenes Docker)
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Configurar permisos para www-data (usuario de Apache)
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Permisos de storage configurados correctamente"

# Ejecutar el comando original (apache2-foreground)
exec "$@"
