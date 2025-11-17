# Script de instalación rápida para PAICAT en Windows
# Ejecutar con: .\install.ps1

Write-Host " PAICAT - Instalación Rápida" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Verificar Docker
Write-Host "Verificando Docker..." -ForegroundColor Yellow
try {
    docker info | Out-Null
    Write-Host " Docker está disponible" -ForegroundColor Green
} catch {
    Write-Host " Error: Docker no está ejecutándose" -ForegroundColor Red
    Write-Host "   Inicia Docker Desktop e intenta de nuevo" -ForegroundColor Yellow
    exit 1
}

# Verificar Docker Compose
try {
    docker-compose --version | Out-Null
    Write-Host " Docker Compose está disponible" -ForegroundColor Green
} catch {
    Write-Host " Error: Docker Compose no está instalado" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Copiar .env.example a .env si no existe
if (-not (Test-Path .env)) {
    Write-Host " Copiando .env.example a .env..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    Write-Host " Archivo .env creado" -ForegroundColor Green
} else {
    Write-Host "  El archivo .env ya existe, no se sobrescribirá" -ForegroundColor Yellow
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
if (Test-Path ".\monitor.ps1") {
    & ".\monitor.ps1"
} else {
    Write-Host " Esperando a que los servicios estén listos..." -ForegroundColor Yellow
    Start-Sleep -Seconds 10
    Write-Host ""
    Write-Host " Verificando estado de los contenedores..." -ForegroundColor Yellow
    docker-compose ps
}

Write-Host ""
Write-Host "✅ ¡Instalación completada!" -ForegroundColor Green
Write-Host ""
Write-Host "📌 Información importante:" -ForegroundColor Cyan
Write-Host "   🌐 Aplicación: http://localhost" -ForegroundColor White
Write-Host "   ⚡ Vite HMR: http://localhost:5173 (levantado automáticamente)" -ForegroundColor White
Write-Host "   🗄️  PHPMyAdmin: http://localhost:8081" -ForegroundColor White
Write-Host "   📧 Mailhog: http://localhost:8025" -ForegroundColor White
Write-Host ""
Write-Host "    Usuario admin:" -ForegroundColor Cyan
Write-Host "      Email: admin@paicat.utn.edu.ar" -ForegroundColor White
Write-Host "      Password: admin123" -ForegroundColor White
Write-Host ""
Write-Host "   Comandos útiles:" -ForegroundColor Cyan
Write-Host "   docker-compose logs -f       Ver logs" -ForegroundColor White
Write-Host "   docker-compose down          Detener contenedores" -ForegroundColor White
Write-Host "   docker-compose restart       Reiniciar contenedores" -ForegroundColor White
Write-Host ""
Write-Host "  Para más información, consulta README.md" -ForegroundColor Yellow
