# quote

Private quote app on **https://quote.snev.dev**. Laravel 12 + Vue 3, its own database and its own login.

Split out of the snev.dev site (Snevver/website) in November 2026.

## Local setup

```bash
composer run setup   # install, .env, key, migrate, npm build
composer run dev     # server + queue + logs + vite
composer test
```

## Login

`/inchecken` (password-only, admin email from `ADMIN_EMAIL`). Guests are redirected there.
The session cookie is `quote_session` and host-only, so logging in here does not log you into the other snev.dev apps.

## One-time data import from the original snev.dev database

The original database is only read. Use a MySQL user with SELECT-only rights on it.

```bash
# .env: LEGACY_DB_HOST / LEGACY_DB_DATABASE / LEGACY_DB_USERNAME / LEGACY_DB_PASSWORD
php artisan migrate --force
php artisan app:import-legacy --dry-run   # shows row counts, changes nothing
php artisan app:import-legacy             # copies + verifies (row count and content hash per table)
```

It refuses to run if any target table already has rows. Tables: users, settings (IP whitelist only), quote_folders, quotes.
