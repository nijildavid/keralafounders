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

### 8 Oct 2026 · Growth C7: do the Add-your-company and Claim forms keep emails hidden?

**State:** Found one real bug and fixed it. Pull request open (draft), not merged or deployed.

**What I checked in the repo (main at c486a87):**
- Company page: a founder's email shows only when "show email" is on for that founder; otherwise it says "Email hidden".
- Claim form: hidden emails and LinkedIn links are never sent to the browser; the form just says "on file". Approving a claim keeps a hidden email hidden. This was fixed earlier (PR #58) and still holds.
- Add-your-company form: the "Show email publicly" switch starts off. The privacy page says a founder's email appears only if the listing allows it.
- **Bug found:** a browser leaves unticked switches out of the form data, so the switches were matched to founders by position. With two founders where only the second ticks "show", the FIRST founder's email would have gone public and the second stayed hidden. Fixed in `add-company.php` and `claim.php`: each founder's own switch is now read directly.

**Unverified:**
- Not tested in a real browser with two founders (PHP syntax check only; the change is one line per form).
- Whether any live listing was already affected: the repo cannot show it. Admin can look at listings with several founders that show an email.
- Whether the live server matches the repo.

**The one thing Nijil needs to do:** after deploy, add a test company with two founders, tick "show" only on the second, and check the company page shows only the second email. Also glance through live listings with several founders to see none shows the wrong email.
