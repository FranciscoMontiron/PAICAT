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
