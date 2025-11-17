#!/bin/bash
# Monitor de Inicialización de PAICAT
# Script que muestra en tiempo real el progreso de inicialización del contenedor PHP

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
GRAY='\033[0;90m'
NC='\033[0m' # No Color

# Símbolos
CHECK="✅"
CROSS="❌"
HOURGLASS="⏳"
GEAR="⚙️ "
SPARKLES="✨"
ROCKET="🚀"
KEY="🔑"
CHART="📊"
BULB="💡"

function write_step() {
    local step=$1
    local status=$2
    
    case $status in
        "WAITING")
            echo -e "${YELLOW}${HOURGLASS} ${step} [${status}]${NC}"
            ;;
        "RUNNING")
            echo -e "${CYAN}${GEAR}${step} [${status}]${NC}"
            ;;
        "DONE")
            echo -e "${GREEN}${CHECK} ${step} [${status}]${NC}"
            ;;
        "ERROR")
            echo -e "${RED}${CROSS} ${step} [${status}]${NC}"
            ;;
        *)
            echo -e "${step} [${status}]"
            ;;
    esac
}

function show_progress_bar() {
    local current=$1
    local total=$2
    local activity=$3
    
    local percent=$((current * 100 / total))
    local completed=$((percent / 2))
    local remaining=$((50 - completed))
    
    local bar="["
    for ((i=0; i<completed; i++)); do bar="${bar}█"; done
    for ((i=0; i<remaining; i++)); do bar="${bar}░"; done
    bar="${bar}]"
    
    echo -ne "\r${CYAN}${bar} ${percent}% - ${activity}${NC}"
}

function get_container_log() {
    docker logs "$1" 2>&1
}

function test_log_contains() {
    local log=$1
    local pattern=$2
    
    echo "$log" | grep -q "$pattern"
}

clear

echo -e "${MAGENTA}═══════════════════════════════════════════════════════${NC}"
echo -e "${MAGENTA}   ${ROCKET} Monitor de Inicialización de PAICAT${NC}"
echo -e "${MAGENTA}═══════════════════════════════════════════════════════${NC}"
echo ""

# Verificar que Docker esté corriendo
echo -e "${YELLOW}🔍 Verificando Docker...${NC}"
if ! docker ps &> /dev/null; then
    echo -e "${RED}${CROSS} Docker no está corriendo. Inicia Docker primero.${NC}"
    exit 1
fi
echo -e "${GREEN}${CHECK} Docker está corriendo${NC}"
echo ""

# Verificar contenedores
echo -e "${YELLOW}🔍 Verificando contenedores...${NC}"
php_container=$(docker ps --filter "name=paicat_php" --format "{{.Names}}" 2>/dev/null)

if [ -z "$php_container" ]; then
    echo -e "${RED}${CROSS} El contenedor paicat_php no está corriendo.${NC}"
    echo -e "${YELLOW}   Ejecuta: docker-compose up -d${NC}"
    exit 1
fi

echo -e "${GREEN}${CHECK} Contenedores encontrados${NC}"
echo ""

# Pasos del proceso de inicialización
declare -A steps=(
    ["Instalación de dependencias Composer"]="Generating optimized autoload files:15"
    ["Descubrimiento de paquetes"]="Discovering packages:25"
    ["Generación de clave de aplicación"]="Application key set successfully:35"
    ["Limpieza de caché"]="Configuration cache cleared successfully:45"
    ["Ejecución de migraciones"]="Running migrations:60"
    ["Ejecución de seeders"]="Seeding database:70"
    ["Optimización de aplicación"]="Configuration cached successfully:80"
    ["Creación de enlaces simbólicos"]="link has been connected:90"
    ["Configuración de permisos"]="Configurando permisos:95"
    ["PHP-FPM iniciado"]="ready to handle connections:100"
)

declare -a step_order=(
    "Instalación de dependencias Composer"
    "Descubrimiento de paquetes"
    "Generación de clave de aplicación"
    "Limpieza de caché"
    "Ejecución de migraciones"
    "Ejecución de seeders"
    "Optimización de aplicación"
    "Creación de enlaces simbólicos"
    "Configuración de permisos"
    "PHP-FPM iniciado"
)

declare -a completed=()

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Monitoreo en tiempo real
max_attempts=120  # 2 minutos máximo
attempt=0
all_done=false

while [ "$all_done" = false ] && [ $attempt -lt $max_attempts ]; do
    ((attempt++))
    log=$(get_container_log "$php_container")
    
    # Verificar cada paso
    for step_name in "${step_order[@]}"; do
        if [[ ! " ${completed[@]} " =~ " ${step_name} " ]]; then
            IFS=':' read -r pattern progress <<< "${steps[$step_name]}"
            
            if test_log_contains "$log" "$pattern"; then
                completed+=("$step_name")
                
                # Limpiar línea de progreso anterior
                echo -ne "\r$(printf ' %.0s' {1..80})\r"
                
                # Mostrar paso completado
                write_step "$step_name" "DONE"
                
                # Mostrar barra de progreso
                show_progress_bar "$progress" 100 "$step_name"
                echo ""
            fi
        fi
    done
    
    # Verificar si todo está completo
    if [ ${#completed[@]} -eq ${#step_order[@]} ]; then
        all_done=true
        break
    fi
    
    sleep 0.5
done

echo ""
echo ""

if [ "$all_done" = true ]; then
    echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}   ${SPARKLES} ¡PAICAT INICIADO EXITOSAMENTE!${NC}"
    echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${CYAN}🌐 Aplicación:${NC}"
    echo -e "   http://localhost"
    echo ""
    echo -e "${CYAN}${KEY} Credenciales de acceso:${NC}"
    echo -e "   Email:    admin@paicat.utn.edu.ar"
    echo -e "   Password: admin123"
    echo ""
    echo -e "${CYAN}${CHART} Servicios disponibles:${NC}"
    echo -e "   • PHPMyAdmin: http://localhost:8081"
    echo -e "   • Mailhog:    http://localhost:8025"
    echo -e "   • Vite HMR:   http://localhost:5173"
    echo ""
    echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
else
    echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}   ⚠️  Inicialización en progreso o timeout${NC}"
    echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${YELLOW}El proceso está tomando más tiempo del esperado.${NC}"
    echo -e "${CYAN}Puedes verificar los logs manualmente con:${NC}"
    echo -e "   docker logs paicat_php -f"
    echo ""
fi

echo ""
echo -e "${GRAY}${BULB} Tip: Ejecuta este script después de 'docker-compose up -d'${NC}"
echo ""
