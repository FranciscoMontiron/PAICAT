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

Write-Host ""
Write-Host " Instalacion completada!" -ForegroundColor Green
Write-Host ""
Write-Host " Informacion importante:" -ForegroundColor Cyan
Write-Host "   Aplicacion: http://localhost" -ForegroundColor White
Write-Host "   Vite HMR:   Ejecutar 'npm run dev' localmente" -ForegroundColor White
Write-Host ""
Write-Host "   Usuario admin:" -ForegroundColor Cyan
Write-Host "      Email: admin@paicat.utn.edu.ar" -ForegroundColor White
Write-Host "      Password: admin123" -ForegroundColor White
Write-Host ""
Write-Host "   Comandos utiles:" -ForegroundColor Cyan
Write-Host "   docker-compose logs -f       Ver logs" -ForegroundColor White
Write-Host "   docker-compose down          Detener contenedores" -ForegroundColor White
Write-Host "   docker-compose restart       Reiniciar contenedores" -ForegroundColor White
Write-Host ""
Write-Host "  Para mas informacion, consulta README.md" -ForegroundColor Yellow
