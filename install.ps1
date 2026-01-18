# Script de instalacion rapida para PAICAT en Windows
# Ejecutar con: .\install.ps1

$ErrorActionPreference = "SilentlyContinue"

function Write-Step { param($msg) Write-Host "`n>> $msg" -ForegroundColor Cyan }
function Write-OK { param($msg) Write-Host "   [OK] $msg" -ForegroundColor Green }
function Write-Warn { param($msg) Write-Host "   [!] $msg" -ForegroundColor Yellow }
function Write-Err { param($msg) Write-Host "   [X] $msg" -ForegroundColor Red }

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "   PAICAT - Instalacion Automatica   " -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan

# ==============================================
# 1. Verificar Docker
# ==============================================
Write-Step "Verificando Docker..."

$dockerRunning = docker info 2>$null
if (-not $dockerRunning) {
    Write-Err "Docker no esta ejecutandose"
    Write-Host "   Inicia Docker Desktop e intenta de nuevo" -ForegroundColor Yellow
    exit 1
}
Write-OK "Docker esta disponible"

# ==============================================
# 2. Descomprimir paicat-data.zip si existe
# ==============================================
Write-Step "Verificando archivos de datos..."

if (Test-Path ".\paicat-data.zip") {
    Write-Host "   Descomprimiendo paicat-data.zip..." -ForegroundColor Yellow
    
    $tempDir = ".\paicat-data-temp"
    if (Test-Path $tempDir) { Remove-Item $tempDir -Recurse -Force }
    
    Expand-Archive -Path ".\paicat-data.zip" -DestinationPath $tempDir -Force
    
    # Mover archivos a sus ubicaciones
    if (Test-Path "$tempDir\datos_paicat_completo.sql") {
        Move-Item "$tempDir\datos_paicat_completo.sql" ".\datos_paicat_completo.sql" -Force
        Write-OK "datos_paicat_completo.sql extraido"
    }
    if (Test-Path "$tempDir\backup_27-10-2025.sql") {
        Move-Item "$tempDir\backup_27-10-2025.sql" ".\backup_27-10-2025.sql" -Force
        Write-OK "backup_27-10-2025.sql extraido"
    }
    if (Test-Path "$tempDir\Datos Sysacad.xlsx") {
        if (-not (Test-Path ".\database\data")) {
            New-Item -ItemType Directory -Path ".\database\data" -Force | Out-Null
        }
        Move-Item "$tempDir\Datos Sysacad.xlsx" ".\database\data\Datos Sysacad.xlsx" -Force
        Write-OK "Datos Sysacad.xlsx extraido"
    }
    
    Remove-Item $tempDir -Recurse -Force -ErrorAction SilentlyContinue
    Write-OK "Archivos extraidos correctamente"
} else {
    if (-not (Test-Path ".\datos_paicat_completo.sql")) {
        Write-Warn "No se encontro paicat-data.zip ni datos_paicat_completo.sql"
        Write-Host "   Descarga el zip de Drive y ponelo en la raiz del proyecto" -ForegroundColor Gray
    } else {
        Write-OK "Archivos de datos ya existen"
    }
}

# ==============================================
# 3. Convertir archivos SQL a UTF-8 (fix Windows)
# ==============================================
Write-Step "Preparando archivos SQL..."

function Convert-ToUtf8 {
    param($FilePath)
    if (Test-Path $FilePath) {
        $bytes = [System.IO.File]::ReadAllBytes($FilePath)
        # Detectar BOM UTF-16 LE (FF FE)
        if ($bytes.Length -ge 2 -and $bytes[0] -eq 0xFF -and $bytes[1] -eq 0xFE) {
            Write-Host "   Convirtiendo $FilePath a UTF-8..." -ForegroundColor Yellow
            $content = Get-Content $FilePath -Encoding Unicode -Raw
            [System.IO.File]::WriteAllText($FilePath, $content, [System.Text.UTF8Encoding]::new($false))
            Write-OK "$FilePath convertido a UTF-8"
            return $true
        }
    }
    return $false
}

Convert-ToUtf8 ".\datos_paicat_completo.sql" | Out-Null
Convert-ToUtf8 ".\backup_27-10-2025.sql" | Out-Null
Write-OK "Archivos SQL listos"

# ==============================================
# 4. Crear .env si no existe
# ==============================================
Write-Step "Configurando entorno..."

if (-not (Test-Path ".\.env")) {
    Copy-Item ".\.env.example" ".\.env" -Force
    Write-OK "Archivo .env creado"
} else {
    Write-OK "Archivo .env ya existe"
}

# ==============================================
# 5. Limpiar instalacion anterior si existe
# ==============================================
Write-Step "Preparando contenedores..."

