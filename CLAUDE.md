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

- Auth: `/inchecken` (login page, password-only, no notification mail), `/inchecken/reset-password/*`, `POST /login`, `POST /logout`
- Everything else requires login; guests are redirected to `/inchecken`.
- `/`: the Quotes page (folders + drag-and-drop ordering)
- `/api/quotes`, `/api/quote-folders`: JSON API for the page (all behind login)

## Data

Own MySQL database. Models: `Quote` (bilingual tags/notes, `folder_id`, `position`), `QuoteFolder` (nested via `parent_id`), `User`. Mail (password reset only) comes from `.env`.
`php artisan app:import-legacy` is the one-time, read-only copy from the original snev.dev database (`legacy` connection, `LEGACY_DB_*`).

## Notes

- Session cookie `quote_session`, `SESSION_DOMAIN` must stay empty (host-only) so login is separate from the other snev.dev apps.
- Layout (`resources/js/Layouts/AppLayout.vue`) is a minimal header: page title slot, language switcher, logout. No links to the portfolio.
- Bilingual UI strings live in `resources/js/locales/en.json` and `nl.json`.
