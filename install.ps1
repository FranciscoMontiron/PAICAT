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

if ($LASTEXITCODE -ne 0) {
    Write-Host " Error al levantar los contenedores" -ForegroundColor Red
    Write-Host "   Revisa los logs con: docker-compose logs" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host " Esperando a que los contenedores esten listos..." -ForegroundColor Yellow

# Esperar a que el contenedor PHP este corriendo
$maxWait = 60
$waited = 0
while ($waited -lt $maxWait) {
    $phpContainer = docker ps --filter "name=paicat_php" --filter "status=running" --format "{{.Names}}" 2>$null
    if ($phpContainer) {
        Write-Host " Contenedor PHP esta corriendo" -ForegroundColor Green
        break
    }
    Start-Sleep -Seconds 2
    $waited += 2
    Write-Host "." -NoNewline -ForegroundColor Gray
}

if ($waited -ge $maxWait) {
    Write-Host ""
    Write-Host " Timeout esperando contenedor PHP" -ForegroundColor Red
    Write-Host "   Verifica los logs con: docker-compose logs php" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host " Iniciando monitor de progreso..." -ForegroundColor Cyan
Write-Host "   (Presiona Ctrl+C si deseas saltar el monitoreo)" -ForegroundColor Gray
Write-Host ""
Start-Sleep -Seconds 3

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

Write-Host ""
Write-Host " Instalacion completada!" -ForegroundColor Green
Write-Host ""
Write-Host " Informacion importante:" -ForegroundColor Cyan
Write-Host "   Aplicacion: http://localhost" -ForegroundColor White
Write-Host "   Vite HMR:   http://localhost:5173" -ForegroundColor White
Write-Host ""
Write-Host "   Usuario admin:" -ForegroundColor Cyan
Write-Host "      Email: admin@paicat.utn.edu.ar" -ForegroundColor White
Write-Host "      Password: admin123" -ForegroundColor White
Write-Host ""
Write-Host "   Base de datos (para MySQL Workbench, DBeaver, etc.):" -ForegroundColor Cyan
Write-Host "      Host:     localhost" -ForegroundColor White
Write-Host "      Puerto:   3308" -ForegroundColor White
Write-Host "      Usuario:  laravel" -ForegroundColor White
Write-Host "      Password: secret" -ForegroundColor White
Write-Host "      BD:       paicat" -ForegroundColor White
Write-Host ""
Write-Host "   Comandos utiles:" -ForegroundColor Cyan
Write-Host "   docker-compose logs -f       Ver logs" -ForegroundColor White
Write-Host "   docker-compose down          Detener contenedores" -ForegroundColor White
Write-Host "   docker-compose restart       Reiniciar contenedores" -ForegroundColor White
Write-Host ""
Write-Host "  Para mas informacion, consulta README.md" -ForegroundColor Yellow
