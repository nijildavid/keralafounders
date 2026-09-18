# Notes for Claude Code

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
