# Kerala Founders — Project Notes for Claude Code

Personal side project (unrelated to any employer) — a PHP/MySQL directory of
Kerala-origin founders/companies across the EU. Live at
https://keralafounders.eu/. This file is auto-loaded at the start of every
Claude Code session on this repo, so keep it accurate and lean — deeper
history goes in `HISTORY.md`, pending work in `OPEN_ITEMS.md`.

## public/assets/logo.svg is off-limits

The `logo.svg` committed in this repo is a stale placeholder and does **not**
match the real logo. The correct logo is maintained by hand, directly on the
production server, via cPanel File Manager — it is not managed through git.

**Do not:**
- Edit, replace, or "fix" `public/assets/logo.svg` in this repo.
- Remove the logo backup/restore steps in `.cpanel.yml` or
  `.github/workflows/deploy-cpanel.yml`. Every deploy backs up whatever logo
  file is currently live on the server, copies `public/*` over, then restores
  that backup — so the live logo survives every deploy untouched, regardless
  of what's committed to git.
- "Sync" the repo's `logo.svg` to match production, or vice versa, without
  being explicitly asked.

If a real logo change is ever wanted, it must be a deliberate, explicit
request from the user — and even then, confirm whether they want it done via
a manual server-side replacement (consistent with how the current logo is
maintained) or by updating the repo and removing the backup/restore exemption.

## Explain decisions in plain, non-engineer terms

The user is a product manager and ex-designer, not a software engineer.
Whenever a response reaches a point where the user has to decide something
(which approach to take, whether to keep or revert something, which option
to pick), automatically include a short "Pros / Cons" in plain language —
no jargon, no assuming familiarity with engineering tradeoffs. Explain what
each option means for the product, the user, or the business, not just the
technical mechanics. Keep it brief (a few bullets per side), and always end
with a plain-language recommendation, not just a neutral list.

## Tech stack & hard constraints

- **PHP 7.4-compatible syntax required** in everything under `public/` —
  hosting constraint, not a style preference.
- MySQL/MariaDB. `schema.sql` is the full current schema, including seed
  data — safe to load fresh, but its `INSERT`s aren't idempotent (unique
  slugs), so don't re-run it against an already-seeded database.
- No frontend build step: plain PHP includes + `public/assets/app.js`
  mirrors `public/assets/render-helpers.php` for client-side rendering.
  **Any change to a card/chip/badge helper needs the same edit in both
  files.**
- No package manager, no bundler. Static CSS in `public/assets/style.css`.

## Repo layout

```
public/                  # document root
  partials/              header.php, footer-full.php, footer-minimal.php, head-common.php
  assets/                 style.css, app.js, render-helpers.php, logo.svg (⚠️ off-limits, see above)
  admin*.php              password-protected admin area, not linked from public nav
  index.php, founders.php, countries.php, company.php, claim.php,
  about.php, add-company.php, privacy.php, terms.php, listing-policy.php,
  stories.php, guidance.php
  api/                    submit-company.php, submit-claim.php, admin-update.php,
                          admin-action.php, claim-action.php, admin-outreach.php, report.php
config/                  # OUTSIDE the document root
  db.php, auth.php, report-auth.php   real secrets — gitignored, never committed, must
                          already exist on the server; deploy never touches these
  db.example.php, auth.example.php, report-auth.example.php, reference.php   committed, no secrets
import/                 # historical one-off CSV imports + migration SQL (changelog, not for re-running)
schema.sql               full current schema + seed data
migration-*.sql          historical top-level migrations
TAXONOMY.md              industry/business-type classification reference (frozen v1.1)
.cpanel.yml                        legacy cPanel deploy script (see below)
.github/workflows/deploy-cpanel.yml  current deploy pipeline (see below)
```

## Local dev

**Claude Code on the web**: `.claude/hooks/session-start.sh` (registered via
`.claude/settings.json`) runs automatically on every session — installs and
starts MariaDB, loads `schema.sql` on a fresh DB, generates
`config/db.php` + `config/auth.php` from the `.example.php` templates with
throwaway local dev credentials (gitignored, sandbox-only — see the hook
script if you need the values), and starts `php -S 127.0.0.1:8000 -t
public`. Idempotent — safe on a resumed session.

