<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Book Sharing Platform

A REST API backend for a simple Book Sharing Platform with two roles: User and Admin.

Users can register, login, share books, and view nearby books (within 10 km). Admin (system-generated) can list users, list books, and delete any book.

### Tech Stack
- Laravel 10, PHP 8.1+
- MySQL
- Laravel Passport (personal access tokens)

### Features
- User: Register, Login, Logout
- Books: Share book, View nearby books (10 km)
- Admin: View all users, View all books, Delete book
- Repository pattern, API Resources, centralized ResponseService, uniform exception handling

### Requirements
- PHP 8.1+, Composer
- MySQL running and accessible

### Setup
1) Clone repository and install dependencies
```
composer install
```

2) Create and configure `.env`
```
cp .env.example .env
```
Edit DB settings:
```
DB_DATABASE=book_sharing
DB_USERNAME=your_user
DB_PASSWORD=your_password
APP_URL=http://localhost
```

3) Generate app key
```
php artisan key:generate
```

4) Migrate and seed
```
php artisan migrate --seed
```
Seeds include: a system Admin user and sample users/books.

5) Install Passport clients/keys (if not already present)
```
php artisan passport:install --force
```
If keys are missing, this command creates `storage/oauth` keys and required clients.

6) Clear caches (recommended)
```
php artisan optimize:clear
```

7) Run the server
```
php artisan serve
```
Base URL: `http://localhost:8000` (or as shown in console)

### Authentication
- Uses Passport; send `Authorization: Bearer {token}` on protected endpoints.
- Admin is seeded (no registration required).

### Seeded Credentials
- Admin: `admin@example.com` / `Admin@123456`
- Users (examples):
  - `john@example.com` / `secret123`
  - `jane@example.com` / `secret123`

### API Endpoints
- Public
  - POST `/api/register`
  - POST `/api/login`
- Authenticated (User)
  - POST `/api/logout`
  - POST `/api/books`
  - GET `/api/books/nearby`
- Admin (Requires admin token)
  - GET `/api/admin/users`
  - GET `/api/admin/books`
  - DELETE `/api/admin/books/{id}`

### Postman Collection
- Import: `postman/Book-Sharing-API.postman_collection.json`
- Variables: `base_url`, `token`, `admin_token`
- Use provided login requests to auto-set tokens:
  - "Login (User - John)" sets `{{token}}`
  - "Login (Admin)" sets `{{admin_token}}`
  - "Logout" revokes current `{{token}}`

### Architecture Notes
- Repositories: `app/Repositories/...` with interfaces and Eloquent implementations
- Service: `app/Services/ResponseService.php` for consistent responses
- Exception Handling: `app/Exceptions/Handler.php` returns JSON for all errors
- Resources: `app/Http/Resources/{UserResource,BookResource}.php`
- Middleware: `Authenticate` returns 401 JSON (no redirect). Admin middleware restricts admin routes.

### Troubleshooting
- Route [login] not defined: fixed by making `Authenticate::redirectTo()` return `null` (API-only).
- Passport key issues: run `php artisan passport:install --force`; ensure `storage/` is writable.
- Token 401: ensure you pass `Authorization: Bearer {token}` and the token isn’t revoked (logout revokes).

### Scripts (cheat sheet)
```
php artisan migrate:fresh --seed
php artisan passport:install --force
php artisan optimize:clear
php artisan serve
```