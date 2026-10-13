# Open questions: Sweden (agent draft, agent review, not legal advice)

## Judgment calls the agent made
1. **Fees taken from verksamt.se, not Bolagsverket.** Bolagsverket pages showed a bot check and could not be opened. verksamt.se is the official one-stop portal run with Bolagsverket and gives "from SEK 2,400" (AB), SEK 250 (beneficial owner), "from SEK 1,800" (name protection). A secondary search result quoted other sole-trader amounts (SEK 2,100 paper / 1,800 e-service, and a blog said 1,200); not used.
2. **No processing time stated.** Quick fact `time` says no official time could be confirmed. A search summary suggested 7 working days for a new AB (snapshot 5 Oct 2026); held, not rendered.
3. **One country-specific section**, "F-skatt, ID numbers and tax-debt certificates", chosen because the tax-clearance certificate rule (countries lived in during the last two years, max three months old) is the most likely surprise for founders arriving from India.
4. **Visa section is fully drafted but held.** It uses the egen-företagare (self-employed) route only; no separate start-up visa was found on Migrationsverket pages. Private "startup visa" sellers exist; not treated as official.
5. **next_check_due 2027-01-15**, since base amounts, egenavgifter and fees are annual.
6. **Handelsbolag** is in the table but its capital and liability cells say "not stated on the pages read" rather than from memory.

## Conflicts between sources
- Board composition: verksamt.se says "at least one board member and one deputy". The statute (ABL 8 kap.) was only grepped for the residency paragraphs; the deputy rule is generally that a deputy is required when there is a single member. A professional should confirm the exact wording.
- Skatteverket's 2025 report (search snippet) dated the VAT limit rise differently from other sources; the Skatteverket page opened states SEK 120,000 and is used.
- Egenavgifter component split differs between commercial sites (Bokio vs Spiris); only the 28.97% total from Skatteverket is used.

## What a local professional must confirm
- Whether a non-EEA founder can realistically hold a 100% AB (board EEA rule, exemption practice at Bolagsverket, how long exemptions take).
- Whether a non-EU founder abroad can obtain F-skatt and a verksamt login before the residence permit, and whether a Swedish company registration certificate is needed before the permit application (Migrationsverket's document list asks for one).
- VAT: whether the SEK 120,000 exemption fits a founder who invoices Swedish clients from outside Sweden (seat rules); the page says a non-EU seat cannot use it.
- Corporate/dividend tax beyond the 20.6% rate (e.g. the 3:12 rules for owner-managers) and municipal income tax: not researched.
- E-invoicing: only the public-procurement duty was confirmed. A search summary mentioned an inquiry on wider B2B e-invoicing and EU VAT-in-the-Digital-Age dates; no official page was opened, so nothing is stated.
- Social security for directors of an AB (employer contributions, pension) and A-kassa (unemployment insurance): not researched on official pages.

## Could not verify
- Bolagsverket processing times and fee page (bot check).
- Tightened conduct (vandel) requirements from 13 July 2026 (search snippet only).
- Whether Arbetsförmedlingen / Tillväxtverket offer a start-up grant or advice relevant to non-resident founders (not opened).
- Skatteverket "bedriva näringsverksamhet" definition page (only referenced from the F-skatt page).
- Whether swedish F-skatt for the sole trader requires physical presence: the F-skatt page says business "på plats i Sverige"; a search summary said no permanent establishment is required. Kept only the official wording.
- The fetch tool summarises pages; an independent agent must re-open each figure (Standard QA 4).

## Decisions for Nijil
See the pull request body (top 5).

## Added by the independent check (2026-10-13)
- Bolagsverket pages (fees, processing times) were still not opened by the checker; only verksamt.se figures are confirmed. Someone with a normal browser should open Bolagsverket's fee and current-processing-time pages once.
- verksamt.se is a JavaScript page and Skatteverket blocked curl from the sandbox, so those rows were read through a summarising fetch tool, not raw HTML. A human should glance at the SEK 2,400 / 250 / 1,800 fees and the SEK 120,000 VAT limit in a browser before publishing.
- Board rule: verksamt.se says "at least one member and one deputy"; the Companies Act (8 kap. 1 and 3 §§) needs a deputy only when the board has fewer than three members. The guide now states both. A professional should confirm which wording to lead with.
- Migrationsverket's news box mentions stricter conduct (vandel) rules from 13 July (2026); details not read. Part of the held visa section.
- Handelsbolag capital and partner liability remain "not stated on the pages read"; nothing was added from memory.
