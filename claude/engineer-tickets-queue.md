# Engineer tickets queue

This file did not exist on main when the hourly run started on 7 Oct 2026. This version holds only the Reply log. The ticket order and done-when lines still live on the board. (PRs #76 and #77 add the same file with other entries; if several merge, keep all entries.)

## Reply log

### 7 Oct 2026 · Growth C3: approve and notify in one step

**State:** Pull request open (draft). Code change in `public/api/admin-action.php`. Nothing deployed, no email sent.

**What I checked in the repo (main at 888a7a2):**
- Approve was already one click in the admin, but sent no email.
- Founder emails from the Add-your-company form are stored in the founders table.
- Only listings that came through the form store an IP hash; imported listings do not. I use that to tell them apart.

**What changed:** When you approve a pending listing that came through the form, each founder with an email gets one "You are live" email from hello@keralafounders.eu: page link, ask to share and follow, and a line saying why they got it and how to be removed. Imported listings never get it. Approving an already-approved listing never re-sends. The email does not mention the share kit (PR #75 is not merged).

**Unverified:** The emailing was not tested at all (no mail on the test machine, only a syntax check). Whether the live server delivers mail, or whether it lands in spam. The email wording has not been read by a lawyer.

**The one thing Nijil needs to do:** Read the email text in the PR, have a lawyer glance at it, and after deploy approve one test submission with your own email as the founder to see it arrive.
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

### 9 Oct 2026 · Growth D2: update claims-tracker.md

**State:** Blocked, nothing changed. Notes only. The file is not in this repo, so I could not update it.

**What I checked in the repo (main at c486a87):**
- Searched the working files and the full git history for `claims-tracker` and for "Known site bugs". No file and no mention, ever.
- The repo only has `claude/engineer-tickets-queue.md` and `claude/engineer-reply-log.md` in the `claude/` folder.
- Why I did not rewrite it from memory: the card says the section is stale on the traffic drop (PR #50) and the cookie banner, but I cannot see what it says today, so any edit would be a guess.

**Unverified:**
- Where the file lives (probably the project folder outside the repo) and what its Known site bugs section says.
- PR #50 is not in the recent history I could see; I did not look up what it changed.

**Also skipped:** Growth C2 (suggestion form). The board says it waits on the privacy text and your yes, so I did not start it.

**The one thing Nijil needs to do:** Put `claims-tracker.md` into the repo (or paste its Known site bugs section into the card), then this takes minutes. Or tell me to close D2 if it no longer matters.
### 7 Oct 2026 · Growth A6: Berlin import, verify after the SQL

**State:** Checked, notes only. The card's old target (4 new pages, Berlin count 10) no longer applies, because the import was cut down to KOKOLAND only before you ran it. Pull request open (notes only, no site change).

**What I checked (repo main at c486a87, and the live site):**
- The import file now adds one record: KOKOLAND (approved, not Verified, no founders, no street address or phone). It deliberately leaves out Berlin Kerala Samajam, the Kerala Cultural Association of Berlin, WMC Berlin and Kombans FC Berlin.
- Live: the KOKOLAND page returns 200, shows "Not yet verified", and is in the sitemap (267 URLs in total).
- Live: the Kombans FC Berlin page returns 404 (hidden, as intended) and is not in the sitemap.
- The page address `company.php?id=kokoland-berlin` is the one the sitemap uses.

**Unverified:**
- The approved Berlin count (expected 7). The directory list is built in the browser, so I could not read the number from the page.
- That no founders, addresses or phones are stored: I only read the SQL file, not the live database.
- Whether the live database really matches the file (I did not touch it).

**Knock-on:** the Instagram post and association asks assumed 10 Berlin companies. With 7 they need the new number (cards G2, G4).

**The one thing Nijil needs to do:** open the Berlin filter on the live directory, confirm it shows 7 companies, then tell me to close this card and fix the Berlin number in the dependent drafts.
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
### 8 Oct 2026 · Growth D1: homepage counters for crawlers and no-script visitors

**State:** Fixed in code, draft pull request open. Not merged, not deployed.

**What I checked in the repo (main at c486a87):** The four homepage numbers (Companies, Founders, Countries, Cities) were printed as `0` in the page itself and filled in only by JavaScript (`app.js`). So search crawlers and visitors without JavaScript saw 0, which matches what the agent fetches saw. That is now confirmed.

**What I changed:** `public/index.php` now prints the real numbers on the server, using the same rules as the script (approved companies, their founders, distinct countries and cities). The script still runs and shows the same figures.

**Tested:** PHP syntax is clean. On a local test database (3 companies) the page source shows 3/3/3/3 without running any script.

**Unverified:** the live site, the live numbers, and what Google shows for the page.

**The one thing Nijil needs to do:** After deploy, open the live homepage source (view-source) and check the four numbers are not 0 and match the page you see.
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
