## Afisha — билеты, хайп и никакого зашквара

Добро пожаловать в `Afisha` — это не просто афиша, а **флекс по мероприятиям**: концерты, стендапы, ивенты и прочий движ, где можно красиво залететь без боли и спама.  
Laravel + React + Inertia тут устроили тусу, так что всё летает и не разваливается (надеемся).

![Зумерский хомяк](https://cont.ws/uploads/posts/2656155.jpg)

---

### Что умеет этот зверь

- **Лента событий**: список ивентов с городами, категориями, артистами, датами и т.п.  
- **Карточка события**: детальная страница, схема мест, описание, цены, можно сразу вкатиться в бронирование.
- **Бронирование билетов**: оформление брони через `BookingController` с привязкой к юзеру.  
- **Избранное**:
  - **События** — `event_favorites` (чтоб не потерять хайповый концерт).
  - **Артисты** — `artist_favorites` (подписка на любимых крашей со сцены).
- **Личный кабинет (`/cabinet`)**:
  - Список бронирований и детали по каждому.
  - Избранное: артисты и события.
  - Настройки аккаунта, город пользователя, аватар.
- **Артисты**:
  - Список артистов.
  - Страница артиста с событиями и инфой.
- **Рекомендации**:
  - `/recommendations` под auth — рекомендации ивентов на основе активности (хайповые подборки без зашквара).
- **Поиск**:
  - `/search` и `/search/suggest` — поиск по ивентам с подсказками.
- **Админка (`/admin`)**:
  - **События**: CRUD по ивентам.
  - **Площадки (venues)**: CRUD по площадкам, координаты и доп. инфа.
  - **Артисты**: CRUD по артистам, связь с событиями.
- **Медиа**:
  - `/media/{path}` – раздача картинок/файлов через `MediaController`, можно красиво хранить афиши и фоточки.

Короче, полноценная **онлайн-афиша без боли старых PHP-монолитов**. Эщкере.

---

### Техностек — фулл флекс
![Флекс на афише](https://i.pinimg.com/736x/0b/f8/e4/0bf8e44d2a890131d6309e2266ecd679.jpg)
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: React 19 + Inertia.js (`@inertiajs/react`)
- **Сборка**: Vite 7 + Tailwind CSS 4
- **Маршруты**: классический `routes/web.php` с auth, cabinet и admin.
- **База**: миграции под:
  - `users`, `venues`, `events`, `event_sections`, `event_seats`
  - `bookings`, `booking_items`, `event_addons`, `booking_addons`
  - избранное, города, просмотры событий, артисты и пр.
- **Dev-утилиты**:
  - `laravel/pint` — кодстайл без зашквара
  - `phpunit` — тестики, чтоб всё не падало
  - `concurrently` — запускаем всё одним шотом

---

### Как запустить локально (без паники)

#### 1. Предусловия

- PHP ≥ 8.2  
- Composer  
- Node.js (LTS) + npm  
- MySQL/PostgreSQL или SQLite (как удобнее)  
- (Опционально) Docker + Docker Compose, если хочешь вообще не париться

#### 2. Быстрый старт через Composer-скрипт

В корне проекта:

```bash
composer run setup
```

Этот скрипт сам:
- поставит PHP-зависимости  
- создаст `.env`, если его ещё нет  
- сгенерит `APP_KEY`  
- прогонит миграции  
- поставит npm-зависимости  
- соберёт фронт через `npm run build`

Если хочешь сам флексить руками — см. ниже.

---

### Ручная установка (олдскульный, но не зашквар)

```bash
# 1. PHP-зависимости
composer install

# 2. ENV
cp .env.example .env
php artisan key:generate

# 3. Настроить .env
# DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Миграции и сиды
php artisan migrate --force
php artisan db:seed
```

---

### Frontend & Dev-сервер

```bash
# Зависимости фронта
npm install

# Dev-режим (Vite + Laravel одновременно)
npm run dev

# Production-сборка
npm run build
```

Для полного **зумер-флекса** также есть dev-скрипт в `composer.json`:

```bash
composer run dev
```

Он через `concurrently` стартанёт:
- `php artisan serve`
- `php artisan queue:listen`
- `php artisan pail` (логи)
- `npm run dev`

Один шот — четыре процесса. Красиво.

---

### Полезные URL’ы

- **Главная афиша**: `/`  
- **Событие**: `/events/{slug}`  
- **Артисты**:
  - список: `/artists`
  - страница артиста: `/artists/{slug}`
- **Избранное**:
  - события: кнопка на странице события (`/events/{slug}/favorite`)
  - артисты: на странице артиста (`/artists/{slug}/favorite`)
- **Поиск**:
  - страница поиска: `/search`
  - подсказки: `/search/suggest`
- **Рекомендации**: `/recommendations` (auth)
- **Кабинет**: `/cabinet`
- **Админка**: `/admin` (middleware `auth` + `admin`)

---






![Когда билет куплен, а денег нет](https://i.ibb.co/F4cwcK4y/dem-69b709f4855c7.png)

### Структура проекта по-простому

- `app/Models` — модели: `User`, `Venue`, `Event`, `Artist`, `Booking` и т.д.  
- `app/Http/Controllers`:
  - публичные контроллеры: `EventController`, `ArtistController`, `SearchController`, `BookingController`, `RecommendationController` и т.п.
  - админские: `Admin\EventController`, `Admin\VenueController`, `Admin\ArtistController`
  - auth: `Auth\LoginController`, `Auth\RegisterController`
- `database/migrations` — вся схема БД.
- `database/seeders` — базовые сидеры (города и прочий стартовый контент).
- `resources/js`:
  - `app.jsx` — вход для Inertia + React
  - `Pages/Events`, `Pages/Artists`, `Pages/Cabinet`, `Pages/Admin` — страницы фронта
  - `Layouts/AppLayout.jsx` — общий лэйаут
- `resources/views/app.blade.php` — оболочка под Inertia-frontend.
- `routes/web.php` — все веб-маршруты (public, cabinet, admin).

---

### Команды для девелопера-флекса

```bash
# Тесты
php artisan test

# Линтинг PHP
vendor/bin/pint

# Очистить и обновить кеш конфига
php artisan config:clear
php artisan cache:clear
```

---

### Что можно допилить (если хочется ещё больше хайпа)

- **Фильтры по городам/категориям** прямо на главной афише.
- **Больше аналитики**: отслеживание просмотров, кликов, конверсий в брони.
- **Уведомления**: e-mail/Telegram/whatever, когда любимый артист анонсит новый ивент.
- **Темная тема**: чтобы ночью смотреть афишу и не ловить зашквар от белого экрана.

---

Если что-то хочется переписать, оптимизировать или навесить ещё фич — этот проект уже готов, чтобы на нём **флексить как senior зумер-разработчик**, а не страдать в legacy.
