# Stage 1: build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm ci 2>/dev/null || npm install
RUN npm run build

# Stage 2: PHP app
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip exif pcntl bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo 'opcache.enable=1\n\
opcache.enable_cli=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=10000\n\
opcache.validate_timestamps=0\n\
opcache.jit=1255\n\
opcache.jit_buffer_size=64M' > /usr/local/etc/php/conf.d/opcache.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN mkdir -p bootstrap/cache storage/logs storage/framework/sessions storage/framework/views storage/framework/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --prefer-dist --no-interaction

# Copy built frontend from node stage (both to final location and backup for volume mounts)
COPY --from=frontend /app/public/build ./public/build
COPY --from=frontend /app/public/build /tmp/vite-build

EXPOSE 8000

COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
