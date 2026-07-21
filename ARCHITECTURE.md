# Planora — Architecture & Tech Stack

A plain-language map of every technology in Planora, how they fit together, and what
each important file/folder is for.

---

## 1. The mental model: two worlds + a bridge

Planora is split into two "worlds", connected by one bridge:

```
        BACKEND (runs on the server, in PHP)              FRONTEND (runs in the browser, in JS)
        ┌───────────────────────────────────┐            ┌───────────────────────────────────┐
        │  PHP + Laravel                     │            │  Vue 3 components (.vue)           │
        │  • routes, controllers, models     │  Inertia   │  • written in TypeScript          │
        │  • talks to the MySQL database     │ ─────────► │  • styled with Tailwind CSS       │
        │  • decides WHAT data a page needs  │   (bridge) │  • bundled by Vite                │
        └───────────────────────────────────┘            └───────────────────────────────────┘
```

- **Backend** decides *what* to show and fetches it from the database.
- **Frontend** decides *how* it looks and feels in the browser.
- **Inertia** is the bridge: the backend hands data straight to a Vue page — no separate
  API to build.

---

## 2. Every piece and its job

| Technology | World | What it is / does |
|---|---|---|
| **XAMPP** | environment | A bundle that installs three things at once: the **Apache** web server, the **MySQL** database, and the **PHP** runtime. |
| **Apache** | environment | The web server. It receives browser requests on a port and runs our PHP. Planora is served on **port 8080**. |
| **PHP 8.2** | backend | The programming language the backend is written in. |
| **Laravel 12** | backend | The PHP **framework** — gives us routing, database tools (Eloquent/migrations), validation, structure. The backbone. |
| **Composer** | backend tooling | PHP's **package manager** (installs Laravel & PHP libraries into `vendor/`). |
| **MySQL** | database | Where all data lives (categories, products, orders…). DB name: `planora`. |
| **Inertia.js** | bridge | Lets Laravel return Vue pages directly with data attached — SPA feel, no hand-built API. |
| **Vue 3** | frontend | The UI framework. Pages/components are `.vue` files. |
| **TypeScript** | frontend | JavaScript **with types** — catches mistakes before they run. |
| **Tailwind CSS** | frontend | Utility-class styling (`flex`, `p-6`, `text-slate-100`) written right in the markup. |
| **Vite** | frontend tooling | The **build tool**: compiles `.vue` + TypeScript + Tailwind into browser-ready files, with hot reload in dev. |
| **Node.js + npm** | frontend tooling | Node runs the JS tooling; **npm** is its **package manager** (installs into `node_modules/`). |
| **Filament 3** | backend (next) | A ready-made **admin panel** for managing data. *Not installed yet.* |
| **Git + GitHub** | tooling | Version control + private cloud backup (`github.com/velizarkosev-web/planora`). |
| **GitHub CLI (`gh`)** | tooling | Drives GitHub from the terminal (we used it to create the repo). |

> Two package managers, on purpose: **Composer** for PHP (`vendor/`), **npm** for
> JavaScript (`node_modules/`). Different worlds, different managers.

---

## 3. What happens when someone visits a page

```
1. Browser requests  http://localhost:8080/
2. Apache hands it to  public/index.php  (Laravel's entry point)
3. Laravel matches the URL in  routes/web.php
4. The route returns  Inertia::render('Welcome', ['appName' => 'Planora'])
5. The HandleInertiaRequests middleware bundles that data
6. resources/views/app.blade.php is sent — an HTML shell containing the page data as JSON
7. resources/js/app.ts boots Vue, finds  Pages/Welcome.vue, and mounts it
8. Vue renders the component; Tailwind styles it → user sees the page
```

Every future page (products, categories, cart) follows this same path.

---

## 4. The two frontend commands

Both are **npm scripts** defined in `package.json`. Type them in a **terminal opened at
the project root** (`C:\Users\User\Projects\planora`) — easiest is VS Code's integrated
terminal (`Terminal → New Terminal`, or `Ctrl + \``).

| Command | Maps to | When | Behaviour |
|---|---|---|---|
| `npm run dev` | `vite` | while actively coding | Starts Vite's dev server with **hot reload**; edits appear instantly. Keep it running; stop with `Ctrl + C`. |
| `npm run build` | `vite build` | before committing / production | Compiles optimized files into `public/build/` once, then exits. |

These only handle the **frontend**. The backend (PHP) is served separately by Apache.

---

## 5. File & folder map

### Backend (PHP — runs on the server)
| Path | What it is |
|---|---|
| `app/` | Your backend code: `Models/` (data), `Http/Controllers/` (request handlers), `Http/Middleware/` (request filters — incl. `HandleInertiaRequests.php`). |
| `routes/web.php` | Maps URLs → what to return. |
| `bootstrap/app.php` | App wiring (we registered the Inertia middleware here). |
| `config/` | Settings for each subsystem (database, mail, etc.). |
| `database/migrations/` | Versioned database schema (each file = one change). |
| `database/seeders/`, `factories/` | Sample/test data generators. |
| `public/` | The web root Apache serves: `index.php` + built assets in `build/`. |
| `tests/` | Automated tests (Pest). |

### Frontend (JS/CSS — runs in the browser)
| Path | What it is |
|---|---|
| `resources/views/app.blade.php` | The single HTML shell Vue mounts into. |
| `resources/js/app.ts` | Boots Vue + Inertia; maps page names → `.vue` files. |
| `resources/js/Pages/` | Your Inertia pages (one `.vue` per screen — e.g. `Welcome.vue`). |
| `resources/css/app.css` | Tailwind entry point. |
| `resources/js/env.d.ts` | Tells TypeScript how to understand `.vue` files. |

### Config & meta (project root)
| File | What it is |
|---|---|
| `.env` | Secrets & environment settings (DB credentials). **Never committed.** |
| `composer.json` / `composer.lock` | PHP dependencies (exact versions in `.lock`). |
| `package.json` / `package-lock.json` | JS dependencies. |
| `vite.config.js` | How Vite builds the frontend (Vue + Tailwind + Laravel plugins). |
| `tsconfig.json` | TypeScript rules. |
| `README.md` | Project intro + setup. |
| `ARCHITECTURE.md` | This file. |

### Auto-generated (you don't edit or commit these)
| Path | Why ignored |
|---|---|
| `vendor/` | PHP packages — rebuilt by `composer install`. |
| `node_modules/` | JS packages — rebuilt by `npm install`. |
| `public/build/` | Compiled frontend — rebuilt by `npm run build`. |

> Rule of thumb: if a folder can be **regenerated by a command**, it's gitignored.
> Your *source* (in `app/`, `resources/`, `routes/`, `database/`) is what gets committed.