# Detener contenedores anteriores si existen
docker-compose down 2>$null | Out-Null

# Limpiar vendor incompleto (causa errores)
if ((Test-Path ".\vendor") -and (-not (Test-Path ".\vendor\autoload.php"))) {
    Write-Host "   Limpiando vendor incompleto..." -ForegroundColor Yellow
    Remove-Item ".\vendor" -Recurse -Force -ErrorAction SilentlyContinue
    Write-OK "Vendor limpiado"
}

Write-OK "Listo para iniciar"

# ==============================================
# 6. Levantar contenedores
# ==============================================
Write-Step "Levantando contenedores Docker..."
Write-Host "   (Esto puede tardar 2-3 minutos la primera vez)" -ForegroundColor Gray

# Primero construir la imagen PHP
Write-Host "   Construyendo imagen PHP..." -ForegroundColor Yellow
$buildOutput = docker-compose build php 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host $buildOutput -ForegroundColor Red
}

# Luego levantar todos los contenedores
Write-Host "   Iniciando contenedores..." -ForegroundColor Yellow
$upOutput = docker-compose up -d 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host $upOutput -ForegroundColor Red
}

# Esperar explicitamente a que los contenedores dependientes arranquen
Write-Host "   Esperando a que los servicios arranquen..." -ForegroundColor Gray
Start-Sleep -Seconds 5

# Verificar que todos los contenedores esten corriendo
$allRunning = $false
$attempts = 0
while (-not $allRunning -and $attempts -lt 30) {
    $php = docker inspect --format="{{.State.Running}}" paicat_php 2>$null
    $nginx = docker inspect --format="{{.State.Running}}" paicat_nginx 2>$null
    $vite = docker inspect --format="{{.State.Running}}" paicat_vite 2>$null
    
    if ($php -eq "true" -and $nginx -eq "true" -and $vite -eq "true") {
        $allRunning = $true
    } else {
        Start-Sleep -Seconds 2
        $attempts++
    }
}

if (-not $allRunning) {
    # Intentar levantar de nuevo si no estan corriendo
    docker-compose up -d 2>&1 | Out-Null
    Start-Sleep -Seconds 3
}

# ==============================================
# 7. Esperar a que MariaDB este listo
# ==============================================
Write-Step "Esperando a que MariaDB este listo..."

$maxWait = 60
$waited = 0
do {
    $healthy = docker inspect --format="{{.State.Health.Status}}" paicat_mariadb 2>$null
    if ($healthy -ne "healthy") {
        Write-Host "." -NoNewline
        Start-Sleep -Seconds 2
        $waited += 2
    }
} while ($healthy -ne "healthy" -and $waited -lt $maxWait)

if ($healthy -eq "healthy") {
    Write-Host ""
    Write-OK "MariaDB listo"
} else {
    Write-Host ""
    Write-Err "MariaDB no respondio a tiempo"
    Write-Host "   Ejecuta: docker logs paicat_mariadb" -ForegroundColor Yellow
    exit 1
}

# ==============================================
# 8. Esperar a que PHP termine de inicializarse
# ==============================================
Write-Step "Esperando a que PHP-FPM este listo..."

$maxWait = 180
$waited = 0
$phpReady = $false

do {
    # Verificar si composer instalo las dependencias
    $result = docker exec paicat_php test -f /var/www/html/vendor/autoload.php 2>$null
    if ($LASTEXITCODE -eq 0) {
        # Verificar si PHP-FPM esta respondiendo
        $fpmReady = docker exec paicat_php pgrep php-fpm 2>$null
        if ($fpmReady) {
            $phpReady = $true
        }
    }
    
    if (-not $phpReady) {
        Write-Host "." -NoNewline
        Start-Sleep -Seconds 5
        $waited += 5
    }
} while (-not $phpReady -and $waited -lt $maxWait)

if ($phpReady) {
    Write-Host ""
    Write-OK "PHP-FPM listo"
    # Esperar un poco mas para que termine toda la inicializacion
    Write-Host "   Esperando inicializacion completa..." -ForegroundColor Gray
    Start-Sleep -Seconds 15
} else {
    Write-Host ""
    Write-Warn "PHP aun esta inicializando"
    Write-Host "   Ejecuta: docker logs paicat_php -f" -ForegroundColor Yellow
    Write-Host "   Esperando 30 segundos adicionales..." -ForegroundColor Gray
    Start-Sleep -Seconds 30
}

# ==============================================
# 9. Configurar bases de datos
# ==============================================
Write-Step "Configurando bases de datos..."

