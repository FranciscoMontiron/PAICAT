#!/bin/bash

# Script para configurar migraciones de PAICAT
# Ejecutar desde la raíz del proyecto

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║            PAICAT - Setup de Migraciones                     ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Verificar si Docker está corriendo
if ! docker ps | grep -q paicat_php; then
    echo "❌ Error: El contenedor paicat_php no está corriendo."
    echo "   Ejecuta primero: docker-compose up -d"
    exit 1
fi

echo "Ejecutando configuración de migraciones..."
echo ""

docker exec paicat_php php database/scripts/setup_migrations.php

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Setup completado exitosamente."
else
    echo ""
    echo "❌ Hubo un error durante el setup."
    exit 1
fi
