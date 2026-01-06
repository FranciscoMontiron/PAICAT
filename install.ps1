# Script de instalacion rapida para PAICAT en Windows
# Ejecutar con: .\install.ps1

Write-Host " PAICAT - Instalacion Rapida" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Verificar Docker
Write-Host "Verificando Docker..." -ForegroundColor Yellow
try {
    docker info | Out-Null
    Write-Host " Docker esta disponible" -ForegroundColor Green
} catch {
    Write-Host " Error: Docker no esta ejecutandose" -ForegroundColor Red
    Write-Host "   Inicia Docker Desktop e intenta de nuevo" -ForegroundColor Yellow
    exit 1
}

# Verificar Docker Compose
try {
    docker-compose --version | Out-Null
    Write-Host " Docker Compose esta disponible" -ForegroundColor Green
} catch {
    Write-Host " Error: Docker Compose no esta instalado" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Verificar y descomprimir paicat-data.zip si existe
if (Test-Path ".\paicat-data.zip") {
    Write-Host " Descomprimiendo paicat-data.zip..." -ForegroundColor Yellow
    
    # Crear carpeta temporal para extraccion
    $tempDir = ".\paicat-data-temp"
    if (Test-Path $tempDir) {
        Remove-Item $tempDir -Recurse -Force
    }
    
    Expand-Archive -Path ".\paicat-data.zip" -DestinationPath $tempDir -Force
    
    # Mover archivos a sus ubicaciones correctas
    if (Test-Path "$tempDir\datos_paicat_completo.sql") {
        Move-Item "$tempDir\datos_paicat_completo.sql" ".\datos_paicat_completo.sql" -Force
        Write-Host "   datos_paicat_completo.sql extraido" -ForegroundColor Green
    }
    if (Test-Path "$tempDir\backup_27-10-2025.sql") {
        Move-Item "$tempDir\backup_27-10-2025.sql" ".\backup_27-10-2025.sql" -Force
        Write-Host "   backup_27-10-2025.sql extraido" -ForegroundColor Green
    }
    if (Test-Path "$tempDir\Datos Sysacad.xlsx") {
        if (-not (Test-Path ".\database\data")) {
            New-Item -ItemType Directory -Path ".\database\data" -Force | Out-Null
        }
        Move-Item "$tempDir\Datos Sysacad.xlsx" ".\database\data\Datos Sysacad.xlsx" -Force
        Write-Host "   Datos Sysacad.xlsx extraido" -ForegroundColor Green
    }
    
    # Limpiar carpeta temporal
    Remove-Item $tempDir -Recurse -Force -ErrorAction SilentlyContinue
    
    Write-Host " Archivos de datos extraidos correctamente" -ForegroundColor Green
} else {
    # Verificar si los archivos ya existen individualmente
    $hasDataFiles = (Test-Path ".\datos_paicat_completo.sql") -or (Test-Path ".\backup_27-10-2025.sql")
    if (-not $hasDataFiles) {
        Write-Host " Archivo paicat-data.zip no encontrado" -ForegroundColor Yellow
        Write-Host "   Descargalo de Drive y ponelo en la raiz del proyecto" -ForegroundColor Yellow
        Write-Host "   O continua sin datos (se crearan las BDs vacias)" -ForegroundColor Gray
    }
}

Write-Host ""

# Copiar .env.example a .env si no existe
if (-not (Test-Path .env)) {
    Write-Host " Copiando .env.example a .env..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    Write-Host " Archivo .env creado" -ForegroundColor Green
} else {
    Write-Host "  El archivo .env ya existe, no se sobrescribira" -ForegroundColor Yellow
}

Write-Host ""
Write-Host " Levantando contenedores Docker..." -ForegroundColor Cyan
docker-compose up -d

Write-Host ""
Write-Host " Iniciando monitor de progreso..." -ForegroundColor Cyan
Write-Host "   (Presiona Ctrl+C si deseas saltar el monitoreo)" -ForegroundColor Gray
Write-Host ""
Start-Sleep -Seconds 2

# Ejecutar monitor de startup
if (Test-Path ".\monitor-startup.ps1") {
    & ".\monitor-startup.ps1"
} else {
    Write-Host " Esperando a que los servicios esten listos..." -ForegroundColor Yellow
    Start-Sleep -Seconds 30
    Write-Host ""
    Write-Host " Verificando estado de los contenedores..." -ForegroundColor Yellow
    docker-compose ps
}

# Importar bases de datos
Write-Host ""
Write-Host " Configurando bases de datos..." -ForegroundColor Cyan

# Esperar a que MariaDB este listo
Write-Host "   Esperando a que MariaDB este listo..." -ForegroundColor Yellow
$maxRetries = 30
$retry = 0
do {
    $healthy = docker inspect --format="{{.State.Health.Status}}" paicat_mariadb 2>$null
    if ($healthy -ne "healthy") {
        Start-Sleep -Seconds 2
        $retry++
    }
} while ($healthy -ne "healthy" -and $retry -lt $maxRetries)

if ($healthy -eq "healthy") {
    # Dar permisos al usuario laravel sobre todas las BDs
    Write-Host "   Configurando permisos de base de datos..." -ForegroundColor Yellow
    docker exec paicat_mariadb mariadb -u root -proot -e "GRANT ALL PRIVILEGES ON paicat.* TO 'laravel'@'%'; GRANT ALL PRIVILEGES ON sysacad.* TO 'laravel'@'%'; GRANT ALL PRIVILEGES ON alumnos_utn.* TO 'laravel'@'%'; FLUSH PRIVILEGES;" 2>$null
    
    # 1. Importar BD paicat (estructura principal)
    if (Test-Path ".\datos_paicat_completo.sql") {
        Write-Host "   [1/3] Importando BD paicat..." -ForegroundColor Yellow
        # Usar docker cp directamente para evitar problemas de encoding con PowerShell
        docker cp ".\datos_paicat_completo.sql" paicat_mariadb:/tmp/datos.sql
        docker exec paicat_mariadb sh -c "mariadb -uroot -proot < /tmp/datos.sql" 2>$null
        Write-Host "   BD paicat importada" -ForegroundColor Green
    } else {
        Write-Host "   [1/3] Archivo datos_paicat_completo.sql no encontrado" -ForegroundColor Yellow
    }
    
    # 2. Importar BD alumnos_utn (datos de alumnos)
    if (Test-Path ".\backup_27-10-2025.sql") {
        Write-Host "   [2/3] Importando BD alumnos_utn..." -ForegroundColor Yellow
        docker exec paicat_mariadb mariadb -u root -proot -e "DROP DATABASE IF EXISTS alumnos_utn; CREATE DATABASE alumnos_utn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
        docker exec paicat_mariadb mariadb -u root -proot -e "GRANT ALL PRIVILEGES ON alumnos_utn.* TO 'laravel'@'%'; FLUSH PRIVILEGES;" 2>$null
        # Usar docker cp directamente para evitar problemas de encoding con PowerShell
        docker cp ".\backup_27-10-2025.sql" paicat_mariadb:/tmp/alumnos.sql
        docker exec paicat_mariadb sh -c "mariadb -uroot -proot alumnos_utn < /tmp/alumnos.sql" 2>$null
        Write-Host "   BD alumnos_utn importada" -ForegroundColor Green
    } else {
        Write-Host "   [2/3] Archivo backup_27-10-2025.sql no encontrado" -ForegroundColor Yellow
    }
    
    # 3. Configurar BD sysacad (datos maestros desde Excel via seeder)
    Write-Host "   [3/3] Configurando BD sysacad (migraciones + seeder)..." -ForegroundColor Yellow
    docker exec paicat_mariadb mariadb -u root -proot -e "DROP DATABASE IF EXISTS sysacad; CREATE DATABASE sysacad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
    docker exec paicat_php php artisan migrate --database=sysacad --force 2>$null
    docker exec paicat_php php artisan db:seed --class="Database\Seeders\Sysacad\SysacadDataSeeder" --force 2>$null
    Write-Host "   BD sysacad configurada" -ForegroundColor Green
    
    Write-Host " Bases de datos configuradas correctamente" -ForegroundColor Green
} else {
    Write-Host " MariaDB no esta listo, configura las bases de datos manualmente" -ForegroundColor Yellow
}

Write-Host ""
Write-Host " Instalacion completada!" -ForegroundColor Green
Write-Host ""
Write-Host " Bases de datos configuradas:" -ForegroundColor Cyan
Write-Host "   - paicat: BD principal de la aplicacion" -ForegroundColor White
Write-Host "   - sysacad: Datos maestros (paises, provincias, escuelas)" -ForegroundColor White
Write-Host "   - alumnos_utn: Datos de alumnos" -ForegroundColor White
Write-Host ""
Write-Host " URLs:" -ForegroundColor Cyan
Write-Host "   Aplicacion: http://localhost" -ForegroundColor White
Write-Host "   Vite HMR: http://localhost:5173" -ForegroundColor White
Write-Host "   PHPMyAdmin: http://localhost:8081" -ForegroundColor White
Write-Host "   Mailhog: http://localhost:8025" -ForegroundColor White
Write-Host ""
Write-Host " Usuario admin:" -ForegroundColor Cyan
Write-Host "   Email: admin@paicat.utn.edu.ar" -ForegroundColor White
Write-Host "   Password: admin123" -ForegroundColor White
Write-Host ""
Write-Host " Comandos utiles:" -ForegroundColor Cyan
Write-Host "   docker-compose logs -f       Ver logs" -ForegroundColor White
Write-Host "   docker-compose down          Detener contenedores" -ForegroundColor White
Write-Host "   docker-compose restart       Reiniciar contenedores" -ForegroundColor White
Write-Host ""
Write-Host " Para verificar las BDs: docker exec paicat_php php database/scripts/setup_databases.php" -ForegroundColor Yellow
Write-Host ""
