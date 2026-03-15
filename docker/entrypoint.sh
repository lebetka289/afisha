#!/bin/sh
set -e

# Каталог для SQLite (часто смонтирован как volume)
mkdir -p /var/www/database/data
touch /var/www/database/data/database.sqlite 2>/dev/null || true
chmod -R 775 /var/www/database/data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Восстановить сборку фронта, если была перезаписана
if [ -d /tmp/vite-build ] && [ ! -f /var/www/public/build/manifest.json ]; then
    echo "Restoring Vite build..."
    rm -rf /var/www/public/build
    cp -r /tmp/vite-build /var/www/public/build
fi

echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link 2>/dev/null || true

echo "Seeding database (if needed)..."
php artisan db:seed --force 2>/dev/null || true

echo "Caching config..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

echo "Starting server at http://0.0.0.0:8000"
export PHP_CLI_SERVER_WORKERS=4
exec php artisan serve --host=0.0.0.0 --port=8000
