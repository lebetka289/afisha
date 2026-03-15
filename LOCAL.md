# Запуск проекта OnTheRise локально (без Docker)

Требования: **PHP 8.2+**, **Composer**, **Node.js 18+**, **npm**.

---

## 1. Установка зависимостей

```powershell
cd "c:\Users\maksm\OneDrive\Desktop\afisha-maxon4ik"

# PHP-зависимости
composer install

# JS-зависимости и сборка фронта
npm install
npm run build
```

---

## 2. Окружение и база

```powershell
# Скопировать пример env (если ещё не скопирован)
copy .env.example .env

# Сгенерировать ключ приложения
php artisan key:generate

# SQLite: создать файл БД (по умолчанию в .env уже sqlite)
# Если в .env указан DB_CONNECTION=sqlite и DB_DATABASE=database/database.sqlite:
New-Item -ItemType File -Path database\database.sqlite -Force

# Миграции
php artisan migrate
```

---

## 3. Запуск сервера

```powershell
php artisan serve
```

Открой в браузере: **http://127.0.0.1:8000**

---

## 4. (Опционально) Создать админа

```powershell
php artisan make:admin your@email.com
```

---

## Если снова соберёшь через Docker

В Dockerfile Composer ставится без образа `composer:2`, чтобы не дергать Docker Hub (TLS timeout). Если ошибка была только на шаге `COPY --from=composer:2`, пересобери:

```powershell
docker compose build --no-cache
docker compose up -d
```

Если падает на шаге `FROM node:20-alpine` или `FROM php:8.2-cli` — это снова обращение к Docker Hub. Тогда:

- Проверь интернет и VPN/прокси.
- В Docker Desktop: **Settings → Resources → Network** — попробуй другой DNS или отключи VPN для Docker.
- Или используй только локальный запуск командами выше.
