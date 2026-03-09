#!/bin/sh
set -e

# Wait for MySQL to be ready (already waited via depends_on healthcheck, but double-check)
until php -r "
  try {
    new PDO(
      'mysql:host=${DB_HOST};dbname=${DB_DATABASE}',
      '${DB_USERNAME}',
      '${DB_PASSWORD}',
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    exit(0);
  } catch (Throwable \$e) {
    exit(1);
  }
" 2>/dev/null; do
  echo "Waiting for MySQL..."
  sleep 2
done

echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link --force || true

echo "Seeding database..."
php artisan db:seed --force

echo "Starting server..."
exec php artisan serve --host=0.0.0.0 --port=8000
