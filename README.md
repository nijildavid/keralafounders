# Kerala Founders

PHP + MySQL directory of Kerala-connected founders and businesses across the EU. Live at https://keralafounders.eu/

## Structure

- `public/` — the website. Upload the *contents* of this folder into the `keralafounders.eu` document root on the server (not the `public/` folder itself).
- `config/` — `db.php` and `auth.php` hold real credentials and are gitignored (never commit them). Copy `db.example.php` → `db.php` and `auth.example.php` → `auth.php`, fill in real values, and upload `config/` as a **sibling folder outside** the web-facing document root — never inside it, since it holds the DB password and admin password hash.
- `schema.sql` — run once (via phpMyAdmin or `mysql` CLI) to create the database tables.
- `import/` — one-off SQL migrations (each additive, safe to re-run against a fresh DB) documenting how the directory's data was built up batch by batch, plus two small local-only CLI tools (`check-duplicates.php`, `generate-contact-update.php`) — never deployed to the server.

## Local development

```
php -S 127.0.0.1:8000 -t public
```

Requires a local MySQL/MariaDB instance, with `config/db.php` and `config/auth.php` set up as above (`config/` sits next to `public/`, not inside it — matches production).

## Admin

- `/admin.php` — all submissions (approve, edit, delete, mark verified/emailed)
- `/admin-dashboard.php` — overview stats, landing page after login
- `/admin-claims.php` — listing claim/correction requests from business owners, with one-click apply
- Password-protected (`config/auth.php`), not linked from public navigation
- Includes CSRF protection on all state-changing actions and IP-based login rate limiting (5 attempts / 5 min lockout)

## Public flow

- `add-company.html` — self-service company submission (goes to "pending" for review)
- `claim.php?id=<slug>` — existing listings can be claimed/corrected by their owner; submissions show as a field-by-field diff in the admin claims queue
- Deploying to production is currently manual (no SSH access on the host) — upload changed files individually via cPanel File Manager. Zip-then-extract uploads have previously mangled hyphenated filenames on this host, so prefer individual file uploads for anything under `public/`.

## Security notes

- `config/` must never be reachable via a URL — verify it sits outside the domain's document root
- PHP's `error_log` is blocked from direct HTTP access via `.htaccess` — if you ever see it appear in a `Require all denied` warning in your host's logs, that's expected and correct
- The admin password hash lives only in `config/auth.php`; regenerate it with:
  ```
  php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
  ```
