# OnTheRise — один образ (Laravel + Node в одном контейнере, без лишних образов с Docker Hub)
FROM php:8.2-cli

# Системные пакеты и расширения PHP
RUN apt-get update && apt-get install -y \
    git unzip curl ca-certificates xz-utils \
    libzip-dev libpng-dev libonig-dev libxml2-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip exif pcntl bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 20 — установка из официального tarball (без NodeSource, стабильнее за сетью)
ENV NODE_VERSION=20.19.0
RUN curl -fsSL "https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64.tar.xz" \
    | tar -xJ -C /usr/local --strip-components=1 \
    && node -v && npm -v

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# OPcache
RUN echo 'opcache.enable=1\n\
opcache.enable_cli=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=10000\n\
opcache.validate_timestamps=0\n\
opcache.jit=1255\n\
opcache.jit_buffer_size=64M' > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www

COPY . .

# Каталоги Laravel и пути для Tailwind @source
RUN mkdir -p bootstrap/cache storage/logs storage/framework/sessions storage/framework/views storage/framework/cache database/data \
    vendor/laravel/framework/src/Illuminate/Pagination/resources/views storage/framework/views \
    && chmod -R 775 storage bootstrap/cache database/data

# Сборка фронта
RUN npm ci 2>/dev/null || npm install
RUN npm run build
RUN cp -r public/build /tmp/vite-build

# Зависимости PHP
RUN composer install --no-dev --prefer-dist --no-interaction

EXPOSE 8000

COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh 2>/dev/null || true && chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
