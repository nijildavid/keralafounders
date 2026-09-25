# Kerala Founders — Open Items

Read `CLAUDE.md` first for architecture/conventions, `HISTORY.md` for how
things got here. This file is a living list — update it as items resolve or
new ones come up, don't let it go stale.

## In progress — Guidance content expansion (pilot)

Goal: grow search-indexable content for SEO/traffic by filling the mostly-
empty Guidance section (`public/content/guidance/`, see `CLAUDE.md`). Only
Germany is `live` today; 8 countries with real companies in the directory
have zero Guidance presence (Belgium, Greece, Luxembourg, Denmark, Spain,
France, Romania, Slovenia).

Plan (full detail in the plan doc from the planning session, not committed
to this repo — summarized here so it isn't lost):
- **Pilot 3 countries first**: Spain, Belgium, Greece — Spain for traffic
  upside, Belgium as a moderate case, Greece deliberately chosen as a
  harder-to-source test case (per a `deep-thinker` sanity check) rather than
  three easy wins.
- **Publish bar**: a country goes `live` only once every section it
  launches with is fully verified against a live official source (no
  visible "under review" placeholders, unlike Germany's current guide,
  which holds back its visa section). A topic that can't be verified within
  ~2 weeks of dedicated research is simply left out of that country's
  initial launch and added later, rather than shown half-verified or
  blocking the whole page.
- After the pilot: a `deep-thinker` review gate decides whether/how to
  scale to the remaining 5 gap countries (Luxembourg, Denmark, France,
  Romania, Slovenia), followed by a design pass
  (`senior-product-designer`, e.g. hub page at scale, directory→guidance
  internal linking) and a distribution plan (`social-media-manager`) per
  country as it goes live.
- All content research/writing goes through the `european-business-lawyer`
  skill only, which never states a fact not read on a live official source
  in-session — same rule Germany's guide was built under.

**Network access lesson (read this before starting Spain research again):**
a first attempt at Spain's guide hit a session where `WebFetch` was
completely blocked by the environment's network policy (was set to
"Trusted", which only allows package registries/GitHub/cloud SDKs — not
government sites). The `european-business-lawyer` skill correctly fell
back to WebSearch per its own documented fallback and downgraded every
claim to `confidence:"secondary"` instead of `"official"` rather than
pretend it had read the pages directly — but that draft was discarded
rather than kept, since it didn't meet the same bar as Germany's guide.
The environment's Network access has since been changed to **Full** (any
domain) specifically so this doesn't recur — but **that only applies to
new sessions**, not one already running when the setting was saved. If
you're picking this up in a fresh session: first sanity-check `WebFetch`
against an official site (e.g. `sede.agenciatributaria.gob.es`) before
starting real research, to confirm you're not hitting the same wall.

**Design audit already done, fixes already shipped** (see PR below): hub
page scales fine responsively; found and fixed two real bugs — mobile
comparison tables were missing their `data-label` attributes (silently
broken on phones), and `company.php` had no link forward to its country's
guidance page even though `countries.php` already did. Both fixed and
pushed. Remaining lower-priority items (FAQ country-dropdown clutter as
more countries get added with no content yet, flag-emoji cross-platform
accessibility) are not urgent — left for a later pass.

**Tracking**: `claude/tender-cori-j0l875` branch,
[PR #21](https://github.com/nijildavid/keralafounders/pull/21) (draft,
watched for CI/review activity — currently clean/no comments). Push
further commits to this same branch/PR rather than opening a new one.

Status: pilot research not yet started (Spain's first attempt was
discarded per the network-access note above). Belgium and Greece haven't
been started at all yet. Next step: confirm `WebFetch` works, then run
`european-business-lawyer` on Spain, Belgium, Greece in that order.

## Needs a live-site check

- **Compact-nav alignment fix** — confirmed committed on `main`
  (`c241773`), and since GitHub Actions now auto-deploys on every push to
  `main` (added later, see `HISTORY.md`), it's very likely already live —
  any Actions run after it landed would have deployed it along with
  everything else. Still not independently confirmed on the live site —
  network egress to `keralafounders.eu` is blocked from Claude Code on the
  web. Spot-check when convenient, but this is low-risk now.

