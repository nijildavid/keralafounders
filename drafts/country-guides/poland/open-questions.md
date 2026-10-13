# Open questions: Poland (agent draft, unreviewed)

Agent review, not legal advice. Nothing here has been reviewed by a lawyer, accountant or the authorities.

## Judgment calls made by the drafter

1. **VAT exemption limit: PLN 240,000.** Two official podatki.gov.pl pages (updated 26 May 2026) still say PLN 200,000. The Sejm's text of the amending act (Dz.U. 2025 poz. 896) and the Ministry's "Stawki i limity" page say PLN 240,000 from 1 Jan 2026. I followed the act text. Nijil or an accountant should confirm.
2. **Extra section.** I added one country-specific section, "who may register a sole proprietorship", because the rule that non-EU foreigners without a qualifying residence title can only use company forms is the biggest trap for this audience.
3. **Visa section kept on hold** with drafted claims at confidence "hold", as the Standard requires.
4. **Source type for regional voivodeship pages** (Lubuskie, Lublin): tagged `official` because they are government offices, but they are regional, not the central authority. Used only inside the held visa section.
5. **Source type for Biznes.gov.pl and Powroty.gov.pl:** tagged `government_portal`.
6. **CRBR deadline left out of the guide** because official pages disagree (14 days on the S24 page, 7 days on the KRS page and the tax page).
7. **Ryczałt rate range.** I used the nine rates on podatki.gov.pl (3% to 17%). A search summary of another page mentioned "2% to 17%"; I did not use it.
8. **Next check date** set to 15 Jan 2027 because the ZUS bases (minimum wage, forecast average wage), the KSeF 1 Jan 2027 step and possibly the VAT limit change on 1 January.
9. **ZUS PDF typo.** The ZUS analysis says the preferential base is PLN 1,441.80 "w 2025 r." in one place; the tables show it as 2026, so I treated it as a typo.

## Conflicts between sources

- VAT limit: PLN 200,000 (podatki.gov.pl "Rozpoczęcie działalności" pages, updated 26 May 2026) vs PLN 240,000 (act text; "Stawki i limity" page).
- VAT standard rate: the "Stawki i limity" page says 22% in its intro and 23% in the rate list. 23% is correct in law as far as I know, but I did not open the VAT Act.
- CRBR: 14 days vs 7 days (see above).

## What a local professional must confirm

- Whether a newly arrived non-EU founder can lawfully live in Poland, be a board member of a sp. z o.o., and run it, and on what permit. This is the real question behind the visa section and I could not answer it from the pages I could open.
- Whether a non-EU board member needs a separate work authorisation (Act of 20 March 2025 on conditions for entrusting work to foreigners).
- Whether a registered seat or delivery address in Poland, or any EU/EEA-resident director, is required for a sp. z o.o. The Biznes.gov.pl and KRS pages I opened mention no residency requirement for founders or board members, but absence of mention is not proof.
- How a foreigner without a PESEL obtains a qualified signature or personal signature for S24 in practice, and whether S24 works for a non-resident with no PESEL. The pages say a trusted profile needs ePUAP; they do not discuss foreigners.
- Accounting obligations of a sp. z o.o. and of a JDG (full books vs simplified); not researched, left out of the comparison table.
- Notary fee for a notarial-deed company; not found on an official page.
- Dividend taxation of a sp. z o.o. (distribution to a non-resident shareholder, treaty relief); not researched.
- Whether the preferential-contribution and ulga-na-start rules apply to a foreigner who pays social security in another country (totalisation, A1 certificate); not researched.
- The "działalność nierejestrowana" (unregistered activity) rules; a secondary summary said they were narrowed from 1 June 2025, which I did not verify and did not use.

## Could not verify

- MOS (mos.cudzoziemcy.gov.pl) and UdSC (udsc.gov.pl/wymogi) pages returned HTTP 503 or a redirect on 12 Oct 2026. The business-permit conditions therefore rest on two regional voivodeship pages.
- Processing time of the business-activity residence permit. One third-party summary cited 60 days (art. 112a of the Act on Foreigners); not verified.
- Legal text of the Act on Foreigners (12 Dec 2013) and of the 2018 act on participation of foreign entrepreneurs; ISAP/Sejm pages showed a human-verification screen. The foreigner rule rests on Biznes.gov.pl and Powroty.gov.pl pages.
- Whether Poland. Business Harbour is still suspended (the only mention is a Biznes.gov.pl page published in 2021).
- Page dates: several Biznes.gov.pl pages carry only a 2021 publication date and no "current as of" date.

## Top questions for Nijil

See the PR description; summary: (1) keep the visa section held until a Polish immigration lawyer reviews it; (2) accept an `official` tag for regional voivodeship pages; (3) decide whether to include accounting and dividend-tax detail; (4) confirm the 15 Jan 2027 re-check; (5) decide how prominently to present the JDG-versus-company rule for non-EU founders.

## Added by the independent checker (2026-10-13)

- VAT limit: the act text (Dz.U. 2025 poz. 896) and the Ministry's rates page support PLN 240,000 from 1 Jan 2026, but two podatki.gov.pl start-up pages (and the body of the rates page itself) still show PLN 200,000. A held CONFLICT claim is in the taxes section. An accountant should confirm; nobody has asked the Ministry to fix the pages.
- The podatki.gov.pl pages block ordinary downloads (bot wall). Figures from them (PIT, CIT, VAT, Estonian CIT) were read through a page-summarising fetch tool with verbatim quotes requested, so a human spot check of PL7, PL8, PL9, PL18 and PL19 in a browser is worthwhile.
- CRBR deadline is still 14 days (S24 page) vs 7 days (KRS page and tax page); the guide states no deadline.
- PCC base: the S24 page deducts both KRS and MSiG fees, the KRS page only the KRS fee; the guide now says so. An accountant should confirm the exact base.
- PL17 (.../00166-1) serves the same page as PL4; consider dropping PL17 as a duplicate source.
- Visa: the "3 years" validity sentence on the Lubuskie page appears in employer-based wording and may not describe the business permit; MOS and UdSC /wymogi pages were still unreachable on 13 Oct 2026.
- KSeF: the sources read do not say whether VAT-exempt (art. 113) small taxpayers must issue invoices in KSeF; the guide claims nothing either way. Worth asking an adviser, since many target readers will be under the exemption limit.
- The health-contribution year runs 1 Feb to 31 Jan (minimum wage on 1 Feb 2026 is the base), so "re-check each January" suits the wage figures, but the health figure changes in February.
- Business Harbour: the Biznes.gov.pl page was updated 23 July 2026 and still says suspended; fine to keep rendered, but no second source was found.
