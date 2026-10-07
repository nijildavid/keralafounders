# Engineer reply log

The real queue file (claude/engineer-tickets-queue.md) is not in this repo, so replies go here until Nijil says where they belong. Copy them into the queue file's Reply log if wanted.

## 7 Oct 2026, card A3: follow and share asks after a claim or submission

**State:** Done in code, draft PR #74 open. Not merged, not deployed.

**What I checked in the repo:** The "Add your company" page ended on a Thank-you screen with no follow or share. The claim page showed only a banner that vanishes after 10 seconds. The tracking added in PR #70 already counts Instagram follow clicks on its own. PHP syntax is clean and both pages render locally.

**What I changed:** Both success screens now show Follow @keralafounders.eu on Instagram, Share on WhatsApp (ready-made text with the site link) and Copy link. WhatsApp and Copy link send a share_click event, only after cookie consent.

**Not verified:** A real submit (the bot check blocks it locally), a phone, real WhatsApp, and the events in Google Analytics real-time. The shared link points to the homepage, because a new listing has no page until approved. The queue file was not available, so scope came from the board card.

**What Nijil needs to do:** After the PR is merged and deployed, submit one test company, check the three buttons appear and work, and tell me if the WhatsApp text wording is right.
