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

### 8 Oct 2026 · Growth C4: Instagram link-in-bio page with tracking

**State:** Built, pull request open (draft). Not merged, not deployed.

**What I checked in the repo (main at c486a87):**
- The site had no Instagram page and no Instagram link in the footer. The only Instagram links were on the add-company and claim success screens.
- Added a page `/instagram.php`: the 3 newest approved companies, plus links to Stories, Cities and Add your company. Every link carries a tracking tag (`utm_source=instagram`, `utm_medium=social`, `utm_campaign=bio`, and `utm_content` = company, story, city or add_company), so Google Analytics can show which post type sent the visit.
- Added an "Instagram" link to the footer legal row, so it shows on every full-footer page. The existing tracking from A1 already counts clicks on it as `follow_instagram_click`.
- The page is hidden from search (noindex) and not in the sitemap, because it is a landing page for the bio link only.
- Tested on the local test database: page returns 200, 3 company links with tags, company page still loads with the tags added.

**Unverified:**
- What the Instagram bio link points to today (outside the repo).
- That Google Analytics shows the tags in a report (not checked; needs cookies accepted).
- Phone layout (HTML checked only).
- The list is "newest companies", not "latest posts": there is no post list in the repo to read from. If you want specific posts listed, that needs a decision.
- Live server matches the repo.

**The one thing Nijil needs to do:** after deploy, set the Instagram bio link to https://keralafounders.eu/instagram.php, open it on your phone and say if the layout is fine.
