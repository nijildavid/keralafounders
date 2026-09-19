# Kerala Founders — Build History

Chronological, technical log of what was built. See `CLAUDE.md` for current
architecture/conventions and `OPEN_ITEMS.md` for what's pending.

## 2026-08-24 — Bootstrap

Converted an initial static-HTML/localStorage demo into a PHP/MySQL app:
`companies`/`founders`/`branches` schema, dynamic `assets/data.php`,
submit-company flow, DB-backed admin. Deployed to Namecheap cPanel via
manual zip upload to File Manager (no git yet). Domain: keralafounders.eu.
GitHub repo created.

## 2026-08-24 – 2026-08-26 — Early features

Submission email notifications. Verified/unverified badge system (origin of
the `--accent`/`--accent2` color tokens). Directory pagination.

## 2026-08-25 – 2026-09-13 — Real data import

Hand-researched CSVs imported country by country: Germany, Netherlands,
Belgium, Cyprus, Greece, Czechia, Luxembourg, Denmark, Spain, Austria,
Finland, Portugal, France, Ireland, Sweden, Malta, Poland, Romania,
Slovakia, Slovenia. Grew from 3 seed companies to 120 companies / 151
founders. All imported as `approved` / `verified=0` (real content isn't the
same as personally verified). Contact/outreach enrichment pipeline and an
admin "mark as emailed" UI added alongside this.

## 2026-09-01 – 2026-09-10 — Admin & security

Claim-your-business flow (structured edit form, field-by-field diff and
one-click apply-and-verify in admin). Admin dashboard with
submissions/claims sub-nav, search/filter, outreach-status filter. Security
pass: blocked `error_log` web exposure, CSRF tokens on admin
state-changing actions, IP-based login rate limiting, baseline security
headers, hardened session cookies. Fixed a live 500 caused by a missing
`render-helpers.php` deployment. Full mobile-responsiveness audit +
hamburger menu added. Google Analytics + consent banner with Google
Consent Mode (default-denied).

Taxonomy: Business Type (10 categories) added alongside a frozen
9-industry list; Finance added as a 10th industry with an open "Specific
type" subfield, backfilled from existing description text for 103/120
companies.

## 2026-09-13 — Deploy pipeline + visual redesign

Built the cPanel Git Version Control deploy pipeline: `.cpanel.yml`,
`.gitignore`, README, config split. Debugged the "each `.cpanel.yml` task
line is its own shell" bug and an SSH-deploy-key dead end; resolved by
making the repo public and cloning over plain HTTPS. Renamed the last
`*.html` pages to `*.php` with 301 redirects for the old URLs.

Homepage polish round shipped: bigger logo, removed the mint hero
fallback color, removed "About" from the top nav, hero-visual
bottom-spacing fix, nav-card vertical restructure, pill
`white-space:nowrap`, nav-card description `line-height` bump.

Root-caused "site still shows old design after a successful deploy" to a
stale `index.html` shadowing `index.php` via Apache's `DirectoryIndex`
priority — fixed at the repo level by `git rm`-ing 7 stale `.html`
duplicates (not just deleting them on the server, which didn't stick since
the deploy step re-copies whatever's still tracked).

## 2026-09-14 — Bug fixes + the logo convention

Fixed hero text/image overlap at ~900–1148px viewport widths. Changed the
"Verified" tag chip to `--accent2` for WCAG AA contrast.

The owner's real `logo.svg` was never successfully transferred into any
sandbox (multiple paste/upload attempts failed). Root cause of "the logo
keeps getting messed up" turned out to be unrelated: full-repo deliveries
were silently overwriting the owner's manually-restored real file every
time. Resolved via a standing instruction — deliver only changed files,
never touch `logo.svg` — now documented in `CLAUDE.md`.

## 2026-09-15 — Compact-nav fix

Fixed compact-nav vertical misalignment: the icon spacer's `height` wasn't
zeroed alongside its `width` in the compact-mode CSS overrides. Committed
to `main`; live-deployment status should be spot-checked (see
`OPEN_ITEMS.md`).

## 2026-09-17 — Cloud dev environment

Added a Claude Code on the web SessionStart hook
(`.claude/hooks/session-start.sh` + `.claude/settings.json`) that
auto-provisions MariaDB, loads `schema.sql`, generates local-only
`config/db.php`/`config/auth.php`, and starts the PHP dev server on every
new session. Validated end to end, merged to `main`.

## 2026-09-18 – 2026-09-19 — Real Privacy/Terms/Listing pages, reporting API, deploy overhaul

(Work from other Claude sessions on this repo, merged in after the fact —
see the reminder in `OPEN_ITEMS.md` about `main` moving between sessions.)

Added real Privacy Policy, Terms, and Listing Policy pages (previously
placeholders). Added a read-only reporting API (`public/api/report.php`,
its own `config/report-auth.php`) with a `missing_email` report, plus an
email-enrichment migration from a separate research pass. Added a proper
favicon/social-share image set built from the real logo, and fixed
duplicate `<title>`s on directory pages.

Deploy pipeline overhaul: discovered cPanel's own "Deploy HEAD Commit"
button doesn't actually work on this host (`Can't locate
Cpanel/API/Git.pm` — the Git UAPI module isn't installed), so a GitHub
Actions workflow (`.github/workflows/deploy-cpanel.yml`) was added to
auto-deploy over SSH on every push to `main`, replacing the manual
cPanel-click step. The logo problem got a real infrastructure-level fix
at the same time: every deploy (both `.cpanel.yml` and the Actions
workflow) now backs up whatever `logo.svg` is currently live on the
server, copies `public/*` over, then restores that backup — so the real
logo survives every deploy no matter what's committed to git. A repo
`CLAUDE.md` documenting this, plus a standing instruction to explain
decisions in plain, non-engineer terms, was added directly by the owner
working with another session — merged with this session's own
`CLAUDE.md`/`HISTORY.md`/`OPEN_ITEMS.md` effort on 2026-09-19.

## Open threads

See `OPEN_ITEMS.md`.
