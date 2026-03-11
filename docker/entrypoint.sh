#!/bin/sh
set -e

# Ensure SQLite file exists
mkdir -p database
touch database/database.sqlite
chmod -R 775 database

# Restore Vite build assets (volume mount may overwrite them)
if [ -d /tmp/vite-build ]; then
    echo "Restoring Vite build assets..."
    rm -rf public/build
    cp -r /tmp/vite-build public/build
fi

echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link --force || true

echo "Seeding database..."
php artisan db:seed --force

echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting server..."
export PHP_CLI_SERVER_WORKERS=4
exec php artisan serve --host=0.0.0.0 --port=8000
