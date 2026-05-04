---
name: digital-store
description: Laravel 12 + Bootstrap 5 + jQuery + Yajra DataTables digital product store. Use this when working in this repo to set up, run, test, lint, or extend the visitor pages or future authenticated/admin features.
---

# digital-store — dev workflow

Laravel 12 storefront for digital products (license keys, Spotify/TikTok/Facebook accounts, streaming subscriptions, gaming).

## Stack (verified versions)
- PHP **8.3** (extensions: mbstring, xml, curl, zip, bcmath, gd, intl, sqlite3, mysql, pgsql)
- Composer 2.x
- Node 22+ / npm
- Laravel **12**
- Bootstrap **5** (compiled from SCSS via Vite)
- jQuery **4** (exposed as `window.$`/`window.jQuery` in `resources/js/app.js`)
- Yajra DataTables **^12** (server-side; frontend bundle: `datatables.net-bs5` + `datatables.net-responsive-bs5`)

## First-time setup
```bash
composer install
cp -n .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed     # 6 categories + 26 sample products
npm install
npm run build                  # or npm run dev for HMR
php artisan serve              # http://127.0.0.1:8000
```

DB defaults to SQLite at `database/database.sqlite`. To switch to MySQL, edit `.env` (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and re-run `php artisan migrate --seed`.

## Test
```bash
php artisan test               # 14 feature tests must pass
```
Feature tests live in `tests/Feature/PublicPagesTest.php` and exercise every public route, the Yajra DataTables JSON endpoint, and the contact form (validation 422 + persistence 200). All tests use `RefreshDatabase`.

## Lint / format
```bash
vendor/bin/pint --test         # check only
vendor/bin/pint                # auto-fix
```

## Project map
- Controllers: `app/Http/Controllers/{Home,Product,Category,Contact,Page}Controller.php`
- Models: `app/Models/{Category,Product,ProductKey,ContactMessage}.php` — slugs auto-generate via the `saving` model event; route binding uses `getRouteKeyName(): 'slug'`.
- Migrations: `database/migrations/2026_05_04_*` (categories, products, product_keys, contact_messages)
- Seeders: `database/seeders/{Category,Product,Database}Seeder.php`
- Views: `resources/views/{layouts,partials,products,categories,pages,home}.blade.php`
- SCSS: `resources/sass/app.scss` — brand color `#6c5ce7`, gradient hero, custom `.product-card` / `.category-card`
- JS entry: `resources/js/app.js` — imports jQuery, Bootstrap, DataTables (bs5 + responsive), exposes `window.$`, sets `X-CSRF-TOKEN` on AJAX

## Yajra DataTables wiring
- AJAX data endpoint: `GET /products/data` → `ProductController@data`
- The eloquent query selects only the columns needed and eager-loads `category:id,name,slug,icon`
- Custom columns added: `category_name`, `price_formatted`, `original_price_formatted`, `discount_percent`, `detail_url`
- `category_name` is made searchable via `filterColumn(... whereHas ...)` and orderable via `orderColumn` joining `categories` (don't apply both `joinSub` AND `whereHas` — pick one)
- View-side filters (`category` slug, `price_min`, `price_max`) are passed as query params and consumed in `data()`
- Inline DataTable init scripts in Blade views MUST be wrapped in `document.addEventListener('DOMContentLoaded', ...)` because the Vite module is deferred — without that, `$` is undefined when the inline script runs.

## Contact form (jQuery AJAX)
- Endpoint: `POST /contact` (returns JSON; 422 with `errors` map on validation fail)
- View: `resources/views/pages/contact.blade.php`
- Server validation in `app/Http/Requests/ContactRequest.php`
- Persists to `contact_messages` with `ip_address`

## Phase 2 (not yet implemented)
Registration, email verification, cart, checkout, payment, order history, automatic license-key delivery, admin panel. Auth-related buttons in the navbar and on product pages are intentionally disabled placeholders.

## Common pitfalls
- If DataTables shows no rows: open devtools, you probably forgot the `DOMContentLoaded` wrapper around the inline `$('#table').DataTable(...)` init.
- `php` not found: install with `sudo apt-get install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-sqlite3 php8.3-zip` (and the rest from First-time setup).
- `composer install` succeeds but Laravel reports missing extension: install the matching `php8.3-<ext>` package.
- Tests failing with `no such table: products`: the test class must `use RefreshDatabase` and seed via the seeders in the test or call `$this->seed(...)`.
