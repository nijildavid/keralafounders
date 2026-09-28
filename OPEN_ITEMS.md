# Kerala Founders — Open Items

Read `CLAUDE.md` first for architecture/conventions, `HISTORY.md` for how
things got here. This file is a living list — update it as items resolve or
new ones come up, don't let it go stale.

## Resolved — admin 2FA and nightly DB backups

Code for both landed in `keralafounders#43` (part of a broader security
hardening pass — see that PR for the full list, including the CSP header,
admin session idle timeout, and hidden error output already live via
`.htaccess`/`config/db.example.php`). Both manual server-side steps below
are now done — 2FA is live on the admin login, and
`BACKUP_ENCRYPTION_PASSPHRASE` is set. A manual test run of the backup
workflow was kicked off to confirm it works end-to-end against the live
server; still needs its result checked (a platform outage interrupted that
check — see `keralafounders#43` follow-up conversation).

- **Admin 2FA** (`config/auth.example.php`, `public/admin-login.php`,
  `scripts/generate-2fa-secret.php`): off by default — a fresh
  `config/auth.php` with blank `ADMIN_TOTP_SECRET`/`ADMIN_TOTP_BACKUP_CODE_HASH`
  logs in with just the password, unchanged from before. To turn it on, run
  `php scripts/generate-2fa-secret.php` and paste its two output lines into
  the live `config/auth.php` on the server (cPanel File Manager), then add
  the printed secret to an authenticator app and store the printed one-time
  backup code somewhere safe.
- **Nightly DB backup** (`.github/workflows/backup-database.yml`,
  `scripts/backup-database.php`): the workflow exists and reuses the same
  SSH secrets `deploy-cpanel.yml` already has, but needs one new GitHub repo
  secret, `BACKUP_ENCRYPTION_PASSPHRASE` (Settings → Secrets and variables →
  Actions → New repository secret — any long random string), before its
  first scheduled run. Without it the job will fail on `openssl enc` with an
  empty passphrase. Restore: download the artifact from a workflow run,
  then `openssl enc -d -aes-256-cbc -pbkdf2 -pass pass:<passphrase> -in
  backup.sql.gz.enc | gunzip | mysql -u <user> -p <db>`. Repo is public, so
  the dump is encrypted before it ever leaves the server — the passphrase is
  the only thing standing between the artifact and the founder/claimant
  emails it contains; treat it like any other production credential.

## Pending — Cloudflare Turnstile on add-company needs a server-side secret key

The public "add your company" form previously only had a honeypot field and
IP-based rate limiting against spam/bot submissions — no real challenge.
Added a Cloudflare Turnstile widget (`public/add-company.php`,
`public/assets/validation-helpers.php`'s `turnstile_verify()`,
`public/api/submit-company.php`, CSP additions in `.htaccess`) — the widget
appears near the submit button (mostly invisible in "Managed" mode) and the
token it produces is verified server-side before a submission is accepted.

Off by default — same "safe to deploy before configured" pattern as 2FA and
backups: without `config/turnstile.php`, `turnstile_verify()` always returns
true and the form works exactly as before (honeypot + rate limit only). To
turn it on: copy `config/turnstile.example.php` to `config/turnstile.php` on
the server (cPanel File Manager) and set `TURNSTILE_SECRET_KEY` to the
Secret Key from the Cloudflare Turnstile dashboard (the Site Key is public
and already deploys automatically via `config/reference.php`).

