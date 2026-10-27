#!/bin/bash
# Despliegue de PAICAT en el servidor compartido del Ingreso FRLP.
# Lo ejecuta el runner self-hosted (.github/workflows/deploy.yml) o a mano:
#   GH_TOKEN=<token> ~/paicat/deploy.sh
#
# IMPORTANTE: el servidor aloja otros sistemas. Este script solo toca
# ~/paicat y los contenedores del proyecto compose "paicat".
set -euo pipefail

APP_DIR="${APP_DIR:-$HOME/paicat}"
REPO="frlputn/paicat-ingreso"
BRANCH="main"
COMPOSE="docker compose -p paicat -f docker-compose.prod.yml"

cd "$APP_DIR"

echo "==> Actualizando código"
if [ -n "${GH_TOKEN:-}" ]; then
    # El token se usa solo en esta llamada; no queda guardado en .git/config
    git pull --ff-only "https://x-access-token:${GH_TOKEN}@github.com/${REPO}.git" "$BRANCH"
else
    git pull --ff-only origin "$BRANCH"
fi
git log --oneline -1

echo "==> Compilando frontend"
npm ci --no-audit --no-fund
npm run build

echo "==> Levantando contenedores"
$COMPOSE up -d --build --remove-orphans

# Compose no puede unir un servicio a la red "bridge" por defecto, donde está
# inscriptosClon (BD del Ingreso). Se conecta a mano tras cada recreación.
if ! docker inspect paicat_app --format '{{json .NetworkSettings.Networks}}' | grep -q '"bridge"'; then
    docker network connect bridge paicat_app
fi

echo "==> Permisos"
docker exec paicat_app sh -c "chown $(id -u):www-data .env && chmod 640 .env && chown -R www-data:www-data storage bootstrap/cache"

echo "==> Dependencias PHP y migraciones"
$COMPOSE exec -T -e COMPOSER_ALLOW_SUPERUSER=1 app composer install --no-dev --optimize-autoloader --no-interaction
$COMPOSE exec -T -u www-data -e HOME=/tmp app php artisan migrate --force
$COMPOSE exec -T -u www-data -e HOME=/tmp app php artisan optimize:clear

echo "==> Verificación"
for i in $(seq 1 15); do
    code=$(curl -s -o /dev/null -w '%{http_code}' http://localhost:9999/login || true)
    [ "$code" = "200" ] && break
    sleep 2
done
[ "$code" = "200" ] || { echo "ERROR: /login respondió $code"; exit 1; }
curl -s http://localhost:9999/robots.txt | grep -qx 'Disallow: /' || { echo "ERROR: robots.txt no bloquea la indexación"; exit 1; }

echo "==> Deploy OK"
