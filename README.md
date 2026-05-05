# DigitalShop — Digital Store System

A multi-branch admin backend and public storefront for selling digital products (license keys, social/streaming accounts, subscriptions). Built with **Laravel 12**, **Bootstrap 5**, **jQuery + Yajra DataTables**, **React 18** (admin language switcher), and **SQLite** (default; swap to MySQL via `.env`).

- Visitor pages: Home, Products, Categories, Product detail, About, Contact (AJAX), Privacy, Terms.
- Admin backend (`/admin`): Branches, Categories, Products, License Keys, Inventory, Orders (with line items + recalculate + mark-as-paid), Payments, Customers, Dashboard.
- UX: Yajra DataTables (server-side, fixed Bootstrap 5 pagination), SweetAlert2 confirm-delete, PHPFlasher success/error toasts, flatpickr datetime fields, Tom Select dropdowns, live Khmer/English switching with **no page reload**.

## Prerequisites

| Tool        | Minimum version      | Notes                                              |
| ----------- | -------------------- | -------------------------------------------------- |
| PHP         | 8.2 (8.3 recommended)| With `pdo_sqlite`, `mbstring`, `intl`, `gd`, `zip` |
| Composer    | 2.x                  |                                                    |
| Node.js     | 18+ (22 LTS works)   | Used by Vite for the admin React + asset bundle    |
| npm         | 9+                   |                                                    |
| SQLite      | bundled with PHP     | Default DB; no separate server needed              |
| Git         | any modern version   |                                                    |

> Want MySQL instead of SQLite? See [Switching to MySQL](#switching-to-mysql) below.

## Quick start (one-liner)

```bash
git clone https://github.com/khmerkromloy/digital-store.git
cd digital-store
composer setup && php artisan migrate:fresh --seed && php artisan serve
```

`composer setup` is a custom script defined in `composer.json` that:

1. Runs `composer install`
2. Copies `.env.example` → `.env` (if missing)
3. Generates `APP_KEY`
4. Runs `php artisan migrate --force`
5. Installs npm dependencies
6. Builds frontend assets

The extra `migrate:fresh --seed` line resets the DB and seeds demo data (branches, categories, products, license keys, customers, orders, payments, inventory).

Open <http://127.0.0.1:8000> in your browser. The admin area is at <http://127.0.0.1:8000/admin>.

## Step-by-step setup

If you'd rather do it manually (or `composer setup` failed mid-way), here are the equivalent steps:

```bash
# 1. Clone
git clone https://github.com/khmerkromloy/digital-store.git
cd digital-store

# 2. Install PHP dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Database (SQLite is the default — file is auto-created on first migrate)
touch database/database.sqlite
php artisan migrate:fresh --seed

# 5. Frontend
npm install
npm run build           # production build
# or for live HMR while developing:
# npm run dev

# 6. Run the app
php artisan serve       # http://127.0.0.1:8000
```

## Default credentials

The seeder creates two admin accounts and a demo customer:

| Role          | Email                          | Password   |
| ------------- | ------------------------------ | ---------- |
| Super admin   | `admin@digitalstore.local`     | `password` |
| Staff         | `staff@digitalstore.local`     | `password` |
| Demo customer | `demo@digitalstore.local`      | `password` |

**These are local-development credentials only. Change them before deploying anywhere public.**

## Common commands

```bash
# Run the dev stack (server + queue + log tailer + vite) all in parallel
composer dev

# Run the test suite
php artisan test
# or
composer test

# Run static analysis / lint
vendor/bin/pint            # auto-format
vendor/bin/pint --test     # check only, no write

# Re-seed demo data without touching code
php artisan migrate:fresh --seed

# Build production assets
npm run build

# Watch & rebuild assets on change (with HMR)
npm run dev

# List all routes (useful when adding a new admin CRUD)
php artisan route:list

# Tail logs in real time
php artisan pail
```

## Project layout (high-level)

```
app/
├── Http/Controllers/
│   ├── Admin/                  # BaseCrudController + per-entity controllers
│   ├── HomeController.php      # storefront
│   ├── ProductController.php
│   └── ...
├── Models/                     # Branch, Category, Product, ProductKey,
│                               # BranchInventory, Customer, Order, OrderItem,
│                               # Payment, ContactMessage, User
└── Support/
    ├── ColumnRenderer.php      # badge() / money() helpers for DataTable cells
    ├── CrudField.php           # value object describing a form field
    └── Translations.php        # exposes lang/* to the JS i18n bridge

resources/
├── js/
│   ├── app.js                  # global JS bootstrap (jQuery, DataTables, etc.)
│   ├── i18n.js                 # data-i18n DOM walker, locale change events
│   ├── datatables.js           # global Yajra DataTable wiring
│   └── components/             # React 18 (LanguageSwitcher)
├── views/
│   ├── admin/                  # admin layout + per-CRUD index/edit views
│   ├── pages/                  # static pages (about, contact, privacy)
│   └── products/               # storefront product pages

lang/
├── en/admin.php                # English UI strings
└── km/admin.php                # Khmer UI strings (must mirror EN keys)

database/
├── migrations/
│   └── 2026_05_04_000000_create_selling_digital_product_system_tables.php
├── seeders/                    # one seeder per entity
└── database.sqlite             # auto-created
```

## Switching to MySQL

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digitalshop
DB_USERNAME=root
DB_PASSWORD=
```

Then create the database and re-run migrations:

```bash
mysql -uroot -e "CREATE DATABASE digitalshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate:fresh --seed
```

## Live language switching

The admin and storefront support **English** (`en`) and **Khmer** (`km`) without a full page reload:

- React `LanguageSwitcher` POSTs to `/locale/{en|km}` to set a cookie.
- A `i18n:locale-changed` event is dispatched; the DOM walker (`resources/js/i18n.js`) updates every node tagged with `data-i18n="..."`, `data-i18n-attr-{attr}="..."`, or `data-i18n-html="..."`.
- Yajra DataTables re-render their headers and currently-rendered cells in place.

To add a new translatable string, add the key to **both** `lang/en/admin.php` and `lang/km/admin.php`, then reference it in Blade as `{{ __('admin.your.key') }}` (or as `data-i18n="admin.your.key"` in HTML).

## Troubleshooting

| Symptom                                                      | Fix                                                                                  |
| ------------------------------------------------------------ | ------------------------------------------------------------------------------------ |
| `SQLSTATE[HY000]: General error: 1 no such table: …`         | Run `php artisan migrate:fresh --seed`                                               |
| `Vite manifest not found` after pulling new code             | Run `npm install && npm run build`                                                   |
| Login page redirects in a loop                               | Clear browser cookies for `127.0.0.1` and re-login                                   |
| 500 error after editing a Blade file                         | Run `php artisan view:clear`                                                         |
| Khmer text shows boxes/gibberish                             | Ensure your terminal/editor uses UTF-8; the app itself sends Unicode correctly       |
| DataTable shows "Processing…" forever                        | Open DevTools network tab — usually a 500 on the `/data` endpoint; check `storage/logs/laravel.log` |

## License

MIT.