## Resolved (kept here briefly so it isn't re-litigated)

- **Footer redesign (SEO-friendly multi-column layout)** — implemented,
  `keralafounders#12` (draft, not yet merged/deployed). New Explore /
  Countries / For Businesses / Contact columns; Countries list is now live
  per-country counts + flags (queried directly, not zero-filled, since
  `companies.country` isn't constrained to the static country list); added
  real `business-types.php` and `cities.php` pages to back the new Explore
  links (Cities deliberately doesn't zero-fill — would've been ~140 mostly
  empty cards); `sitemap.php` extended accordingly. Scope was narrowed from
  the original plan doc during review: dropped a people-centric "Founders"
  page and an "Organisations" page (business type currently has 0
  companies) rather than ship dead/empty pages; Disclaimer in the bottom
  bar links to the existing Listing Policy accuracy/removal section
  (new `#accuracy` anchor) instead of a new page. `footer-minimal.php`
  (admin-only) untouched.
- **"Homepage polish round"** (bigger logo, remove mint hero fallback,
  remove "About" from top nav, hero-visual bottom-spacing fix, nav-card
  restructure, pill `white-space:nowrap`, nav-card description
  `line-height` bump) — all 7 items confirmed present in the current repo.
  Done.
- **Real `logo.svg` never transferring into a sandbox** — not actually the
  bug. Root cause was full-repo deliveries overwriting the owner's
  manually-managed file; fixed by the changed-files-only convention in
  `CLAUDE.md`. Don't restart this investigation unless the logo problem
  resurfaces.

## Explicitly unresolved

- **A ChatGPT-shared plan link**
  (`https://chatgpt.com/s/t_6aa8c4f749ac8191802d45e77282269e`) was posted for
  review but has never been reachable — `chatgpt.com` is blocked by network
  egress policy in every Claude Code on the web sandbox tried so far. Ask
  the owner to paste the plan's text directly instead of the link.
- **Low-priority server clutter** — `__MACOSX/`, `Archive.zip`, and a few
  stray top-level files outside `public/api/`/`public/assets/` on the live
  server. Not shadowing anything live, just untidy. Leave alone unless
  asked for a cleanup pass.

## Database migration checklist (for the next schema change)

`schema.sql` is a snapshot, not a migration tool — schema changes get applied
by hand against the live DB (cPanel/phpMyAdmin) or via a one-off
`migration-*.sql` file (see `CLAUDE.md`'s repo layout). Before doing that
again:

- Never hand-edit `schema.sql` to "match" a change already made live —
  update it as documentation afterward, and only if it would still load
  cleanly against a fresh database (its seed `INSERT`s aren't idempotent).
- Adding a column to a table that already has rows: make it nullable or give
  it a default. A `NOT NULL` column with no default can fail — or lock the
  table — against existing data.
- Keep structural changes (`ALTER TABLE`) and data fixes (bulk `UPDATE`) as
  separate steps, not one script — so a bad backfill doesn't also undo the
  schema change.
- Once a `migration-*.sql` file has actually been run against production,
  treat it as a historical record, not something to edit — write a new one
  instead.
- Sanity-check a migration against something closer to the real data size
  where practical, not just the dev dataset — a query that's instant on 120
  companies can behave differently at scale (a smaller concern at this
  project's current size, but worth remembering as it grows).

## Reminders

- Personal project, not work for any employer.
- No SSH/CLI on the production host from *your* side — cPanel File Manager
  is still the only hands-on access. Deploys themselves now go through
  GitHub Actions over SSH automatically on push to `main` — see
  `CLAUDE.md`'s Deployment pipeline section, this changed from the earlier
  manual cPanel-click flow.
- Follow `CLAUDE.md`'s delivery convention: only the file(s) that changed,
  never touch `logo.svg` unless asked (now also enforced at the deploy
  level via backup/restore, but keep following it anyway).
- Other Claude sessions work on this same repo — `main` can move between
  sessions. Always `git fetch origin main` and diff before assuming your
  branch is still ahead/current.
