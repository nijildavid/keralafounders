# Kerala Founders

PHP + MySQL directory of Kerala founders building across the EU. Live at https://keralafounders.eu/

## Structure

- `public/` — the website (upload contents into the `keralafounders.eu` document root on the server)
- `config/` — `db.php` and `auth.php` hold real credentials and are gitignored. Copy `db.example.php` → `db.php` and `auth.example.php` → `auth.php`, fill in real values, and upload `config/` as a sibling folder *outside* the web-facing document root (never inside `public_html` or the domain's docroot).
- `schema.sql` — run once via phpMyAdmin (or `mysql` CLI) to create the `companies`, `founders`, `branches` tables.

## Local development

```
php -S 127.0.0.1:8000 -t public
```

Requires a local MySQL/MariaDB instance and `config/db.php` pointed at it.

## Admin

`/admin.php` — password-protected (see `config/auth.php`), not linked from public navigation. Lists pending company submissions with approve/delete actions.
