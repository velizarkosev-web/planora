# Planora

Planora is a **bilingual (Bulgarian / English) full-stack e-commerce platform** — a Vue 3
storefront backed by a Filament administration system for managing products, variants,
pricing, media, translations, and publication workflows. It is built to be
**category-agnostic**, so new product types plug in without rework.

> Built as a real production application, with security, data safety, and performance
> treated as first-class concerns rather than afterthoughts.

## Tech stack

- **Laravel 12** (PHP 8.2) — application backend
- **Vue 3 + TypeScript** — storefront UI
- **Inertia.js** — connects Laravel routes to Vue pages (single codebase, no separate API)
- **Tailwind CSS** + **Vite**
- **Filament 3** — administration panel
- **MySQL**
- **spatie/laravel-translatable** — bg/en content
- **Glide** — on-the-fly image optimization (responsive WebP)
- **Swiper** — interactive product gallery
- **Pest** — automated tests · **Laravel Pint** / ESLint / Prettier — formatting

## Key features (implemented)

- **Bilingual storefront and admin** (Bulgarian primary, English secondary) via JSON translation columns
- **Category & product management** through a custom Filament admin
- **Product variants** using a default-variant pattern (a simple product transparently maps to one hidden variant carrying price/stock/SKU)
- **Scheduled pricing** — timezone-aware sale windows (a sale price activates and expires on set dates, Europe/Sofia)
- **Publication workflow** — draft / published state so unfinished products never reach customers
- **Media synchronisation** — reorderable multi-image upload in admin, kept in sync with an ordered gallery
- **On-the-fly image optimization** — Glide serves responsive, cached WebP (a 2.8 MB source becomes ~47 KB)
- **Interactive product gallery** — Swiper with thumbnails, arrows, swipe, keyboard, and zoom (code-split to the product page)
- **Automated tests** — Pest feature tests covering pricing logic and the variant wiring

## Architecture

Laravel serves as the backend; **Inertia.js** bridges Laravel routes and **Vue 3** pages, so
the app is a single codebase with SPA-smooth navigation and no separate REST API. **MySQL**
handles persistence, **Filament** provides the administrative workflows, and **Glide**
performs image processing at request time.

See [`ARCHITECTURE.md`](./ARCHITECTURE.md) for a fuller map of the stack and folders.

```
Category → Product → Product variants → Product media → (Cart → Order)
```

## Local setup

Requirements: PHP 8.2, Composer 2.x, Node.js + npm, MySQL (XAMPP recommended).

1. Clone the repository.
2. Copy `.env.example` to `.env`.
3. Install dependencies: `composer install` and `npm install`.
4. Generate the app key: `php artisan key:generate`.
5. Create an empty MySQL database named `planora` and configure the `DB_*` values in `.env`.
6. Run migrations and seeders: `php artisan migrate --seed`.
7. Start the app: `npm run dev` (Vite) and serve `public/` via Apache (port 8080) or `php artisan serve`.

Then open **http://localhost:8080**.

## Testing

```bash
php artisan test
```

Tests run against an in-memory SQLite database, so they never touch your development data.

## Current status

Planora is under **active development**. Implemented: the core catalogue, Filament
administration, bilingual content, variant and scheduled pricing, media synchronisation,
image optimization, and the storefront product page with an interactive gallery.

In development: the marketing homepage sections (blog, testimonials, newsletter),
cart and checkout, and commercial integrations (payments). These are intentionally staged
rather than stubbed — the status above reflects what actually works today.
