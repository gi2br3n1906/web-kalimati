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
php artisan optimize:clear
php artisan filament:assets

required_filament_assets=(
    "public/js/filament/filament/app.js"
    "public/js/filament/support/support.js"
    "public/js/filament/forms/components/rich-editor.js"
    "public/js/filament/forms/components/select.js"
    "public/js/filament/forms/components/file-upload.js"
)

for asset in "${required_filament_assets[@]}"; do
    if [[ ! -s "$asset" ]]; then
        echo "Required Filament asset is missing or empty: $asset" >&2
        exit 1
    fi
done

php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
npm ci
npm run build

if [[ ! -s "public/build/manifest.json" ]]; then
    echo "Vite manifest is missing or empty after npm run build." >&2
    exit 1
fi

php artisan queue:restart

echo "Deployment successfully completed."