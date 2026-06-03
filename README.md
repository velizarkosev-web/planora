# Planora

A small, scalable **e-commerce platform** for branded lifestyle & planning products.

The first product category is yearly planning notebooks, but Planora is built to be
**category-agnostic** — stickers, accessories, bundles, and digital products will be added
over time. The long-term goal is a responsive, polished customer storefront (banners,
product galleries, video, marketing sections, smooth checkout flow) backed by an admin
back-office for managing categories, products, pricing, stock, media, homepage content,
and orders.

> Private project. Fully separate from any other codebase or database.

## Tech stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** Inertia.js + Vue 3 + TypeScript, Tailwind CSS, Vite
- **Admin:** Filament 3
- **Database:** MySQL (`planora`)
- **Testing/Quality:** Pest, Laravel Pint, ESLint/Prettier

> Note: the base Laravel app is in place; the frontend stack and admin are being layered
> on. See [`SESSIONS_CONTEXT.md`](./SESSIONS_CONTEXT.md) for current status.

## Domain model

```
Category → Product → Product media → Content sections → Cart → Order → Admin
```

## Requirements

- XAMPP (Apache + MySQL), PHP 8.2
- Composer 2.x, Node.js + npm

## Local setup

1. **Create the database** (once): in phpMyAdmin, create an empty database named `planora`
   (collation `utf8mb4_general_ci`).
2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```
3. **Environment:** copy `.env.example` to `.env`, then:
   ```bash
   php artisan key:generate
   php artisan migrate
   ```
   The `.env` is preconfigured for MySQL (`DB_DATABASE=planora`, user `root`, empty password).
4. **Build front-end assets:**
   ```bash
   npm run dev      # during development (hot reload)
   # or: npm run build   # production build
   ```

## Running the app

Planora is served by **Apache** (XAMPP) via a virtual host on port **8080**, pointing at
the `public/` directory.

- Start **Apache** and **MySQL** in the XAMPP Control Panel.
- Open **http://localhost:8080**

> A backup dev server is available with `php artisan serve` (http://127.0.0.1:8000), but
> Apache on :8080 is the primary, recommended way to run Planora locally.

## Project structure

Standard Laravel layout. Key entry points:

```
app/            Application code (models, controllers, providers)
config/         Framework & app configuration
database/       Migrations, factories, seeders
public/         Web root (Apache DocumentRoot) — index.php
resources/      Views, CSS, JS/TS front-end source
routes/         web.php, console.php
tests/          Pest tests
```

## Status

Foundation complete (Laravel + MySQL + Apache + Git). Frontend stack and admin in progress.
No payments, customer accounts, reviews, or other advanced features yet — by design.
