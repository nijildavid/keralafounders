# Engineer tickets queue

This file did not exist in the repo when the hourly run started on 7 Oct 2026. It holds only the Reply log. The ticket order and done-when lines still live on the board.

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

### 7 Oct 2026 · Growth A4 part 2: story links and the branded image

**State:** Checked, notes only. No code change, because the open point is a decision for Nijil.

**What I checked in the repo (main at 888a7a2):**
- There is no page for an individual story. There is one Stories page (`stories.php`) with the Spotify player embedded. So "story links" today means that one page.
- The Stories page already has a title, a description and the site-wide preview image (1200x630), through the shared preview block. Pasted into WhatsApp or LinkedIn it should show those. It is in the sitemap.
- Company, city and country previews were handled in PR #73 (merged). Every page still uses the same single site image.

**Unverified:**
- What WhatsApp and LinkedIn actually show for any link. I cannot open them from here.
- Whether the live server matches the repo.

**Decision needed (not built):** a branded image per company.
- Pros: a link to one company looks like that company (name, city), so it is more likely to be tapped; it fits the share kit images in PR #75.
- Cons: more to maintain; the images need a bold font on the live server (unchecked); the look has not been approved.
- Recommendation: wait. Finish PR #75 first, approve its look, then reuse the same square image for previews. Until then the one site image is fine.

**The one thing Nijil needs to do:** paste one company link and the Stories link into WhatsApp, look at the preview, and tell me yes or no to a branded image per company.

### 8 Oct 2026 · Growth D1: homepage counters for crawlers and no-script visitors

**State:** Fixed in code, draft pull request open. Not merged, not deployed.

**What I checked in the repo (main at c486a87):** The four homepage numbers (Companies, Founders, Countries, Cities) were printed as `0` in the page itself and filled in only by JavaScript (`app.js`). So search crawlers and visitors without JavaScript saw 0, which matches what the agent fetches saw. That is now confirmed.

**What I changed:** `public/index.php` now prints the real numbers on the server, using the same rules as the script (approved companies, their founders, distinct countries and cities). The script still runs and shows the same figures.

**Tested:** PHP syntax is clean. On a local test database (3 companies) the page source shows 3/3/3/3 without running any script.

**Unverified:** the live site, the live numbers, and what Google shows for the page.

**The one thing Nijil needs to do:** After deploy, open the live homepage source (view-source) and check the four numbers are not 0 and match the page you see.
