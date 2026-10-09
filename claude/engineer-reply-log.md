# Engineer reply log

The real queue file (claude/engineer-tickets-queue.md) is not in this repo, so replies go here until Nijil says where they belong. Copy them into the queue file's Reply log if wanted.

## 7 Oct 2026, card A3: follow and share asks after a claim or submission

**State:** Done in code, draft PR #74 open. Not merged, not deployed.

**What I checked in the repo:** The "Add your company" page ended on a Thank-you screen with no follow or share. The claim page showed only a banner that vanishes after 10 seconds. The tracking added in PR #70 already counts Instagram follow clicks on its own. PHP syntax is clean and both pages render locally.

**What I changed:** Both success screens now show Follow @keralafounders.eu on Instagram, Share on WhatsApp (ready-made text with the site link) and Copy link. WhatsApp and Copy link send a share_click event, only after cookie consent.

**Not verified:** A real submit (the bot check blocks it locally), a phone, real WhatsApp, and the events in Google Analytics real-time. The shared link points to the homepage, because a new listing has no page until approved. The queue file was not available, so scope came from the board card.

**What Nijil needs to do:** After the PR is merged and deployed, submit one test company, check the three buttons appear and work, and tell me if the WhatsApp text wording is right.

## 9 Oct 2026, card C2: "Know a Malayali business?" suggestion form

**State:** Built, draft PR open. Not merged, not deployed. The page is unlisted and marked noindex until you say go.

**What I checked in the repo:** No suggestion form existed. The Stories signup (honeypot, hashed-IP rate limit, own database table, admin page) is the closest pattern, so I copied it. PHP syntax is clean. On a local test database: a valid suggestion is stored, an empty one is refused, a bot-trap entry is silently dropped, the 6th attempt in 10 minutes is refused, the admin list needs login, a wrong security token is refused, and Mark handled and Delete work.

**What I changed:** a new page `suggest.php` (business name, city, website or Instagram link, "I own it" tick); a new table `company_suggestions` (SQL file `migration-add-company-suggestions.sql`); a "Suggestions" tab in the admin; a `suggest_business_submit` analytics event after cookie consent. It stores no names, emails or phones of people, only a hashed IP for rate limiting. Suggestions do not go into the Add your company queue. They sit in their own list so half-filled rows never get approved by mistake.

**Not verified:** a phone, the live server, the Analytics event in real time, and whether the privacy page needs a sentence for this form (no legal text added; the form only tells visitors not to add personal details). No bot check widget (Turnstile) on this form, only the honeypot and rate limit.

**What Nijil needs to do:** say yes or no to the form going live. If yes: run the SQL file in phpMyAdmin first, merge, then tell me where to link it (footer, Instagram bio page) and whether a lawyer should add a privacy sentence.
