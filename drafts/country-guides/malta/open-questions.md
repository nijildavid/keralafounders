# Open questions: Malta (agent draft, unreviewed)

Agent review, not legal advice. Nothing here has been checked by a lawyer.

## Pages that could not be opened (important)
1. The tax authority site (cfr.gov.mt / mtca.gov.mt), the Department of Social Security (socialsecurity.gov.mt), Malta Enterprise (maltaenterprise.com), Servizzi.gov.mt and mfsa.mt all returned HTTP 403 (Cloudflare block) from the agent environment. I did not try to get around the block. As a result:
   - VAT, company tax, refunds and Class 2 figures are sourced from the consolidated laws on legislation.mt (official, but not the authority's own explanatory pages).
   - The tax authority's explanation of the shareholder refund (forms, timing, 14-day payment claim that advisers quote) is NOT verified. Held.
   - E-invoicing plans (mandatory B2B e-invoicing / ViDA timetable) are NOT verified. Held. Only the current VAT Act rule (electronic invoice needs recipient acceptance) is stated.
   - Tax registration steps for a sole trader or new company, provisional tax, filing deadlines: held.
   - Annual return fees at MBR: the fee page opened lists only the registration fee. Held.
   - Current weekly Class 2 amounts as published by the Department of Social Security were not checked; the guide uses the figures printed in the Act.
   - Malta Enterprise's own start-up pages were not read; the programme PDF is hosted by Residency Malta Agency.
2. Language: official pages and the laws are in English (Malta's co-official language). I did not read Maltese-language versions.

## Judgment calls
3. Minimum capital: the prompt said "issued 20%, check". The sources say the minimum AUTHORISED capital is EUR 1,164.69, at least that minimum must be SUBSCRIBED, and at least 20% of each share taken up must be PAID UP at signing. The guide uses that wording.
4. Companies Act Art 72(1) says the private-company minimum is "subscribed by at least two persons", while the MBR guide says a single-member company is allowed with one subscriber. I followed the MBR guide (the Act has single-member provisions I did not read in full). Checker 2026-10-13: the MBR guide text does say this (p.5), so the guide wording is supported; a local professional should still confirm.
5. Directors: neither MBR nor the Act text I read states a residence or nationality condition for directors or shareholders. I stated only what the sources say (secretary need not be resident). I did not claim "no residence requirement for directors".
6. Notary: the MBR guide does not mention a notary for a company. The table says "not mentioned in the MBR guide" rather than "not needed".
7. Individual income tax bands: the consolidated text shows the single-person bands footnoted "applicable from year of assessment 2027". I show them with that label and hold the year-of-assessment-2026 bands. Checker 2026-10-13: the footnote is attached to Art 56(1)(b) itself (Act III of 2026, Art 13(2)); YA 2027 = income earned in 2026. Label settled; YA 2026 bands still not read.
8. The 5% effective rate is arithmetic (35% x 1/7), labelled editorial, not an official statement.
9. Tax refund: the user asked for the explanation "as given by CFR". Only the statute (Income Tax Management Act Art 48) was verified.
10. Class 1 vs Class 2 for a working director-shareholder is not verified; Jobsplus says the sole owner registers as an employee, so the guide says "expect" employee treatment in an editorial note only.
11. Source type: legislation.mt is tagged "official" (government publication). Identita FAQ is a government page but is an FAQ, not law.
12. The Start-up Residence PDF is version 4 dated 22 August 2024; I did not confirm that it is still the current version. Visa claims are all held anyway.
13. next_check_due set to 2027-01-11 because social security rates and Budget changes usually take effect in January.

## Conflicts between sources
14. KEI/SEI salary thresholds: Identita FAQ EUR 45,000 / 30,000; Jobsplus page EUR 35,000 / 25,000.
15. Jobsplus Jan 2021 guide says the single permit procedure does not apply to self-employed; the Identita pages agree that self-employment is a separate permit. Fee lists differ by authority (Identita EUR 100; Jobsplus EUR 150 + 80 in the 2021 guide). Possibly different fees for different steps, possibly outdated.
16. Some advisers quote separate Article 11 thresholds (EUR 35,000 for goods, EUR 30,000 for services) are superseded by a single EUR 35,000 domestic threshold in the Act.

## For a local professional to confirm
- Whether a non-EU founder can lawfully be employed by their own newly formed company (labour market test, advertising, minimum salary) and the realistic timeline.
- Whether the January 2021 self-employment licence criteria still apply after the 2025 labour migration policy (identita.gov.mt/wp-content/uploads/2025/07/MIGRATION-POLICY-DOC-JUNE-2025.pdf, not read).
- Bank account opening for non-resident founders (not researched).
- Whether the Article 11 exemption is useful to a company with non-resident shareholders.

## Top 5 decisions for Nijil
1. Keep the visa section held, or ask for a Maltese immigration lawyer review? (Recommended: keep held.)
2. Accept legislation.mt statute text as the main tax source, or wait for a re-check once the tax authority pages are reachable?
3. Publish the individual tax bands (labelled year of assessment 2027) or drop them?
4. Keep the "founders are employees" extra section as the country trap?
5. Is January 2027 an acceptable re-check date?

## Added by the independent checker (2026-10-13)
17. NEW, important: the Class Two weekly amounts printed in the Social Security Act (EUR 34.70 / 15% / EUR 79.84; EUR 63.46 for persons born up to 1961) appear to be out of date against the Department of Social Security's 2026 Class 2 table (search snippets show SA 36.18, 73.56 and 83.89; the DSS page itself returned 403 so this is unconfirmed). The figures were removed from the guide and FAQ and held. Needs: someone able to open https://socialsecurity.gov.mt/?p=543035 (and the Class 1 2026 table, which also gives the weekly maxima) to copy the real 2026 amounts, then re-add them with the year.
18. The Class One weekly maxima remain held for the same reason.
19. The low-income certificate limit has a second figure for married persons (EUR 1,470); now in the guide.
20. Still blocked from this environment: mtca.gov.mt / cfr.gov.mt, socialsecurity.gov.mt, maltaenterprise.com (all HTTP 403 via curl and WebFetch). Items that depend on them stay held: shareholder refund procedure and timing, provisional tax and filing deadlines, tax registration steps, e-invoicing timetable, annual MBR fees.
21. Visa section (hold:true) cross-checked against the pages and the 2021 Jobsplus guide: values match their sources, but the KEI/SEI thresholds conflict between Identita and Jobsplus and the 2021 guide's currency is unknown. A reviewer must settle these.
22. The Identita FAQ also says the Single Permit employer must advertise for 3 weeks within the 2 months before filing; the guide mentions the 3/2 weeks but not the 2-month window. Minor.
