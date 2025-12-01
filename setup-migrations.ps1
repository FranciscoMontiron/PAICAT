# Script para configurar migraciones de PAICAT (Windows)
# Ejecutar desde la raíz del proyecto con: .\setup-migrations.ps1

Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║            PAICAT - Setup de Migraciones                     ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Verificar si Docker está corriendo
$container = docker ps --filter "name=paicat_php" --format "{{.Names}}" 2>$null

if (-not $container) {
    Write-Host "❌ Error: El contenedor paicat_php no está corriendo." -ForegroundColor Red
    Write-Host "   Ejecuta primero: docker-compose up -d" -ForegroundColor Yellow
    exit 1
}

Write-Host "Ejecutando configuración de migraciones..." -ForegroundColor Yellow
Write-Host ""

docker exec paicat_php php database/scripts/setup_migrations.php

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✅ Setup completado exitosamente." -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "❌ Hubo un error durante el setup." -ForegroundColor Red
    exit 1
}
