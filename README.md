# Laravel Support Ticket API

Production-ready support ticket REST API built with Laravel 12, JWT authentication, RBAC, Redis caching, and queue workers.

> **Requires PHP 8.2+** — XAMPP default PHP 8.1 will fail with `unexpected token "readonly"`.  
> Run `php check-php.php` to verify. Fix: [docs/PHP-UPGRADE.md](docs/PHP-UPGRADE.md) or use `.\artisan.ps1` with Docker.

## Tech Stack

- Laravel 12
- MySQL 8
- Redis 7
- JWT Auth (`php-open-source-saver/jwt-auth`)
- PHPUnit 11
- Docker & Docker Compose

## Features

- JWT authentication (register, login, logout, refresh, profile)
- Role-based access control (Admin, Agent, Customer)
- User management
- Ticket management with status and priority
- Comments on tickets
- File attachments
- Dashboard statistics with Redis caching
- Queue jobs for ticket notifications and cache invalidation

## Architecture

- **Service Layer** — business logic in dedicated services
- **Repository Pattern** — data access abstracted behind interfaces
- **Policies** — authorization rules per resource
- **Form Requests** — input validation
- **API Resources** — consistent JSON responses

## Admin Panel (Web UI)

A browser-based admin panel is available for administrators to monitor all system activity.

**URL:** http://localhost:8080/admin/login

| Page | URL |
|------|-----|
| Login | `/admin/login` |
| Dashboard | `/admin` |
| All Activities | `/admin/activities` |
| Tickets | `/admin/tickets` |
| Users | `/admin/users` |

**Admin login:** `admin@example.com` / `password`

The admin panel uses **session-based login** (web guard). Only users with the **admin** role can access it. Agents and customers are blocked.

---

## RBAC — Custom (Not Spatie)

