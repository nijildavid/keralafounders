# Open questions: Portugal (agent draft, unreviewed; agent review, not legal advice)

## Judgment calls
1. PT6 (Companies Code art. 219) is a consolidated copy on the Lisbon regional prosecutor's legal portal, typed `government_portal`, not the Diario da Republica. EUR 1 minimum per quota should be re-checked at DRE.
2. "Minimum capital": the guide says each quota must be at least EUR 1, not "capital EUR 1". The base prompt said "minimum capital EUR 1 and rules"; no page opened states a company-level minimum.
3. Where gov.pt and the tax/social-security authority disagree, the authority's own document was used and the gov.pt figure held (VAT filing days 20/25 vs 10/15; contribution rate 21.4% vs 29.6%).
4. Liability wording ("personal" for sole traders, "limited to the company" for a Lda.) is general law not tied to a page opened; flagged "general rule" in the table.
5. next_check_due set to 2027-01-08 because IAS, VAT thresholds, contribution tables and IRC rates reset or change around 1 January.
6. Social security payment dates cite the gov.pt service page (10th to 20th); the Institute guide of May 2025 was not checked for payment days.
7. No D2 / D8 codes used: the official pages opened describe categories without codes.

## Conflicts between sources
- VAT return deadlines: AT leaflet (20th / 25th) vs gov.pt guide (15th / 10th).
- Self-employed contribution rate: ISS guide 21.4% (May 2025, 25.2% for ENI) vs gov.pt guide 29.6%.
- Empresa na Hora: IRN page says all partners present; gov.pt page says present or legally represented.
- Empresa Online price table was garbled on the page; urgent prices EUR 440 / EUR 720 were inferred from the column layout and should be eyeballed.

## Could not verify
- AIMA (aima.gov.pt) returned HTTP 503 / certificate errors: residence-permit steps, validity, fees, processing times.
- Consular visa fee (seen EUR 90 in a snippet only), subsistence amounts, official processing times in the MNE portal.
- Whether an EU/EEA resident is needed to register a company or to be a director: no such requirement on pages opened, but absence is not proof.
- Whether Empresa na Hora can be used by a non-resident in practice (appointment, passport as ID, NIF, bank).
- Seg-social.pt rules for Lda. managers (paid and unpaid); any first-year exemption for directors.
- B2B structured e-invoicing mandate status, PDF-invoice signature rule after 31 Dec 2026, accounting SAF-T timing.
- Derrama (municipal and state surtax) and IRS progressive rates not covered.
- Whether the free AT invoicing tool is usable by a non-resident without Portuguese credentials.
- IFICI eligibility for founders (art. 58-A EBF list not opened).

## A local professional must confirm
- NIF route for a founder abroad, fiscal representative need, and VAT status.
- Whether the art. 53 exemption or EU-based small-business scheme (EX number) fits the founder.
- Simplified IRS regime vs organised accounting.
- Manager social security.
- The whole visa section and tax incentive eligibility.

## Questions for Nijil
1. Approve publishing a draft that treats gov.pt pages as `government_portal` sources when the primary authority page was unreadable?
2. Accept that the visa section is thin on AIMA until its site is reachable?
3. Should the second-agent re-verification prioritise the held conflicts (VAT days, contribution rate)?
4. Is "no company-level minimum, EUR 1 per quota" the wording you want in quick facts?
5. Do you want derrama and IRS bracket tables added (needs more research)?

## Added by independent check (2026-10-13)
- VAT return days and the 21.4% vs 29.6% rate are resolved in favour of the law and the Institute (20th; 21.4%). Still ask a human to confirm no 2026 change to art. 168, since no consolidated 2026 text opened.
- The gov.pt self-employed guide disagrees with the law on two points (VAT days, contribution rate); treat gov.pt guides as secondary for figures.
- Art. 53(2) EX route for EU-based businesses vs the AT FAQ line that excludes non-domiciled businesses: the two pages read differently; accountant to confirm.
- PT6 is still a consolidated copy; check art. 219 on DRE.
- IFICI FAQ lists company managers as eligible; relevant to the held visa section only.
