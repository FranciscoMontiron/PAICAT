# Script para crear el archivo de datos para compartir
# Ejecutar: .\create-data-package.ps1
# Esto crea paicat-data.zip que se puede subir a Drive

Write-Host ""
Write-Host " PAICAT - Crear paquete de datos" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

$dataFiles = @(
    ".\datos_paicat_completo.sql",
    ".\backup_27-10-2025.sql",
    ".\database\data\Datos Sysacad.xlsx"
)

$missingFiles = @()

foreach ($file in $dataFiles) {
    if (Test-Path $file) {
        Write-Host "  Encontrado: $file" -ForegroundColor Green
    } else {
        Write-Host "  Falta: $file" -ForegroundColor Red
        $missingFiles += $file
    }
}

if ($missingFiles.Count -gt 0) {
    Write-Host ""
    Write-Host " Faltan archivos necesarios. Asegurate de tener:" -ForegroundColor Red
    Write-Host "   - datos_paicat_completo.sql (exportar desde phpMyAdmin)" -ForegroundColor Yellow
    Write-Host "   - backup_27-10-2025.sql (datos de alumnos)" -ForegroundColor Yellow
    Write-Host "   - database\data\Datos Sysacad.xlsx (datos maestros)" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host " Creando paicat-data.zip..." -ForegroundColor Yellow

# Eliminar ZIP anterior si existe
if (Test-Path ".\paicat-data.zip") {
    Remove-Item ".\paicat-data.zip" -Force
}

# Crear el ZIP
Compress-Archive -Path $dataFiles -DestinationPath ".\paicat-data.zip" -CompressionLevel Optimal

if (Test-Path ".\paicat-data.zip") {
    $size = (Get-Item ".\paicat-data.zip").Length / 1MB
    Write-Host ""
    Write-Host " paicat-data.zip creado exitosamente!" -ForegroundColor Green
    Write-Host "   Tamanio: $([math]::Round($size, 2)) MB" -ForegroundColor White
    Write-Host ""
    Write-Host " Instrucciones:" -ForegroundColor Cyan
    Write-Host "   1. Subi paicat-data.zip a Google Drive" -ForegroundColor White
    Write-Host "   2. Compartilo solo con las personas autorizadas" -ForegroundColor White
    Write-Host "   3. Al clonar el repo, descargarlo y ponerlo en la raiz" -ForegroundColor White
    Write-Host "   4. Ejecutar .\install.ps1" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host " Error al crear el archivo ZIP" -ForegroundColor Red
    exit 1
}
