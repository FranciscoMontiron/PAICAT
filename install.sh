#!/usr/bin/env bash

# Script de instalación rápida para PAICAT
# Este script configura el proyecto automáticamente

set -e

echo " PAICAT - Instalación Rápida"
echo "================================"
echo ""

# Verificar Docker
if ! command -v docker &> /dev/null; then
    echo " Error: Docker no está instalado"
    echo "   Instala Docker Desktop desde: https://www.docker.com/products/docker-desktop"
    exit 1
fi

if ! docker info &> /dev/null; then
    echo " Error: Docker no está ejecutándose"
    echo "   Inicia Docker Desktop e intenta de nuevo"
    exit 1
fi

echo " Docker está disponible"

# Verificar Docker Compose
if ! command -v docker-compose &> /dev/null; then
    echo " Error: Docker Compose no está instalado"
    exit 1
fi

echo " Docker Compose está disponible"
echo ""

# Copiar .env.example a .env si no existe
if [ ! -f .env ]; then
    echo " Copiando .env.example a .env..."
    cp .env.example .env
    echo " Archivo .env creado"
else
    echo "  El archivo .env ya existe, no se sobrescribirá"
fi

echo ""
echo " Levantando contenedores Docker..."
docker-compose up -d

echo ""
echo " Iniciando monitor de progreso..."
echo "   (Presiona Ctrl+C si deseas saltar el monitoreo)"
echo ""
sleep 2

# Ejecutar monitor de startup
if [ -f "./monitor-startup.sh" ]; then
    chmod +x ./monitor-startup.sh
    ./monitor-startup.sh
else
    echo " Esperando a que los servicios estén listos..."
    sleep 30
    echo ""
    echo " Verificando estado de los contenedores..."
    docker-compose ps
fi

# Importar base de datos si existe el archivo SQL
if [ -f "./datos_paicat_completo.sql" ]; then
    echo ""
    echo " Importando base de datos..."
    
    # Esperar a que MariaDB esté listo
    echo "   Esperando a que MariaDB esté listo..."
    max_retries=30
    retry=0
    while [ $retry -lt $max_retries ]; do
        healthy=$(docker inspect --format="{{.State.Health.Status}}" paicat_mariadb 2>/dev/null)
        if [ "$healthy" = "healthy" ]; then
            break
        fi
        sleep 2
        retry=$((retry + 1))
    done
    
    if [ "$healthy" = "healthy" ]; then
        # Convertir encoding si es necesario (remover BOM y convertir a UTF-8)
        echo "   Preparando archivo SQL..."
        
        # Detectar si es UTF-16 y convertir
        if file "./datos_paicat_completo.sql" | grep -q "UTF-16"; then
            echo "   Convirtiendo de UTF-16 a UTF-8..."
            iconv -f UTF-16LE -t UTF-8 "./datos_paicat_completo.sql" > "./datos_paicat_utf8_temp.sql"
        else
            # Remover BOM si existe
            sed '1s/^\xEF\xBB\xBF//' "./datos_paicat_completo.sql" > "./datos_paicat_utf8_temp.sql"
        fi
        
        # Copiar al contenedor
        docker cp "./datos_paicat_utf8_temp.sql" paicat_mariadb:/tmp/datos.sql
        
        # Importar
        echo "   Ejecutando importación (puede tomar unos minutos)..."
        if docker exec paicat_mariadb sh -c "mariadb -uroot -proot < /tmp/datos.sql" 2>/dev/null; then
            echo " Base de datos importada correctamente"
        else
            echo " Error al importar la base de datos"
            echo "   Intenta manualmente: docker exec paicat_mariadb sh -c 'mariadb -uroot -proot < /tmp/datos.sql'"
        fi
        
        # Limpiar archivo temporal
        rm -f "./datos_paicat_utf8_temp.sql"
    else
        echo " MariaDB no está listo, importa la base de datos manualmente"
    fi
else
    echo ""
    echo " Archivo datos_paicat_completo.sql no encontrado"
    echo "   Coloca el archivo en la carpeta PAICAT y ejecuta:"
    echo "   docker cp datos_paicat_completo.sql paicat_mariadb:/tmp/"
    echo "   docker exec paicat_mariadb sh -c 'mariadb -uroot -proot < /tmp/datos_paicat_completo.sql'"
fi

echo ""
echo " ¡Instalación completada!"
echo ""
echo " Información importante:"
echo "    Aplicación: http://localhost"
echo "    Vite HMR: http://localhost:5173 (levantado automáticamente)"
echo "    PHPMyAdmin: http://localhost:8081"
echo "    Mailhog: http://localhost:8025"
echo ""
echo "      Usuario admin:"
echo "      Email: admin@paicat.utn.edu.ar"
echo "      Password: admin123"
echo ""
echo "  Comandos útiles:"
echo "   docker-compose logs -f       Ver logs"
echo "   docker-compose down          Detener contenedores"
echo "   docker-compose restart       Reiniciar contenedores"
echo ""
echo " Para más información, consulta README.md"
