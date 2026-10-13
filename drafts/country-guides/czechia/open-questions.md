# Open questions: Czechia (agent draft, agent review, not legal advice)

## Judgment calls made
1. businessinfo.cz (the usual Czech starter portal) returned HTTP 403 to every fetch, so no figure rests on it. Trade-licence facts rest on the MPO page, portal.gov.cz and the immigration site. Two trade facts (age 18/capacity, any-office filing) seen only in search snippets were left out.
2. Two country-specific sections were added: `trade-types` (the four trade types and the foreigner document rules) and `flat-rate` (pausalni dan). Both are the usual first-year traps.
3. Mid-2026 changes: the CSSZ minimum pension advance fell from CZK 5,720 to CZK 5,005 from 1 July 2026 (Act no. 90/2026 Coll. per search results; CSSZ page opened). Both figures are in the guide with their periods.
4. The quick-fact gotcha is the visa/trade-registration order problem, which is a visa topic. It is shown as a plain pointer; the details stay in the held visa section.
5. Derived number: CZK 1,762,812 = 36 x 48,967 (own arithmetic). Marked as derived in the text.
6. next_check_due set to 2027-01-15 because flat-rate bands, minimum advances and the average wage reset in January.

## Conflicts between sources
- Notary registration fee: the portal page lists CZK 2,700 for a notary-made first entry and also exempts notary-made entries based on a standard-content s.r.o. deed with cash contributions only (checker reading: the two fit together, the result depends on the deed). Held for a notary to confirm.
- CSSZ "OSVC v kostce" says the start-year advance exemption applies when no business was run in the previous 5 years; the other CSSZ page speaks of a starter minimum when no SVC for 20 years. Both are reported; how they interact needs CSSZ confirmation.
- Business-visa consular fee: ipc.gov.cz says CZK 5,000; other pages and secondary sites say CZK 2,500 or 5,000 depending on the mission.
- Living minimum for the business permit: ipc page (CZK 5,000, housing from 1 Oct 2026) conflicts with the archived MV page (CZK 4,860, 2023). Held.
- Blue Card contract length: one year (eligibility) versus six months (documents), same ipc page.
- Band I flat-rate advance after June 2026: a secondary article says CZK 9,162; the tax office page read says CZK 9,984 for 2026 and the CSSZ page tells band I taxpayers to ask the tax office.

## What a Czech professional must confirm
- Sole-director and non-resident rules for an s.r.o. (director's address, EU residence). The visa/trade order is described on the MPO page (CZ15 s.2.2.3: trade notified first; right to trade arises when the visa or permit is shown, within 3 working days of registering residence and within 6 months of the trade office's extract); a professional should confirm how this works in practice from abroad, including representation by power of attorney.
- Current s.r.o. minimum contribution (source dated 2016), notary tariff, and the true all-in cost and timeline.
- Social and health insurance position of an s.r.o. director.
- Mandatory content of a Czech invoice and any B2B e-invoicing timetable (not found on an opened official page).
- Income-tax allowance for 2026 (CZK 30,840 reported by secondary sites only).
- Each flat-rate band's revenue limit for 2026.
- Health-insurer registration deadline after starting.

## Could not verify
- Anything on businessinfo.cz, justice.cz, the Czech Criminal Register process, or the Act text on e-Sbirka (the page returned an empty shell).
- The FS corporate-tax PDF could not be read; the 21% rate comes from the FS system description and general-information pages instead.
- Visa fees and funds for 2026 beyond what the ipc.gov.cz pages state.
- Everything in this folder is unreviewed by a lawyer. Independent re-verification (Standard QA 4) was done on 2026-10-13 by a second agent; see evidence-log.md.

## Added by the independent checker (2026-10-13)
- Business-visa funds (CZK 156,500 = 50 x 3,130, 2023) and the 2026 living-minimum figures sit on the same ipc.gov.cz page and may not be consistent; a lawyer must confirm which applies.
- The e-invoicing page (CZ17) is dated 2 Oct 2018; a newer B2B e-invoicing timetable would not appear on it.
- The lump-sum expense caps (CZ11) rest on the English gov.cz page only; a Czech version was not opened.
- The date the 21% / 12% VAT rates took effect (believed 1 Jan 2024) is not printed on CZ10 or CZ28; it was removed from the guide. Confirm on an official page before adding it back.
- Whether an s.r.o. needs its own trade notification for a trade-licensed activity: CZ15 s.3 says a company's right to trade arises on its commercial-register entry; scope still to be confirmed.
- January 2027 re-check: replace the 12 January 2026 flat-rate deadline with the 2027 date once the tax office publishes it.
