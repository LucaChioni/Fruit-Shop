# AGENTS.md

## Stack
- Laravel 13 app with Vue 3 + Inertia pages under `resources/js/Pages`; Vite entrypoint is `resources/js/app.js` from `vite.config.js`.
- `.env.example` defaults to SQLite (`DB_CONNECTION=sqlite`); the README mentions MySQL/Sail, but local config and `database/database.sqlite` point to SQLite unless the user changes env.
- Sail is configured in `compose.yaml` with MySQL, Redis, and Mailpit; use `./vendor/bin/sail ...` variants when working inside Docker.

## Commands
- Full setup: `composer setup` runs Composer install, creates `.env`, generates the key, migrates, installs npm packages with `--ignore-scripts`, then builds assets.
- Dev stack: `composer dev` runs `php artisan serve`, queue listener, Laravel Pail logs, and `npm run dev` concurrently.
- Frontend build: `npm run build`.
- Backend tests: `composer test` clears config first, then runs `php artisan test`.
- Focused backend test: `php artisan test --filter=TestName` or `./vendor/bin/phpunit --filter TestName`.
- PHP formatting is available via `./vendor/bin/pint`; there is no configured npm lint/typecheck/test script.

## App Notes
- Routes live in `routes/web.php`; auth routes are split into `routes/auth.php`.
- Inertia page names must match `resources/js/Pages/**/*.vue` paths because `app.js` resolves `./Pages/${name}.vue`.
- `CartService` owns cart identity: logged-in users get a `user_id` cart, guests get a `guest_cart_token` cookie lasting 30 days.
- Checkout stores `last_order_id` in the session; guest order detail access depends on that session value.
- Admin routes use the `admin` middleware alias registered in `bootstrap/app.php`; admin access is `users.is_admin`.
- `php artisan db:seed` creates `test@example.com` / `password` as an admin user and seeds products.

## Style
- Follow `.editorconfig`: 4-space indentation generally, 2 spaces for YAML except Compose files stay at 4 spaces.
- JS imports can use `@/*` for `resources/js/*` per `jsconfig.json`; Ziggy is aliased to `vendor/tightenco/ziggy`.