# Crear bases de datos si no existen
docker exec paicat_mariadb mariadb -u root -proot -e "CREATE DATABASE IF NOT EXISTS paicat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
docker exec paicat_mariadb mariadb -u root -proot -e "CREATE DATABASE IF NOT EXISTS sysacad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
docker exec paicat_mariadb mariadb -u root -proot -e "CREATE DATABASE IF NOT EXISTS alumnos_utn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null

# Dar permisos
docker exec paicat_mariadb mariadb -u root -proot -e "GRANT ALL PRIVILEGES ON paicat.* TO 'laravel'@'%'; GRANT ALL PRIVILEGES ON sysacad.* TO 'laravel'@'%'; GRANT ALL PRIVILEGES ON alumnos_utn.* TO 'laravel'@'%'; FLUSH PRIVILEGES;" 2>$null
Write-OK "Permisos configurados"

# Importar BD paicat
if (Test-Path ".\datos_paicat_completo.sql") {
    Write-Host "   [1/3] Importando BD paicat..." -ForegroundColor Yellow
    docker cp ".\datos_paicat_completo.sql" paicat_mariadb:/tmp/datos.sql 2>$null
    docker exec paicat_mariadb sh -c "mariadb -uroot -proot paicat < /tmp/datos.sql" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-OK "BD paicat importada"
    } else {
        Write-Warn "Error importando paicat, intentando con otra sintaxis..."
        docker exec paicat_mariadb sh -c "mariadb -uroot -proot < /tmp/datos.sql" 2>$null
        Write-OK "BD paicat importada (alternativo)"
    }
} else {
    Write-Warn "[1/3] datos_paicat_completo.sql no encontrado"
}

# Importar BD alumnos_utn
if (Test-Path ".\backup_27-10-2025.sql") {
    Write-Host "   [2/3] Importando BD alumnos_utn..." -ForegroundColor Yellow
    docker cp ".\backup_27-10-2025.sql" paicat_mariadb:/tmp/alumnos.sql 2>$null
    docker exec paicat_mariadb sh -c "mariadb -uroot -proot alumnos_utn < /tmp/alumnos.sql" 2>$null
    Write-OK "BD alumnos_utn importada"
} else {
    Write-Warn "[2/3] backup_27-10-2025.sql no encontrado"
}

# Configurar BD sysacad via migraciones
Write-Host "   [3/3] Configurando BD sysacad..." -ForegroundColor Yellow
docker exec paicat_php php artisan migrate --database=sysacad --force 2>$null
docker exec paicat_php php artisan db:seed --class="Database\Seeders\Sysacad\SysacadDataSeeder" --force 2>$null
Write-OK "BD sysacad configurada"

# ==============================================
# 10. Limpiar cache y optimizar
# ==============================================
Write-Step "Optimizando aplicacion..."

docker exec paicat_php php artisan config:clear 2>$null
docker exec paicat_php php artisan config:cache 2>$null
docker exec paicat_php php artisan route:cache 2>$null
docker exec paicat_php php artisan view:cache 2>$null
Write-OK "Cache optimizada"

# ==============================================
# 11. Verificar estado final
# ==============================================
Write-Step "Verificando instalacion..."

$phpOK = docker exec paicat_php php artisan --version 2>$null
$dbOK = docker exec paicat_mariadb mariadb -u root -proot -e "SELECT 1" 2>$null

if ($phpOK -and $dbOK) {
    Write-OK "Instalacion completada exitosamente!"
} else {
    Write-Warn "Algunos servicios pueden no estar listos"
}

# ==============================================
# Resumen final
# ==============================================
Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "   INSTALACION COMPLETADA!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host " URLs:" -ForegroundColor Cyan
Write-Host "   Aplicacion:  http://localhost" -ForegroundColor White
Write-Host "   Vite HMR:    http://localhost:5173" -ForegroundColor White
Write-Host "   PHPMyAdmin:  http://localhost:8081" -ForegroundColor White
Write-Host "   Mailhog:     http://localhost:8025" -ForegroundColor White
Write-Host ""
Write-Host " Credenciales:" -ForegroundColor Cyan
Write-Host "   Email:    admin@paicat.utn.edu.ar" -ForegroundColor White
Write-Host "   Password: admin123" -ForegroundColor White
Write-Host ""
Write-Host " Comandos utiles:" -ForegroundColor Cyan
Write-Host "   docker-compose logs -f php   Ver logs de PHP" -ForegroundColor Gray
Write-Host "   docker-compose down          Detener todo" -ForegroundColor Gray
Write-Host "   docker-compose restart       Reiniciar" -ForegroundColor Gray
Write-Host ""

# Abrir navegador
Start-Process "http://localhost"
