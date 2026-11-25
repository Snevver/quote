# CLAUDE.md

Private quote app for https://quote.snev.dev. **Laravel 12 + Vue 3 hybrid SPA**: Laravel handles routing, auth and data; Vue renders every page. `resources/views/app.blade.php` is a thin shell that mounts `resources/js/Pages/<page>.vue` via `data-page` / `data-props`.

## Commands

```bash
composer run dev    # server + queue + logs + vite
npm run build
composer test       # in-memory SQLite
./vendor/bin/pint
```

## Routes (`routes/web.php`)

- Auth: `/inchecken` (login page, password-only, IP whitelist in `settings.inchecken_ip_whitelist` suppresses the "login page visited" mail), `/inchecken/reset-password/*`, `POST /login`, `POST /logout`
- Everything else requires login; guests are redirected to `/inchecken`.
- `/`: the Quotes page (folders + drag-and-drop ordering)
- `/api/quotes`, `/api/quote-folders`: JSON API for the page (all behind login)

## Data

Own MySQL database. Models: `Quote` (bilingual tags/notes, `folder_id`, `position`), `QuoteFolder` (nested via `parent_id`), `User`, `Setting`. `Setting` only holds the login IP whitelist; mail config comes from `.env`.
`php artisan app:import-legacy` is the one-time, read-only copy from the original snev.dev database (`legacy` connection, `LEGACY_DB_*`).

## Notes

- Session cookie `quote_session`, `SESSION_DOMAIN` must stay empty (host-only) so login is separate from the other snev.dev apps.
- Sidebar menu links to the portfolio via `resources/js/config/domains.js`.
- Bilingual UI strings live in `resources/js/locales/en.json` and `nl.json`.
