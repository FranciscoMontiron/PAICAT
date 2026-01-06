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

# Importar base de datos si existe el archivo SQL
if (Test-Path ".\datos_paicat_completo.sql") {
    Write-Host ""
    Write-Host " Importando base de datos..." -ForegroundColor Cyan
    
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
        # Convertir a UTF-8 si es necesario (Windows puede guardar en UTF-16)
        Write-Host "   Convirtiendo encoding del archivo SQL..." -ForegroundColor Yellow
        $sqlContent = Get-Content ".\datos_paicat_completo.sql" -Raw
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText(".\datos_paicat_utf8_temp.sql", $sqlContent, $utf8NoBom)
        
        # Copiar al contenedor
        docker cp ".\datos_paicat_utf8_temp.sql" paicat_mariadb:/tmp/datos.sql
        
        # Importar
        Write-Host "   Ejecutando importacion (puede tomar unos minutos)..." -ForegroundColor Yellow
        docker exec paicat_mariadb sh -c "mariadb -uroot -proot < /tmp/datos.sql" 2>$null
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host " Base de datos importada correctamente" -ForegroundColor Green
        } else {
            Write-Host " Error al importar la base de datos" -ForegroundColor Red
            Write-Host "   Intenta manualmente: docker exec paicat_mariadb sh -c 'mariadb -uroot -proot < /tmp/datos.sql'" -ForegroundColor Yellow
        }
        
        # Limpiar archivo temporal
        Remove-Item ".\datos_paicat_utf8_temp.sql" -ErrorAction SilentlyContinue
    } else {
        Write-Host " MariaDB no esta listo, importa la base de datos manualmente" -ForegroundColor Yellow
    }
} else {
    Write-Host ""
    Write-Host " Archivo datos_paicat_completo.sql no encontrado" -ForegroundColor Yellow
    Write-Host "   Coloca el archivo en la carpeta PAICAT y ejecuta:" -ForegroundColor Yellow
    Write-Host "   docker cp datos_paicat_completo.sql paicat_mariadb:/tmp/" -ForegroundColor White
    Write-Host "   docker exec paicat_mariadb sh -c 'mariadb -uroot -proot < /tmp/datos_paicat_completo.sql'" -ForegroundColor White
}

Write-Host ""
Write-Host " Instalacion completada!" -ForegroundColor Green
Write-Host ""
Write-Host " Informacion importante:" -ForegroundColor Cyan
Write-Host "   Aplicacion: http://localhost" -ForegroundColor White
Write-Host "   Vite HMR: http://localhost:5173 (levantado automaticamente)" -ForegroundColor White
Write-Host "   PHPMyAdmin: http://localhost:8081" -ForegroundColor White
Write-Host "   Mailhog: http://localhost:8025" -ForegroundColor White
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