This project does **NOT** use [Spatie Laravel Permission](https://github.com/spatie/laravel-permission). RBAC is implemented with custom tables and Laravel Policies:

| Custom Implementation | Spatie Equivalent |
|----------------------|-------------------|
| `roles` table | `roles` |
| `permissions` table | `permissions` |
| `permission_role` pivot | `role_has_permissions` |
| `users.role_id` | `model_has_roles` |
| `User::hasPermission()` | `$user->can('permission')` |
| Laravel Policies | Gate + Policies |

**Why custom?** Full control, no extra package dependency, demonstrates understanding of RBAC fundamentals.

---

## Database Setup

### Option 1: Docker (recommended)

```bash
docker compose up -d --build
```

This automatically runs migrations and seeds the database.

### Option 2: Manual (XAMPP / local MySQL)

1. Create a MySQL database named `support_tickets`
2. Copy environment file and configure:

```bash
cp .env.example .env
```

Set in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=support_tickets
DB_USERNAME=root
DB_PASSWORD=
```

3. Run migrations and seeders:

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

This creates all tables and default users:

| Table | Purpose |
|-------|---------|
| `users` | All users |
| `roles` | admin, agent, customer |
| `permissions` | users.view, tickets.create, etc. |
| `permission_role` | Links roles to permissions |
| `tickets` | Support tickets |
| `comments` | Ticket comments |
| `attachments` | File uploads |
| `sessions` | Admin panel login sessions |
| `cache`, `jobs` | Redis/database support |

### Reset database

```bash
php artisan migrate:fresh --seed
```

---

## Quick Start (Docker)

```bash
cd laravel-support-ticket-api
docker compose up -d --build
```

API available at: http://localhost:8080

### Default Users

| Role     | Email               | Password  |
|----------|---------------------|-----------|
| Admin    | admin@test.com   | password  |
| Agent    | agent@test.com   | password  |
| Customer | customer@test.com| password  |

## API Endpoints

Base URL: `/api/v1`

### Authentication

| Method | Endpoint           | Auth | Description        |
|--------|--------------------|------|--------------------|
| POST   | /auth/register     | No   | Register customer  |
| POST   | /auth/login        | No   | Login              |
| POST   | /auth/logout       | Yes  | Logout             |
| POST   | /auth/refresh      | Yes  | Refresh token      |
| GET    | /auth/me           | Yes  | Current user       |

### Users (Admin)

| Method | Endpoint    | Description   |
|--------|-------------|---------------|
| GET    | /users      | List users    |
| POST   | /users      | Create user   |
| GET    | /users/{id} | Show user     |
| PUT    | /users/{id} | Update user   |
| DELETE | /users/{id} | Delete user   |

### Tickets

| Method | Endpoint       | Description    |
|--------|----------------|----------------|
| GET    | /tickets       | List tickets   |
| POST   | /tickets       | Create ticket  |
| GET    | /tickets/{id}  | Show ticket    |
| PUT    | /tickets/{id}  | Update ticket  |
| DELETE | /tickets/{id}  | Delete ticket  |

### Comments

| Method | Endpoint                      | Description     |
|--------|-------------------------------|-----------------|
| GET    | /tickets/{id}/comments        | List comments   |
| POST   | /tickets/{id}/comments        | Add comment     |
| PUT    | /comments/{id}                | Update comment  |
| DELETE | /comments/{id}                | Delete comment  |

### Attachments

| Method | Endpoint                                      | Description       |
|--------|-----------------------------------------------|-------------------|
| GET    | /tickets/{id}/attachments                     | List attachments  |
| POST   | /tickets/{id}/attachments                     | Upload file       |
| GET    | /tickets/{id}/attachments/{id}/download       | Download file     |
| DELETE | /tickets/{id}/attachments/{id}                | Delete attachment |

### Dashboard

| Method | Endpoint   | Description              |
|--------|------------|--------------------------|
| GET    | /dashboard | Role-based statistics    |
| GET    | /roles     | List roles & permissions |

## Authentication

Include JWT token in requests:

```
Authorization: Bearer {access_token}
```

### Example: Login

```bash
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@example.com","password":"password"}'
```

### Example: Create Ticket

```bash
curl -X POST http://localhost:8080/api/v1/tickets \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"title":"Cannot login","description":"Getting 500 error","priority":"high"}'
```

## Roles & Permissions

| Role     | Capabilities                                              |
|----------|-----------------------------------------------------------|
| Admin    | Full access to users, all tickets, dashboard              |
| Agent    | View/update assigned tickets, comments, attachments       |
| Customer | Manage own tickets, comments, attachments                 |

## Running Tests

```bash
docker compose run --rm app php vendor/bin/phpunit
```

Or with a one-off PHP container:

```bash
docker run --rm -v "$(pwd)":/app -w /app php:8.2-cli \
  bash -c "apt-get update -qq && apt-get install -y -qq git unzip libzip-dev sqlite3 \
  && docker-php-ext-install pdo_sqlite zip \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && composer install --no-interaction \
  && php vendor/bin/phpunit"
```

## Local Development (without Docker)

Requires PHP 8.2+, Composer, MySQL, Redis.

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
php artisan queue:work redis
```

## Environment Variables

| Variable         | Description              | Default          |
|------------------|--------------------------|------------------|
| DB_*             | MySQL connection         | see .env.example |
| REDIS_*          | Redis connection         | see .env.example |
| CACHE_STORE      | Cache driver             | redis            |
| QUEUE_CONNECTION | Queue driver             | redis            |
| JWT_SECRET       | JWT signing key          | generated        |
| JWT_TTL          | Token TTL (minutes)      | 60               |

## Project Structure

```
app/
├── Contracts/Repositories/   # Repository interfaces
├── Enums/                    # Ticket status, priority, roles
├── Http/
│   ├── Controllers/Api/      # API controllers
│   ├── Middleware/           # Custom middleware
│   ├── Requests/             # Form request validation
│   └── Resources/            # API resources
├── Jobs/                     # Queue jobs
├── Models/                   # Eloquent models
├── Policies/                 # Authorization policies
├── Repositories/             # Repository implementations
└── Services/                 # Business logic services
```

## License

MIT
