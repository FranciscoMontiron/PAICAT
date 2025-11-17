#!/bin/sh
set -e

echo " Iniciando configuración de PAICAT..."

cd /var/www/html

# Copiar .env.example a .env si no existe
if [ ! -f ".env" ]; then
    echo " Copiando .env.example a .env..."
    cp .env.example .env
fi

# Esperar a que MariaDB esté listo
echo " Esperando a que MariaDB esté disponible..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
  echo "   MariaDB no está listo - esperando..."
  sleep 2
done

echo " MariaDB está listo!"

# Verificar si la base de datos existe, si no, crearla
echo " Verificando base de datos..."
php -r "
\$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');
\$pdo->exec('CREATE DATABASE IF NOT EXISTS ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
"

# Instalar dependencias de Composer si no existen
if [ ! -d "vendor" ]; then
    echo " Instalando dependencias de Composer..."
    composer install --no-interaction --optimize-autoloader
fi

# Instalar dependencias de NPM si no existen
if [ ! -d "node_modules" ]; then
    echo " Instalando dependencias de NPM..."
    npm install --silent
fi

# Generar clave de aplicación si no existe
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo " Generando clave de aplicación..."
    php artisan key:generate --force
fi

# Limpiar caché antes de migrar
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
echo "🗄️  Ejecutando migraciones..."
php artisan migrate --force 2>&1 || echo "⚠️  Migraciones ya ejecutadas o error (continuando...)"

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force 2>&1 || echo "⚠️  Seeders ya ejecutados o error (continuando...)"

# Optimizar aplicación
echo " Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlaces simbólicos de storage
echo " Creando enlaces simbólicos..."
php artisan storage:link 2>/dev/null || true

# Arreglar permisos
echo " Configurando permisos..."
chown -R www-data:www-data /var/www/html
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo ""
echo " ¡PAICAT configurado exitosamente!"
echo ""
echo " Credenciales de acceso:"
echo "   Email: admin@paicat.utn.edu.ar"
echo "   Password: admin123"
echo ""
echo " Accede a la aplicación en: http://localhost"
echo ""

# Ejecutar el comando original de PHP-FPM
exec "$@"
