# Instalación Manual de PAICAT

Esta guía es para usuarios que tienen problemas con el script `install.ps1`, especialmente en Windows 10 con versiones antiguas de Docker Desktop.

## 1. Levantar los contenedores

```powershell
# Primero, asegúrate de que no haya contenedores del setup normal corriendo
docker-compose down 2>$null

# Levantar con el compose manual
docker-compose -f docker-compose-manual.yml up -d --build
```

## 2. Esperar a que MariaDB esté listo (~30 segundos)

```powershell
# Verificar que MariaDB esté healthy
docker-compose -f docker-compose-manual.yml ps
```

Espera hasta que `paicat_mariadb_manual` muestre estado `healthy`.

## 3. Entrar al contenedor PHP

```powershell
docker exec -it paicat_php_manual bash
```

## 4. Configurar Laravel (dentro del contenedor)

Ejecuta estos comandos uno por uno dentro del contenedor:

```bash
# Copiar .env si no existe
cp .env.example .env

# Instalar dependencias de Composer
composer install --no-interaction --optimize-autoloader

# Generar clave de aplicación
php artisan key:generate

# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (opcional, los datos ya vienen en el SQL)
# php artisan db:seed --force

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace de storage
php artisan storage:link

# Arreglar permisos
chown -R www-data:www-data /var/www/html
chmod -R 775 storage bootstrap/cache

# Salir del contenedor
exit
```

## 5. Acceder a la aplicación

- **Aplicación**: http://localhost
- **PHPMyAdmin**: http://localhost:8081
- **Mailhog**: http://localhost:8025

### Credenciales por defecto

- **Email**: admin@paicat.utn.edu.ar
- **Password**: admin123

---

## Comandos útiles

```powershell
# Ver logs
docker-compose -f docker-compose-manual.yml logs -f

# Ver logs solo de PHP
docker-compose -f docker-compose-manual.yml logs -f php

# Reiniciar contenedores
docker-compose -f docker-compose-manual.yml restart

# Parar todo
docker-compose -f docker-compose-manual.yml down

# Parar y eliminar volúmenes (reset completo)
docker-compose -f docker-compose-manual.yml down -v
```

## Solución de problemas

### Error de conexión a la base de datos

Si obtienes error de conexión, asegúrate de que MariaDB esté healthy:

```powershell
docker-compose -f docker-compose-manual.yml ps
```

Puedes probar la conexión manualmente:

```powershell
docker exec -it paicat_php_manual mysql -h mariadb -u laravel -psecret paicat -e "SELECT 1"
```

### Los assets no cargan (Vite)

El contenedor de Vite puede tardar un poco en instalar dependencias. Verifica:

```powershell
docker-compose -f docker-compose-manual.yml logs vite
```
