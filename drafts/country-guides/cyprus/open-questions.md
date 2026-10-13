# Open questions: Cyprus (agent draft, unreviewed)

Agent draft, agent review, not legal advice.

## Judgment calls I made
1. **Language.** Pages were read in English. The Registrar's English pages are the authority's own; the Greek-language pages were not read, so national-language cross-checking (Standard rule 2) is incomplete.
2. **gov.cy blocked.** The Tax Department, Migration Department, Startup Visa, Social Insurance Services and Tax For All pages return an automated-access block (Azure WAF / HTTP 403). I used the Point of Single Contact (a government portal), the EURES portal and Registrar pages instead, tagged `government_portal`/`official`. Anything that depends only on blocked official pages is held.
3. **Corporate tax rate held.** Registrar and PSC pages still say 12.5%. Press reports (Cyprus Mail, secondary) say 15% from 1 Jan 2026. I held both rather than state either.
4. **Personal income tax rendered.** Two official portals (PSC, EURES) show the 2026 bands, so rendered.
5. **Social insurance 16.6%** rendered as a 2025 figure (EURES labels it 2025). The 2026 figure and EUR 68,904 cap come only from KPMG, so held.
6. **Director residence held.** The PSC says a Cypriot-resident director is required. The Registrar pages do not say so. I held it as a conflict instead of rendering it.
7. **Legal-forms table** covers only self-employed and Ltd. Minimum capital is shown as "not stated", not "none", because I could not verify the rule.
8. **Extra section** `company-from-abroad` (one allowed) covers director/tax residence and the digital-nomad trap.
9. **next_check_due 2027-01-11**: tax bands, SI ceilings and fees reset on 1 January.
10. Dates: the run spanned 12-13 Oct 2026; all dates use 2026-10-12.

## Source conflicts
- Corporate tax: 12.5% (DRCIP, PSC) vs 15% (press, from 1 Jan 2026).
- VAT threshold EUR 15,600 (PSC, DRCIP) vs the 2025 EU small-enterprise scheme; PSC links to the new scheme but still quotes 15,600.
- VAT return timing: PSC says "within 40 days of declaration" in one place and "10th day of the second month" in another (roughly consistent).
- Digital nomad: up to 1 year + 2 (Feb 2024 strategy) vs reportedly 2 years max (Dec 2025 scheme document, not opened).
- Startup Visa capital: EUR 20,000 (2019 booklet, reported) vs no figure in the 2024 guide (reported).
- Self-employed deadlines on the PSC page (30 Sept / 30 June / 15 months) look pre-reform and were NOT used.

## A local professional must confirm
- Minimum share capital and any director-residence rule for a private Ltd.
- Company tax rate and dividend tax in 2026; deemed-dividend rules.
- Small-enterprise VAT threshold in 2026 and whether EUR 15,600 still applies.
- Whether an owner-director is treated as employee or self-employed for social insurance.
- All non-EU permit routes, fees and processing times (Category F, self-employment permit, Startup Visa, Business Facilitation Unit, digital nomad, "pink slip" employment permit), directly with the Civil Registry and Migration Department.
- Whether a non-resident can register as self-employed before holding residence status.
- Bank-account opening for foreign-owned companies (not researched).
- GeSY contribution ceiling (secondary sources disagree; left out).
- Registrar processing times (not published on opened pages).

## Could not verify
- Anything on the Tax Department, Social Insurance Services, Migration Department, GeSY/HIO and Tax For All sites.
- Startup Visa, Category F and self-employment permit figures (search summaries only; recorded as reviewer questions, not claims).
- Whether the Registrar's site is fully up to date (CY10 is a navigation page summary).

## Top 5 decisions for Nijil
1. Accept a Cyprus guide where the company tax rate and the non-EU permit routes are held until a local professional or the Tax Department confirms them?
2. Is a fallback acceptable where official gov.cy sites cannot be reached by the agent (use the Point of Single Contact and EURES as `government_portal` sources), or should someone with a normal browser re-check them?
3. Keep the extra "directors, tax residence and running a company from abroad" section, or drop it until the director-residence question is settled?
4. Who does the independent re-verification (Standard QA 4) and the Migration Department check?
5. Should the guide say anything about Category F / Startup Visa at all before they are verified (currently reviewer questions only)?

## Added by the independent check (2026-10-13)
- Settled by official pages: company tax 15% from 1 Jan 2026 (Tax Department), dividend SDC 5% for individuals, VAT threshold EUR 15,600 (Tax Department and EC SME portal), no minimum share capital for a private company. An accountant should still confirm company and non-resident dividend cases. Earlier lines above saying these are open are superseded.
- The Registrar comparison table and the Point of Single Contact still print 12.5% corporate tax; the Tax Department says 15%. Readers of those pages will see conflicting numbers.
- Self-employed social insurance for 2026 (16.6%) and the self-employed ceiling are confirmed only by KPMG; the official notice found covers employees (EUR 5,742 a month).
- The Point of Single Contact says self-employed social insurance "now includes" the health-system contribution, while EURES lists GeSY 4.00% separately. The guide treats them as two charges; confirm.
- The EUR 70,000 self-employed audit threshold and filing dates on the PSC page may predate the 2026 reform; check the Tax Department's 2026 guide for individuals.
- Director-residence conflict (PSC vs Registrar directors page) still needs a Cyprus lawyer.
- Visa figures (Category F, Startup Visa, self-employment permit) still could not be opened; Migration Department pages need a normal browser.
- Tax Department VAT-rates and news pages returned blocks; VAT rates rest on EURES and PSC only.
- The Tax Department site (gov.cy/mof-tax) is reachable by curl with a browser user agent, so the author's "blocked" finding applied to other hosts.