**Own machine**: `php -S 127.0.0.1:8000 -t public`, needs a local
MySQL/MariaDB and a real `config/db.php` (copy `config/db.example.php`).

## Deployment pipeline

**Current mechanism**: a GitHub Actions workflow
(`.github/workflows/deploy-cpanel.yml`) auto-deploys on every push to
`main` — it SSHes into the cPanel host and runs the same steps `.cpanel.yml`
describes (`git pull`, copy `public/*` into the live docroot, copy
`config/reference.php` into the private config folder, back up/restore
`logo.svg` around the copy). No manual cPanel click needed for a normal
deploy.

`.cpanel.yml` (cPanel's own **Git Version Control** → "Deploy HEAD Commit"
feature) still exists and describes the same steps, but cPanel's Git UAPI
module isn't installed on this host (`Can't locate Cpanel/API/Git.pm`), so
that button doesn't actually work — the GitHub Actions workflow was built
specifically to replace it. Don't assume "Deploy HEAD Commit" in the cPanel
UI does anything; the real trigger is a push to `main`.

**Gotchas**:
1. *(fixed)* Each `.cpanel.yml` task line runs in its own shell — don't rely
   on `export` carrying over between lines; hardcode full paths.
2. *(recurring)* The deploy step only **copies**, never deletes. Files
   removed from the repo linger on the live server until deleted manually
   via File Manager.
3. Apache's `DirectoryIndex` serves `index.html` before `index.php` for a
   bare `/` request. A stale `index.html` in the docroot silently shadows
   `index.php` no matter how many times you redeploy.
4. `logo.svg` is deliberately excluded from ever being overwritten by a
   deploy — see "public/assets/logo.svg is off-limits" above.

## Conventions (follow these)

1. **Deliver only the file(s) that changed**, never a full-repo zip/delivery
   — a full-repo delivery once overwrote the owner's manually-managed real
   `logo.svg` with a placeholder (now also guarded against at the deploy
   level, see above, but keep following this convention anyway). Standing
   instruction from the owner.
2. **Never touch `public/assets/logo.svg`** unless explicitly asked to
   change the logo itself.
3. `render-helpers.php` (PHP) and `app.js`'s equivalent functions (JS) must
   render identical markup — edit both together.
4. Color tokens live in `style.css` `:root`. `--accent` on white/near-white
   gives only ~2.8:1 contrast (fails WCAG AA for text) — use `--accent2`
   (~5.2:1) whenever orange text needs to be accessible.
5. `admin-login.php` and `admin-edit.php` intentionally use a different,
   minimal admin-only header and no footer — exclude these from any
   header/footer/nav redesign work.
6. Spacing/border-radius tokens and the canonical breakpoint set live in
   `STYLE-GUIDE.md` — new or touched CSS should use them; don't mass-migrate
   existing hardcoded values.

## Current feature surface

- Public directory with country/industry/business-type filtering and
  company detail pages; ~120 companies / 151 founders across ~20 EU
  countries as of the last full import.
- Add-company submission flow with admin approve/reject.
- Claim-your-business flow (`claim.php` → `admin-claims.php` field-by-field
  diff with one-click apply-and-verify).
- Admin dashboard: submissions + claims, search/filter, outreach-status
  filter.
- Contact/outreach enrichment tracking with a "mark as emailed" admin UI,
  plus a read-only reporting API (`public/api/report.php`, its own
  `config/report-auth.php`) for outreach/missing-email reports.
- Security: CSRF tokens on all admin state-changing actions, IP-based login
  rate limiting, baseline security headers, hardened session cookies.
- GDPR-style consent banner + Google Consent Mode (default-denied), Google
  Analytics.
- Mobile-responsive nav with a hamburger menu.
- Favicon/social-share image set (using the real logo) and real Privacy
  Policy, Terms, and Listing Policy pages.

## See also

- `OPEN_ITEMS.md` — what's currently pending / needs a status check.
- `HISTORY.md` — chronological build log.
- `TAXONOMY.md` — industry/business-type classification reference; don't
  edit ad hoc, it's governed at named checkpoints.
- `STYLE-GUIDE.md` — CSS spacing/radius tokens and breakpoint conventions.
