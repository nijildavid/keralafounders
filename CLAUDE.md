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
