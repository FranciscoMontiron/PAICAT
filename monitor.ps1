# Monitor de Inicializacion de PAICAT
# Muestra el progreso de inicializacion en tiempo real

$ErrorActionPreference = 'SilentlyContinue'

function Write-Step {
    param($Step, $Status)
    $color = switch ($Status) {
        "WAITING" { "Yellow" }
        "RUNNING" { "Cyan" }
        "DONE" { "Green" }
        "ERROR" { "Red" }
        default { "White" }
    }
    
    $symbol = switch ($Status) {
        "WAITING" { "[*]" }
        "RUNNING" { "[>]" }
        "DONE" { "[+]" }
        "ERROR" { "[!]" }
        default { "[-]" }
    }
    
    Write-Host "$symbol $Step" -ForegroundColor $color -NoNewline
    Write-Host " [$Status]" -ForegroundColor $color
}

function Show-ProgressBar {
    param($Current, $Total)
    
    $percent = [math]::Round(($Current / $Total) * 100)
    $completed = [math]::Floor($percent / 2)
    $remaining = 50 - $completed
    
    $bar = "[" + ("=" * $completed) + ("." * $remaining) + "]"
    Write-Host "    $bar $percent%" -ForegroundColor Cyan
}

Clear-Host

Write-Host "=======================================================" -ForegroundColor Magenta
Write-Host "   Monitor de Inicializacion de PAICAT" -ForegroundColor Magenta
Write-Host "=======================================================" -ForegroundColor Magenta
Write-Host ""

# Verificar Docker
Write-Host "[?] Verificando Docker..." -ForegroundColor Yellow
$dockerRunning = docker ps 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "[!] Docker no esta corriendo" -ForegroundColor Red
    exit 1
}
Write-Host "[+] Docker esta corriendo" -ForegroundColor Green
Write-Host ""

# Verificar contenedores
Write-Host "[?] Verificando contenedores..." -ForegroundColor Yellow
$phpContainer = docker ps --filter "name=paicat_php" --format "{{.Names}}" 2>&1

if (-not $phpContainer) {
    Write-Host "[!] El contenedor paicat_php no esta corriendo" -ForegroundColor Red
    Write-Host "    Ejecuta: docker-compose up -d" -ForegroundColor Yellow
    exit 1
}

Write-Host "[+] Contenedores encontrados" -ForegroundColor Green
Write-Host ""
Write-Host "-------------------------------------------------------" -ForegroundColor Blue
Write-Host ""

# Pasos del proceso
$steps = @(
    @{ Name = "Instalacion de dependencias Composer"; Pattern = "Generating optimized autoload files"; Progress = 15 }
    @{ Name = "Descubrimiento de paquetes"; Pattern = "Discovering packages"; Progress = 25 }
    @{ Name = "Generacion de clave de aplicacion"; Pattern = "Application key set successfully"; Progress = 35 }
    @{ Name = "Limpieza de cache"; Pattern = "Configuration cache cleared successfully"; Progress = 45 }
    @{ Name = "Ejecucion de migraciones"; Pattern = "Running migrations"; Progress = 60 }
    @{ Name = "Ejecucion de seeders"; Pattern = "Seeding database"; Progress = 70 }
    @{ Name = "Optimizacion de aplicacion"; Pattern = "Configuration cached successfully"; Progress = 80 }
    @{ Name = "Creacion de enlaces simbolicos"; Pattern = "link has been connected"; Progress = 90 }
    @{ Name = "Configuracion de permisos"; Pattern = "Configurando permisos"; Progress = 95 }
    @{ Name = "PHP-FPM iniciado"; Pattern = "ready to handle connections"; Progress = 100 }
)

$completed = @()
$maxAttempts = 120
$attempt = 0
$allDone = $false

while (-not $allDone -and $attempt -lt $maxAttempts) {
    $attempt++
    $log = docker logs $phpContainer 2>&1 | Out-String
    
    foreach ($step in $steps) {
        if ($step.Name -notin $completed) {
            if ($log -match [regex]::Escape($step.Pattern)) {
                $completed += $step.Name
                Write-Step -Step $step.Name -Status "DONE"
                Show-ProgressBar -Current $step.Progress -Total 100
                Write-Host ""
            }
        }
    }
    
    if ($completed.Count -eq $steps.Count) {
        $allDone = $true
        break
    }
    
    Start-Sleep -Milliseconds 500
}

Write-Host ""

if ($allDone) {
    Write-Host "=======================================================" -ForegroundColor Green
    Write-Host "   PAICAT INICIADO EXITOSAMENTE!" -ForegroundColor Green
    Write-Host "=======================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "[*] Aplicacion:" -ForegroundColor Cyan
    Write-Host "    http://localhost" -ForegroundColor White
    Write-Host ""
    Write-Host "[*] Credenciales:" -ForegroundColor Cyan
    Write-Host "    Email:    admin@paicat.utn.edu.ar" -ForegroundColor White
    Write-Host "    Password: admin123" -ForegroundColor White
    Write-Host ""
    Write-Host "[*] Servicios:" -ForegroundColor Cyan
    Write-Host "    PHPMyAdmin: http://localhost:8081" -ForegroundColor White
    Write-Host "    Mailhog:    http://localhost:8025" -ForegroundColor White
    Write-Host "    Vite HMR:   http://localhost:5173" -ForegroundColor White
    Write-Host ""
    Write-Host "=======================================================" -ForegroundColor Green
} else {
    Write-Host "=======================================================" -ForegroundColor Yellow
    Write-Host "   [!] Inicializacion en progreso o timeout" -ForegroundColor Yellow
    Write-Host "=======================================================" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Verifica los logs manualmente:" -ForegroundColor Yellow
    Write-Host "  docker logs paicat_php -f" -ForegroundColor White
    Write-Host ""
}

Write-Host ""
Write-Host "[i] Ejecuta este script despues de 'docker-compose up -d'" -ForegroundColor Gray
Write-Host ""
