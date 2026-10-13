# Open questions: Austria (agent draft, unreviewed)

Agent draft, agent review, not legal advice. Nothing here was reviewed by a lawyer. No German-law-family consult (skill) was run; that is itself an open item, and Austrian law differs from German law.

## Judgment calls the agent made

1. **Dates kept at 2026-10-12.** The pages were read on 12 Oct 2026; the clock later rolled to 13 Oct, but the brief says 2026-10-12, so that is used everywhere.
2. **next_check_due set to 2027-01-11.** SVS bases, income-tax brackets, the VAT-related thresholds and chamber levies reset on 1 January.
3. **One country-specific section added** (`trade-or-freelance`): trade licence vs freelance, regulated trades, WKO levies, foreign company cannot run a trade. This is the trap most founders hit.
4. **FlexKapG kept out of the capital figures.** The FlexKapGG text read gives no total minimum capital, only EUR 1 per share and a reference to GmbH rules. Commentary says EUR 10,000 but that is secondary, so it is a hold claim.
5. **Foreign-resident GmbH director.** The GmbHG text read sets no residence condition for managing directors (company law). Trade law (GewO section 39) generally requires the trade-law managing director to live in Austria, with exceptions. Both are stated, separated.
6. **Sources written without umlauts** (ae, oe, ue) in titles and quotes, matching how other guides write local terms in plain ASCII. A reviewer may want the original spelling restored.
7. **Visa section fully held** (all 9 claims `hold`), as the Standard requires, even though sources are official (BMI, oesterreich.gv.at).
8. **No GmbH setup-time range stated.** No official range was found. A private advisor PDF says 2 to 4 weeks (secondary, not used).

## Conflicts between sources

- **Trade-law managing director working time:** RIS section 39 GewO says at least half of normal weekly working time (when a qualification proof is required); the ABA page says at least 20 hours per week. The statute is used.
- **WKO sole trader page (AT36):** says the Firmenbuch duty comes at EUR 700,000 and also that bookkeeping starts after exceeding EUR 700,000 twice. USP (AT17) states the two-year rule plus EUR 1,000,000 once. USP is used for accounting.
- **WKO brochure (AT15):** the income tax table prints "401 %" for the 40% bracket; USP (AT16) shows 40%. USP used.
- **SVS 2026 values:** WKO "voraussichtliche Werte 2026" (AT35, dated 31 Aug 2026) calls them provisional, the WKO contributions page (AT20, Stand 1 Jan 2026) presents them as final. Re-check in January 2027.
- **Older sources** (a WKO brochure and advisor PDFs) still show GmbH capital of EUR 35,000 and corporate tax of 25% or 24%. Not used.
- **Permit validity wording:** "up to 24 months" (migration.gv.at start-up page), "exactly 24 months" (key-worker page), "up to two years" (oesterreich.gv.at).

## What a local professional must confirm

- Whether a given IT, consulting, design or content activity is a free trade, a regulated trade or a freelance profession.
- Whether a non-resident can use the simplified online GmbH route (ID Austria, in-person bank ID check) or must use a notary, and whether a notary can sign remotely.
- Real-world time from application to Firmenbuch entry and to a first permit.
- The Firmenbuch court fee for a GmbH under the current Gerichtsgebuehrengesetz (the EUR 450 is a WKO estimate).
- Whether NeuFoeG relief eligibility is affected for someone who ran a business abroad in the last five years (the law counts foreign activity).
- Whether the GmbH shareholder-director is insured under ASVG or GSVG in the founder's exact setup (25% and 50% rules, blocking minority).
- Tax residence consequences for a founder who moves to Austria (not researched).
- All permit questions in the visa section's `open_questions_for_reviewer`.

## Things the agent could not verify

- Any official GmbH setup-time range.
- The FlexKapG total minimum capital from a primary page.
- Whether the EUR 218 fee is the only federal fee, and whether NeuFoeG waives it.
- The migration.gv.at pages carry no date.
- usp.gv.at startup portal (startup.usp.gv.at) is a JavaScript app and could not be read directly; USP content came from www.usp.gv.at pages instead.
- Quotes come through a fetch tool that summarises pages. The independent re-verification (QA check 4) must reopen each figure.
- The Austrian Business Agency and German-law-family skill consult were not used beyond the one ABA page.

## Top questions for Nijil

1. Is the visa section to stay held until an Austrian immigration lawyer reviews it (recommended)?
2. Should the guide describe the trade-licence trap more strongly in the quick facts (it now leads with the permit and the managing-director rule)?
3. Do you want FlexKapG (FlexCo) covered in more depth, or kept as a short row until its capital is confirmed?
4. May the guide use WKO (chamber) pages for cost estimates (notary, Firmenbuch) when no official tariff was read?
5. Should the next re-check be set for January 2027 (current) or later?

## Added by the independent checker (2026-10-13)

- Agent review, not legal advice. No German-law-family skill consult was run; Austrian law differs from German law.
- SVS accident insurance: SVS says EUR 12.96 per month for 2026, WKO says EUR 12.95. Guide follows SVS and says the minimum total is "about EUR 161". Re-check in January 2027.
- The 2026 maximum SVS base (EUR 8,085) and maximum monthly total were removed from the rendered text: only WKO states them and a WKO page of 31 Aug 2026 calls the values non-binding. Confirm from SVS.
- The 4.9% VAT rate exists only for selected basic foods from 1 July 2026 (USP). Reviewer may prefer to drop it.
- Small-business flat rate (EUR 60,500; 45% / 20%) rests on the WKO brochure alone. Find the BMF or USP page.
- FlexKapG total capital: ABA (English) says EUR 10,000 with EUR 5,000 cash; still held until a primary page confirms it.
- Grundumlage: the exemption is the calendar year after the start year, so the founding year owes it. A local adviser should confirm for the founder's Fachgruppe.
- GewO s.14(1) requires a new permit only from first-time applicants not yet lawfully in Austria. Whether a founder already living in another EU country on a national permit counts is for the immigration lawyer.
- WKO page AT13 still mentions Handysignatur (replaced by ID Austria); not used in the guide.
