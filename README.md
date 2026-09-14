# Kerala Founders

PHP + MySQL directory of Kerala-origin founders and companies building across
the EU. Live at https://keralafounders.eu/

## Structure

- `public/` — the website (this is the document root — everything a browser
  can reach lives here).
- `public/partials/` — shared header/footer, included by every page via PHP
  `include`. Change the nav or footer once here instead of editing every page.
- `config/` — `db.php` and `auth.php` hold real credentials and are
  gitignored. Copy `db.example.php` → `db.php` and `auth.example.php` →
  `auth.php`, fill in real values, and keep `config/` as a sibling folder
  *outside* the web-facing document root (never inside `public_html` or the
  domain's docroot) — `public/*.php` reaches it via
  `require __DIR__ . '/../config/db.php'`.
- `schema.sql` — the full current schema (companies/founders/branches +
  taxonomy columns). Run once via phpMyAdmin or the `mysql` CLI on a fresh
  database.
- `import/` and the top-level `migration-*.sql` files — historical one-off
  migrations, kept as a changelog. Not meant to be re-run blindly against a
  database already provisioned from `schema.sql`.
- `TAXONOMY.md` — the industry/business-type classification reference.

## Local development

```
php -S 127.0.0.1:8000 -t public
```

Requires a local MySQL/MariaDB instance and `config/db.php` pointed at it. See
also the XAMPP local-preview package delivered separately, which sets this up
end-to-end with a data export.

## Deploying to production

This repo is meant to be connected via cPanel's **Git Version Control**
feature (Namecheap/most cPanel hosts have it under the "Files" section):

1. In cPanel, go to Git Version Control → Create, and paste this repo's
   clone URL. Set the repository path to something *outside* your document
   root, e.g. `repositories/keralafounders` (not `public_html` directly).
2. After the initial clone, click "Manage" on the repository, then
   **"Deploy HEAD Commit"**. This runs `.cpanel.yml`, which copies
   `public/*` into the live document root and `config/reference.php` into
   the private config folder — see `.cpanel.yml` for exactly what it does
   and which paths to double-check for your account.
3. For every future change: commit and push to this repo, then click
   "Deploy HEAD Commit" again in cPanel. No manual file uploads.

`config/db.php` and `config/auth.php` are never touched by deployment — they
stay as whatever's already on the server from the original manual setup.

## Admin

`/admin.php` — password-protected (see `config/auth.php`), not linked from
public navigation. Lists pending company submissions with approve/delete
actions, plus `/admin-claims.php` for correction/claim requests.
