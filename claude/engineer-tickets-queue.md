# Engineer tickets queue

This file did not exist in the repo when the hourly run started on 7 Oct 2026
(it was only referred to by the board). This version holds only the Reply log.
The ticket order and done-when lines still live on the board.

## Reply log

### 7 Oct 2026 · Growth C1: any form still sending data to MailerLite?

**State:** Checked, no form in the code points at MailerLite. Pull request open (notes only, no site change).

**What I checked in the repo (main at 888a7a2):**
- Searched all of `public/`, `config/`, `scripts/`, `import/`, `schema.sql` and the docs for "mailerlite", its script and form addresses, and other newsletter tools (Brevo, Mailchimp, Substack, and similar). Zero hits.
- Every form on the site and where its data goes:
  | Form | Where it goes |
  |---|---|
  | Add your company | our own database, plus an email to us |
  | Claim this listing | our own database, plus an email to us |
  | Contact | an email to us (no database) |
  | "Be the first to know" on the Stories page (the podcast signup) | our own database table `story_signups`; you can see it and download it in the admin under "Story signups" |
  | Guidance thumbs up/down | our own database |
- The only outside script on a form page is Cloudflare Turnstile (bot check) on Add your company. The Spotify player on Stories is an embed, not a form.

**Unverified:**
- Other places that could still point at MailerLite, because they live outside the repo: the bio link on Instagram, Dropbox or Buffer text, any hosted MailerLite form page, and the old podcast page on nijildavid.com. I could not check those.
- Whether the live server's files match the repo.
- Whether the "Notify me" signups reach anyone. They are only stored. Nothing sends emails to them, so they are a list for you to export.

**Found along the way (needs a lawyer's eye, not changed):** The privacy page does not say anything about the Stories "Notify me" email signups: what is stored (email and a hashed IP), why, or how to be removed. No placeholder or legal text was added.

**The one thing Nijil needs to do:** Decide how the Stories signup list will be used now that MailerLite is gone (for example, export the CSV from the admin and send one-by-one from Gmail). Then say yes so the lawyer drafts a privacy-page paragraph for it.
