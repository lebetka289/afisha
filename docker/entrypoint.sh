#!/bin/sh
set -e

# Ensure SQLite file exists
mkdir -p database
touch database/database.sqlite
chmod -R 775 database

echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link --force || true

echo "Seeding database..."
php artisan db:seed --force

echo "Starting server..."
exec php artisan serve --host=0.0.0.0 --port=8000
