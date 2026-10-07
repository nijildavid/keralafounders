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
