# OnTheRise — запуск через Docker

По ТЗ: веб-платформа для продвижения артистов в хип-хоп индустрии и продажи билетов на мероприятия.

## Требования

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (включает Docker Compose).

## Запуск

```powershell
cd "c:\Users\maksm\OneDrive\Desktop\afisha-maxon4ik"
docker compose up --build
```

Или двойной щелчок по **start.bat**.

При первом запуске:
- Собирается один образ (PHP 8.2 + Node 20 + Composer внутри).
- Выполняются миграции и сидер (тестовые мероприятия, площадки, артисты).
- Сервер доступен по адресу: **http://localhost:8000**

## Остановка

```powershell
docker compose down
```

Данные SQLite хранятся в volume `app_database`. Полное удаление с данными:

```powershell
docker compose down -v
```

## Полезные команды

**Все команды Artisan нужно выполнять внутри контейнера**, иначе на Windows может появиться ошибка `could not find driver` (локальный PHP без драйвера SQLite):

```powershell
# Миграции
docker compose exec app php artisan migrate --force

# Миграции заново + сидер
docker compose exec app php artisan migrate:fresh --seed --force

# Создать администратора
docker compose exec app php artisan make:admin your@email.com

# Логи
docker compose logs -f app
```

Если вы запускали `php artisan migrate` в терминале на Windows и получили **could not find driver**: не используйте локальный PHP для этого проекта — запускайте команды через `docker compose exec app php artisan ...`.

## Если ошибка TLS / timeout при сборке

Используется **один образ** — `php:8.2-cli`. Node ставится из официального tarball (nodejs.org), без образа `node:20-alpine`.

Если не удаётся скачать базовый образ:
1. При нормальном интернете выполните: `docker pull php:8.2-cli`, затем снова `docker compose up --build`.
2. В Docker Desktop можно настроить зеркало Docker Hub (Settings → Docker Engine → `registry-mirrors`).
3. Проверьте VPN/фаервол при необходимости.
