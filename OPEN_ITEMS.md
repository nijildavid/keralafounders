# Kerala Founders — Open Items

Read `CLAUDE.md` first for architecture/conventions, `HISTORY.md` for how
things got here. This file is a living list — update it as items resolve or
new ones come up, don't let it go stale.

## Needs a live-site check

- **Compact-nav alignment fix** — confirmed committed on `main`
  (`c241773`). Live-deployment status (cPanel "Update from Remote" →
  "Deploy HEAD Commit" run, hard-refresh check) hasn't been independently
  verified from a sandbox — network egress to `keralafounders.eu` is
  blocked from Claude Code on the web. Spot-check on the live site when
  convenient.

## Resolved (kept here briefly so it isn't re-litigated)

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

## Reminders

- Personal project, not work for any employer.
- No SSH/CLI on the production host — cPanel File Manager + cPanel Git
  Version Control's web UI only.
- Follow `CLAUDE.md`'s delivery convention: only the file(s) that changed,
  never touch `logo.svg` unless asked.
