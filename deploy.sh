#!/usr/bin/env bash

set -Eeuo pipefail

readonly APP_ROOT="${APP_ROOT:-/var/www/desa-kalimati}"
readonly DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"

cd "$APP_ROOT"

php artisan down || true

cleanup() {
    php artisan up || true
}

trap cleanup EXIT

git pull origin "$DEPLOY_BRANCH"
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
npm ci
npm run build
php artisan queue:restart

echo "Deployment successfully completed."