Not yet tested end-to-end (syntax check + a live round-trip against
Cloudflare's siteverify API + a dev-server submission test) — a platform
outage blocked command execution while this was being verified. Needs that
follow-up before trusting it fully in production.

## Resolved — Guidance presentation layer

After all 6 guides went live, a `deep-thinker`/`day-to-day-thinker` gut-check
asked the real question: would a founder actually read 2,000+ words of
citation-heavy legal/tax prose per guide, or is this built mainly to be
crawled? Answer: it's genuinely useful reference material, but the page
asked too much of a first-time visitor before showing anything scannable.
A `senior-product-designer` pass (piloted first on Netherlands, reviewed via
an interactive design-preview Artifact before rolling out) added three
things on top of the existing content, without touching any sourced claim,
confidence tag, or citation:

- **Quick facts strip** near the top of each guide — cheapest route, typical
  cost, typical time, biggest gotcha — each one a restatement of a fact
  already cited elsewhere on the same page (same `source_ids`).
- **A plain "your step-by-step checklist"** — one short, human-readable,
  do-this-in-order list distinct from the existing per-section "Checklist"
  lists (which stay as detailed, sourced reference material).
- **Collapsible sections**: dense sections (taxes, legal forms, social
  security) now start closed (native `<details>`), short ones stay open.
  Full text is still in the DOM either way, so this doesn't affect SEO
  indexing — confirmed via `guidance_section_html()` rendering the same
  markup either way, just wrapped.

`quick_facts` and `founder_checklist` are new optional `guide.json` fields,
validated by `scripts/validate-guidance-content.php` (source IDs must
already exist; `section_id` on a checklist item must reference a real
section). Rolled out to all 6 live guides (Germany, Netherlands, Spain,
Belgium, Greece, Slovenia) in one pass. **Status: merged and deployed**
(`keralafounders#32`).

## Resolved — Slovenia Guidance guide

Next after Netherlands per the directory-data-driven priority order: Slovenia
has 5 listed companies, more than Luxembourg (1), France (2) or Romania (1),
though fewer than Netherlands (31). After this, Luxembourg/France/Romania are
thin enough to batch together rather than treat individually; Denmark
(0 companies) stays parked until it has at least one.

**Status: `live`.** Written, sourced, independently audited, and fixed —
27 sources, 24 of them
official/chamber/government-portal (SPOT, FURS, ZPIZ, ZZZS, AJPES, the
Employment Service, Uradni list RS — Slovenia's Official Gazette), 3
secondary (flagged inline, not dressed up as official). Includes a
Slovenia-specific section on the "normiranec" flat-rate tax regime and
"popoldanski s.p." (running a sole-trader business part-time alongside a
job) — the local analogue to Netherlands' false-self-employment section.

An independent `european-business-lawyer` audit re-fetched all 27 sources
(full coverage, zero dead links) and **resolved** the VAT-threshold conflict
the original research flagged: FURS's EUR 60,000/66,000 figure is correct
and current (independently corroborated via the EU Commission's own
Slovenia SME-VAT page); SPOT's English page, still showing EUR 50,000, is
simply stale. The audit found and this session fixed two narrower
overclaims — a PIZ new-founder relief detail and a normiranec eligibility
test that didn't quite match their cited sources' actual wording — both now
corrected or downgraded to secondary confidence.

Still worth a look:

- The residence-permit section stays fully held (`hold: true`) — it rests
  on one SPOT overview page rather than a dedicated immigration-authority
  source, and the audit surfaced a further discrepancy for the eventual
  immigration-lawyer review: a second official-adjacent page states the
  self-employment permit runs for up to 2 years, not the 1-year-renewable
  term the guide's held content currently states. Logged in the guide's own
  `open_questions_for_reviewer` list.
- The popoldanski-s.p. and normiranec figures are all dated 2026 —
  Slovenian contribution bases and thresholds move at least annually, so
  worth confirming they still hold whenever this actually goes live.

## Resolved — Guidance content expansion (pilot)

Goal: grow search-indexable content for SEO/traffic by filling the mostly-
empty Guidance section (`public/content/guidance/`, see `CLAUDE.md`). Only
Germany was `live` before this pilot; 8 countries with real companies in the
directory had zero Guidance presence (Belgium, Greece, Luxembourg, Denmark,
Spain, France, Romania, Slovenia).

Plan (summarized here so it isn't lost):
- **Pilot 3 countries first**: Spain, Belgium, Greece — Spain for traffic
  upside, Belgium as a moderate case, Greece deliberately chosen as a
  harder-to-source test case (per a `deep-thinker` sanity check) rather than
  three easy wins.
- **Publish bar**: a country goes `live` only once you've reviewed it and
  are happy with what it says — the `european-business-lawyer` skill always
  writes new guides to `ready_for_review`, never `live`, so nothing goes
  public without you looking at it first, regardless of how well-sourced it
  is.
- After the pilot: a `deep-thinker` review gate decides whether/how to
  scale to the remaining 5 gap countries (Luxembourg, Denmark, France,
  Romania, Slovenia), followed by a design pass
  (`senior-product-designer`, e.g. hub page at scale, directory→guidance
  internal linking) and a distribution plan (`social-media-manager`) per
  country as it goes live.
- All content research/writing goes through the `european-business-lawyer`
  skill only, which never states a fact not read on a live official source
  in-session — same rule Germany's guide was built under.

**Status: all three pilot guides are `live`** (Spain, then Belgium and
Greece after Nijil reviewed each and chose to ship as-is). Each has an
immigration section held back (`hold: true`) exactly like Germany's. Two
sourcing gaps were deliberately shipped rather than blocked on, since the
guides already state the affected figures at `secondary` confidence rather
than presenting them as settled:

- **Belgium**, Brussels-Capital Region edition (27 sources): several
  Belgian federal sites (tax authority, immigration office, Flanders'
  economic agency) blocked every fetch attempt with bot protection, both
  during initial research and on a same-day retry — confirmed to be a
  standing block, not a one-off. The small-business VAT threshold (€25,000,
  reportedly rising to €30,000 from 2027) is sourced from two independent
  accounting firms and an EU Commission page, never Belgium's own tax
  authority directly. Worth a from-a-browser check against the EU's TEDB
  tool (sme-vat-rules.ec.europa.eu → national threshold lookup) at the next
  6-month check — it's a JS app that automated tools here couldn't render.
- **Greece** (23 sources): aade.gr, gov.gr and mfa.gr returned errors on
  every attempt, both during initial research and on a same-day retry.
  Sourcing leans on chamber/portal mirrors instead of the tax authority
  directly. A genuine conflict between two official government pages on the
  Golden Visa minimum-investment amount (€250k vs. €400k/€800k under a
  newer law) was found and left unresolved in the guide rather than
  silently picked — still worth resolving whenever those sites become
  reachable, since Golden Visa content stays hidden (`hold: true`) until
  then regardless.
- Spain shipped clean — 23 sources, mostly official incl. BOE statute
  text, no comparable sourcing gap.

## Resolved — Netherlands Guidance guide

Picked ahead of the other 5 gap countries once directory data showed why:
Netherlands has **31** listed companies — more than Luxembourg, Denmark,
France, Romania and Slovenia combined (1, 0, 2, 1, 5 respectively) — despite
having sat at `coming_soon` (target month 2026-11) with zero Guidance
content written. Denmark specifically has 0 companies right now, so a guide
there today would be an orphan page with no directory link path; it stays
parked until that changes. Slovenia (5) is the next reasonable single
target after Netherlands; Luxembourg/France/Romania are thin enough to
batch together later rather than treat individually.

**Status: `live`.** Sourcing is unusually clean — 25 sources, all
`official`/`government_portal` (KVK, Belastingdienst, IND, business.gov.nl,
Rijksoverheid), no advisor-blog fallbacks needed at all, a stronger profile
than Germany/Spain/Belgium/Greece. Immigration section held back
(`hold: true`) as usual. Before shipping, an independent
`european-business-lawyer` audit re-fetched all 25 sources itself (not a
sample) and confirmed the two trickiest figures exactly right — the fast-
declining zelfstandigenaftrek (€2,470 → €1,200 → €900 over three years) and
the not-yet-law mandatory disability insurance (BAZ) proposal, correctly
framed as proposed rather than current law. It found and this session fixed
three narrow attribution issues (an unsupported "most businesses need no
permit" framing, an uncited "about 2 weeks" VAT-number turnaround claim,
and a missing source citation on a vof-liability detail) — none were wrong
facts with real sources, just overstatements or gaps, now closed.

Still worth a look whenever the immigration section comes off hold: whether
the standard self-employed residence permit is really the typical route for
KeralaFounders' audience vs. arriving on an employer-sponsored permit
first, and whether any Friendship-Treaty-style shortcut (the US-NL DAFT
arrangement) applies to a nationality relevant here — not researched,
flagged as an open question in the guide rather than guessed at.

## Needs a production DB action (Nijil, via phpMyAdmin)

- **Instagram backfill** — code and data are ready (see `HISTORY.md`
  2026-09-27), but nothing has been run against the live database yet
  (no SSH/CLI access to production from this side). Two SQL files to run
  in phpMyAdmin, in this order:
  1. `migration-add-instagram-provenance.sql` — adds the
     `instagram_source`/`instagram_confidence`/`instagram_note` columns.
  2. `import/migration-instagram-backfill-2026-09-27.sql` — the actual
     backfill (71 high-confidence handles published immediately, 7
     medium-confidence held for review). Its last query lists any slug
     from the research that doesn't exist in the live `companies` table
     under that exact name — check that list is empty (or investigate any
     slug it names) before considering the backfill done.
- **Ayurveda duplicate pair — merge approved, script ready to run** —
  Nijil confirmed `karinkada-ayurveda` and `sonnentag-kerala-ayurveda-shop`
  are the same business and asked to merge them.
  `import/migration-merge-ayurveda-listings-2026-09-27.sql` keeps
  `sonnentag-kerala-ayurveda-shop` (matches the live website's own name)
  and removes `karinkada-ayurveda`, carrying over its founders/branches/
  claim history and filling in any field the surviving row was missing.
  Tested against a local copy with dummy data (including a re-run after
  the fact, to confirm it's a safe no-op the second time) — not run
  against production from this session (no DB/SSH access). Run it any
  time relative to the Instagram backfill above; it sets the Instagram
  handle itself either way. After running: check the script's own
  validation queries, and manually check `admin-edit.php` for the merged
  listing in case the same founder was entered on both original rows
  (the script doesn't try to dedupe founders). `karinkada-ayurveda`'s URL
  will 404 after this — no redirect was built for it (see the script's
  header for why).
- **igcs-indo-german-services / igcs-consultancy — left as two listings**
  — same founder (Saju Jakob) and website (`igcsvisa.de`), but possibly
  two distinct legal entities (GmbH vs Ltd.) rather than one listing
  duplicated, so not merged like the Ayurveda pair above. Revisit if
  that gets clarified.
- **7 medium-confidence Instagram handles need a review decision** —
  `admin.php?instagram=needs_review` lists them with the reviewer's note;
  `admin-edit.php` has a "Publish this handle" checkbox to approve each
  one (or edit/clear the field to reject). Two of the seven
  (`acsia-systems-deutschland`, `beta-group`) are the founder's *personal*
  Instagram rather than a company page — worth a judgment call on whether
  that's appropriate for a business listing before approving.

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
