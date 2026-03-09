# Запуск проекта через Docker

## Требования

- Установленные [Docker Desktop](https://www.docker.com/products/docker-desktop/) (включает Docker Compose)

## Запуск одной кнопкой (Windows)

1. **Двойной щелчок по `start.bat`** — запускает MySQL и приложение, в одном окне выводятся логи. Сайт: **http://localhost:8000**. Остановка: закройте окно или нажмите Ctrl+C.

2. **Двойной щелчок по `start-background.bat`** — запуск в фоне (окно можно закрыть), откроется браузер. Остановка: запустите `stop.bat`.

3. **`stop.bat`** — остановить все контейнеры.

## Запуск из терминала

```bash
docker compose up --build
```

При первом запуске:

1. Собирается образ приложения (PHP 8.2, Composer, Node, сборка фронта).
2. Поднимается MySQL 8.0 с базой `afisha`.
3. После готовности MySQL выполняются миграции и сидер (заполнение тестовыми данными).
4. Запускается сервер приложения.

Сайт доступен по адресу: **http://localhost:8000**

- Главная — список событий.
- Событие по slug, например: http://localhost:8000/events/neon-lights-tour
- Админка: http://localhost:8000/admin (события и площадки)

## Остановка

```bash
docker compose down
```

Данные MySQL сохраняются в volume `mysql_data`. Чтобы удалить и их:

```bash
docker compose down -v
```

## Перезапуск миграций и сидера

```bash
docker compose exec app php artisan migrate:fresh --seed --force
```

## Подключение к MySQL с хоста

- Хост: `127.0.0.1`
- Порт: `3306`
- База: `afisha`
- Пользователь: `afisha`
- Пароль: `secret`
- Root-пароль: `root`
