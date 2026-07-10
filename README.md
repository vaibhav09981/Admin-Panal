# Admin Panel & Backend Demo (PHP)

A small, dependency-free PHP backend + admin panel built to demonstrate how a
backend works (routing, controllers, database layer, and authentication).

## Requirements
- PHP 8.1+ (this was built on PHP 8.3)
- SQLite support (`pdo_sqlite`) — enabled in the bundled `php.ini`

## Run it
From this folder:

```bash
php -S localhost:8000 public/index.php
```

Then open http://localhost:8000

Seeded admin login:
- Email: `admin@example.com`
- Password: `admin123`

## How it is structured
```
public/index.php        <- Router (entry point). Maps every request to a controller.
src/bootstrap.php       <- Autoloader (PSR-style class -> file mapping).
src/Database.php        <- Data layer. Opens SQLite, creates tables, seeds data.
src/Auth.php            <- Authentication. Sessions, login, logout, route protection.
src/controllers/        <- Business logic. One controller per resource.
    UserController.php
    ItemController.php
    DashboardController.php
views/                  <- Server-rendered HTML (the admin UI).
```

## Two ways the backend is exposed
1. **HTML pages** (what you click through in the browser): `/dashboard`, `/users`, `/items`.
2. **JSON API** (what a frontend/app would call): `/api/users`, `/api/items`, `/api/stats`, `/api/login`.

Both use the *same* controllers, so the API and the UI never drift apart.

## API quick test (curl)
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}' -c cookies.txt

curl http://localhost:8000/api/users -b cookies.txt
curl http://localhost:8000/api/stats -b cookies.txt
```

## What to show during the demo
See `docs/SHOW-AND-TELL.md`.
