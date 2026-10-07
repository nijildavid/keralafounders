# Engineer tickets queue

This file did not exist on main when the hourly run started on 7 Oct 2026.
This version holds only the Reply log. The ticket order and done-when lines
still live on the board. (PR #76 adds the same file with the C1 entry; if both
merge, keep both entries.)

## Reply log

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
