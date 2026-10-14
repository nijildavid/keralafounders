# Evidence log: Sweden (agent draft, unreviewed)

Author: Claude agent (draft). Pages opened 2026-10-12 via WebFetch (the fetch tool summarises the page, so "quote" below is the quote the tool returned from the page, not a copy I checked by eye in a browser). Independent re-verification (Standard QA 4) has NOT been done: a second agent must re-open every row. "Verified" below means: read on the named official page by the author agent.

| # | Value | Year / as-of | Source | URL | Quote or paraphrase from the page | How verified | Confidence |
|---|---|---|---|---|---|---|---|
| 1 | Minimum share capital SEK 25,000 (AB) | Oct 2026 | SE2 | https://www.verksamt.se/starta/valj-foretagsform/aktiebolag | "När du startar ett aktiebolag måste du ha minst 25 000 kronor i aktiekapital." | Page opened | official |
| 2 | SEK 25,000 minimum in statute | text to SFS 2026:783 | SE8 | https://data.riksdagen.se/dokument/sfs-2005-551.text | 1 kap. 5 §: "Om aktiekapitalet är bestämt i kronor, ska det uppgå till minst 25 000 kronor." (also confirmed in consolidated text via curl, line 117) | Full statute text downloaded and grepped | official |
| 3 | AB registration fee from SEK 2,400 | Oct 2026 | SE2, SE5 | as above; https://verksamt.se/en/setting-up/choose-business-type/limited-company | "Det kostar från 2 400 kronor att registrera ett aktiebolag." / "from SEK 2,400" | Both language versions opened; agree | official |
| 4 | Beneficial-owner registration SEK 250 | Oct 2026 | SE2, SE5 | as above | "Det kostar 250 kronor att anmäla verklig huvudman." | Both versions opened | official |
| 5 | Optional name protection (sole trader) from SEK 1,800 | Oct 2026 (page updated 29 Sep 2026) | SE3, SE6 | https://www.verksamt.se/starta/valj-foretagsform/enskild-naringsverksamhet | "Det kostar från 1 800 kronor att skydda företagsnamnet." | Both versions opened; a search snippet (FAR) said 1,800 e-service / 2,100 paper, not used | official |
| 6 | Skatteverket registration free | Oct 2026 | SE1, SE7 | https://www.verksamt.se/starta/valj-foretagsform | "Alla ärenden som du anmäler till Skatteverket är avgiftsfria." | Page opened | official |
| 7 | Board: at least 1 member and 1 deputy | Oct 2026 | SE2, SE5 | as above | "minst en styrelseledamot och en styrelsesuppleant" (see open question on exact statute) | Page opened | official |
| 8 | At least half of board resident in EEA; Bolagsverket exemption for special reasons | text to SFS 2026:783 | SE8 | https://data.riksdagen.se/dokument/sfs-2005-551.text | 8 kap. 9 §: "Minst hälften av styrelseledamöterna ska vara bosatta inom Europeiska ekonomiska samarbetsområdet. Om det finns särskilda skäl, får Bolagsverket ... besluta om undantag" | Statute downloaded, read | official |
| 9 | Managing director EEA-resident (exemption possible) | same | SE8 | same | 8 kap. 30 § | Statute read | official |
| 10 | At least one special signatory EEA-resident | same | SE8 | same | 8 kap. 37 § | Statute read | official |
| 11 | Person resident in Sweden to receive service if no representative resident in Sweden | same | SE8 | same | 8 kap. 40 § "särskild delgivningsmottagare" | Statute read | official |
| 12 | VAT exemption threshold SEK 120,000 | Oct 2026 | SE9 | https://skatteverket.se/momsbefrielse | "högst 120 000 kronor" | Page opened | official |
| 13 | Not exceeded in either of two previous calendar years | Oct 2026 | SE9 | same | "har inte överstigit 120 000 kronor under något av de två närmast föregående kalenderåren" | Page opened | official |
| 14 | EU-wide turnover EUR 100,000 for other-EU-seat companies | Oct 2026 | SE9 | same | EU turnover must not exceed 100,000 euro | Page opened | official |
| 15 | Non-EU seat: exemption does not apply | Oct 2026 | SE9 | same | seat outside EU: small-business exemption does not apply | Page opened | official |
| 16 | VAT rates 25% / 12% / 6% | 2026 | SE10 | https://www.skatteverket.se/privat/skatter/beloppochprocent/2026 | general 25%, 12% and 6% for certain goods and services | Page opened | official |
| 17 | Food VAT 6% from 1 April 2026 | 2026 | SE10 | same | "Från och med 1 april 2026 är momsen på livsmedel 6 procent." | Page opened (not used in a founder-critical claim) | official |
| 18 | Corporate tax 20.6% | 2026 | SE10 | same | "Statlig inkomstskatten för juridiska personer är 20,6 procent." | Page opened | official |
| 19 | Egenavgifter 28.97% (born 1959 or later) | 2025 and 2026 | SE11, SE10 | https://www.skatteverket.se/privat/skatter/arbeteochinkomst/inkomster/egenavgifter.4.70ac421612e2a997f85800022812.html | table: "Född 1959 eller senare 28,97 %"; SE10 "Summa 28,97 procent" | Both pages opened; agree | official |
| 20 | Standard deduction (schablonavdrag) 25% | 2025 and 2026 | SE11, SE17 | same; https://www.skatteverket.se/foretagochorganisationer/foretagare/enskildnaringsverksamhet.4.5c13cb6b1198121ee8580002518.html | "schablonavdrag för beräknade egenavgifter med högst 25 procent" | Both opened | official |
| 21 | Employer contributions 31.42% | 2026 | SE10 | https://www.skatteverket.se/privat/skatter/beloppochprocent/2026 | table row "Summa" arbetsgivaravgift 31,42 procent | Page opened | official |
| 22 | Prisbasbelopp SEK 59,200; inkomstbasbelopp SEK 83,400 | 2026 | SE10 | same | "Prisbasbeloppet inkomstår 2026: 59 200 kronor"; "Inkomstbasbeloppet för år 2026 har fastställts till 83 400 kronor" | Page opened | official |
| 23 | Simplified invoice up to SEK 4,000 incl. VAT | Oct 2026 | SE15 | https://www.skatteverket.se/foretag/moms/saljavarorochtjanster/momslagensregleromfakturering.4.58d555751259e4d66168000403.html | "Om totalbeloppet på fakturan inte överstiger 4 000 kronor inklusive moms." | Page opened | official |
| 24 | Simplified closing / cash method if turnover normally under SEK 3 million | Oct 2026 | SE16 | https://www.skatteverket.se/foretag/drivaforetag/bokforingochbokslut/bokforingvadkraverlagen.4.18e1b10334ebe8bc80005195.html | "normalt understiger 3 miljoner kronor" | Page opened | official |
| 25 | Keep bookkeeping at least 7 years | Oct 2026 | SE16 | same | "under minst 7 år" | Page opened | official |
| 26 | Fiscal year max 18 months (not used in guide text) | Oct 2026 | SE16 | same | "inte längre än 18 månader" | Page opened | official |
| 27 | E-invoice duty for public-procurement suppliers, procurements from 1 April 2019 | 2019 onward, page read Oct 2026 | SE18 | https://www.upphandlingsmyndigheten.se/digitalisering-och-e-handel/e-handel/regler-for-e-handel-och-e-faktura/ | "Lagen gäller upphandlingar som påbörjats från och med den 1 april 2019." | Page opened; page has no update date | official |
| 28 | EN 16931, UBL or CII; PDF is not an e-invoice | Oct 2026 | SE18 | same | paraphrase from page | Page opened | official |
| 29 | Tax-debt documents for countries lived in during last 2 years; at most 3 months old | Oct 2026 | SE12, SE13 | https://www.skatteverket.se/foretag/drivaforetag/startaochregistrera/fochfaskatt.4.58d555751259e4d661680006355.html | "Handlingarna får vara högst tre månader gamla när du ansöker." | Both pages opened | official |
| 30 | Verksamt: documents proving no tax debts no older than 3 months | Oct 2026 | SE4 | https://verksamt.se/en/setting-up/foreign-citizen | same rule | Page opened | official |
| 31 | Samordningsnummer: apply if staying under 1 year; active 5 years | Oct 2026 | SE14 | https://skatteverket.se/servicelankar/otherlanguages/inenglishengelska/individualsandemployees/coordinationnumbers.4.1657ce2817f5a993c3a7d2a.html | "stay in Sweden for less than one year"; "remains active for five years" | Page opened | official |
| 32 | Form SKV 4620 (paper registration) | Oct 2026 | SE3, SE6 | verksamt pages | "SKV 4620" | Pages opened | official |
| 33 | Form SKV 5703 (VAT representative, non-EU) | Oct 2026 | SE13 | https://www.skatteverket.se/foretag/internationellt/utlandskaforetagisverige/registreraverksamhet.4.569165a01749e7ae7893042.html | "Non-EU businesses generally need a VAT representative (form SKV 5703)" | Page opened | official |
| 34 | Sickness benefit karens default 7 days; options 1/14/30/60/90 | Oct 2026 | SE19 | https://www.forsakringskassan.se/privatperson/sjuk-eller-skadad/ersattning-nar-du-ar-sjuk-eller-skadad-sjukpenning/sjukpenning-har-eget-foretag/sjukpenning-for-dig-med-enskild-firma | "Som egenföretagare har du vanligen 7 karensdagar"; "1, 14, 30, 60 eller 90 dagar" | Page opened | official |
| 35 | Sickness benefit levels 25/50/75/100% | Oct 2026 | SE19 | same | "25 procent, 50 procent, 75 procent eller 100 procent" | Page opened | official |
| 36 | Report sick on first day or within 7 days | Oct 2026 | SE19 | same | "den första dagen du är sjuk, eller senast inom 7 dagar" | Page opened | official |
| 37 | Different SGI rules first 36 months | Oct 2026 | SE20 | https://www.forsakringskassan.se/privatperson/foretagare/enskild-firma-handelsbolag-eller-kommanditbolag | "mindre än 36 månader" (different SGI rules) | Page opened | official |
| 38 | Residence permit self-employed: SEK 200,000 own funds | Oct 2026 | SE21 | https://www.migrationsverket.se/du-vill-ansoka/arbeta/anstalld-eller-egen-foretagare/egen-foretagare.html | "minst 200 000 kronor på ett bankkonto i ditt namn" | Page opened | hold (visa) |
| 39 | Family funds SEK 100,000 spouse, SEK 50,000 per child | Oct 2026 | SE21 | same | listed in documents section | Page opened | hold (visa) |
| 40 | Bank statements last 3 months | Oct 2026 | SE21 | same | document list | Page opened | hold (visa) |
| 41 | Fees SEK 2,000 / 1,500 / 750 | Oct 2026 | SE21, SE22 | both Migrationsverket pages | "Arbetstagare/egenföretagare: 2 000 kronor" etc. | Both pages opened; agree | hold (visa) |
| 42 | Own at least 51% | Oct 2026 | SE21, SE22 | same | "äger minst 51 procent" | Both opened | hold (visa) |
| 43 | Permit at most 2 years, not beyond passport validity | Oct 2026 | SE21 | same | "högst två år, men aldrig för längre tid än ditt pass är giltigt" | Page opened | hold (visa) |
| 44 | First-time applications: 75% of recent incomplete decisions within 11 months; too few complete cases | Oct 2026 | SE21 | same | "75% av nyligen avgjorda ärenden har avgjorts inom:11 månader" | Page opened | hold (visa) |
| 45 | Extension: 75% within 19 months (incomplete); apply up to 2 months before expiry | Oct 2026 | SE22 | https://www.migrationsverket.se/du-vill-forlanga/arbeta/anstalld-eller-egen-foretagare/egen-foretagare.html | "inom:19 månader"; "tidigast två månader innan" | Page opened | hold (visa) |
| 46 | Permanent residence after 2 years; living amount SEK 6,243/month single adult 2026 | 2026 | SE22 | same | "För 2026 är den summan 6 243 kronor per månad för en ensamstående vuxen." | Page opened | hold (visa) |
| 47 | Application form 124011 | Oct 2026 | SE21 | same | "Ansökan om uppehållstillstånd för egna företagare (124011)" | Page opened | hold (visa) |
| 48 | next_check_due 2027-01-15 | n/a | n/a | n/a | Chosen: figures reset on 1 Jan (fees, base amounts, egenavgifter); within 6 months of 2026-10-12 | Author's choice | editorial |

## Not verified (not in the guide as fact)
- Bolagsverket processing times (about 7 working days for a new AB, 9 for other forms, snapshot 5 Oct 2026): came from a search-result summary only; Bolagsverket's page showed a bot check. Held as a hold claim.
- Bolagsverket's own fee page: not reachable (bot check). Fees taken from verksamt.se.

## Independent check (2026-10-13, checker agent, not the author)

Method. Every cited URL was re-opened on 2026-10-13. Raw text via curl for the statute (SE8), Upphandlingsmyndigheten (SE18), Forsakringskassan (SE19, SE20) and Migrationsverket (SE21 to SE23). verksamt.se is a JavaScript page (curl returns an empty shell) and Skatteverket closes curl connections from this sandbox, so those pages were read through the fetch tool, which summarises: quotes for those rows are the tool's quotes, not a raw-HTML check. Bolagsverket's own pages were not opened (the author could not either). Row numbers refer to the table above. "A1 to A8" are claims in guide.json not numbered in the table above.

| Row / figure | Verdict | What I saw | URL |
|---|---|---|---|
| 1 SEK 25,000 minimum capital | CONFIRMED | "minst 25 000 kronor i aktiekapital"; EN "minimum SEK 25,000" | SE2, SE5 |
| 2 Statute 1 kap. 5 § | CONFIRMED | raw text: "ska det uppgå till minst 25 000 kronor"; header "Ändrad: t.o.m. SFS 2026:783" | https://data.riksdagen.se/dokument/sfs-2005-551.text |
| 3 AB fee from SEK 2,400 | CONFIRMED | SV and EN both "from 2 400"; page updated 29 Sep 2026 | SE2, SE5 |
| 4 Beneficial owner SEK 250 | CONFIRMED | SV and EN | SE2, SE5 |
| 5 Name protection from SEK 1,800 | CONFIRMED | SV "från 1 800 kronor", EN "from 1,800 SEK"; updated 29 Sep 2026 | SE3, SE6 |
| 6 Skatteverket registration free | CORRECTED (citation only) | Value true, but the sentence "Alla ärenden ... avgiftsfria" is on SE1/SE7 (EN: "free of charge"), NOT on SE3, SE5 or SE6 (they say nothing on it). Source ids on quick_facts.cost, the legal-forms fee claim and faq se-sole-cost fixed | SE1, SE7 |
| 7 Board: 1 member and 1 deputy | CORRECTED (refined) | Verksamt says so, but the statute says 8 kap. 1 §: one or more members; 8 kap. 3 §: a deputy is required only if fewer than three members. Step 4 now gives both and cites SE8. Not a conflict: verksamt gives the minimum set-up | SE8 |
| 8 Half of board in EEA, Bolagsverket exemption | CONFIRMED | 8 kap. 9 § raw text matches | SE8 |
| 9 Managing director EEA | CONFIRMED | 8 kap. 30 § | SE8 |
| 10 One special signatory EEA | CONFIRMED | 8 kap. 37 § ("Minst en av de särskilda firmatecknarna") | SE8 |
| 11 Service recipient resident in Sweden | CONFIRMED | 8 kap. 40 §: needed if no competent representative lives in Sweden | SE8 |
| 12 VAT threshold SEK 120,000 | CONFIRMED | "högst 120 000 kronor"; calendar year Jan to Dec; excl. VAT ("Du ska inte ta med någon moms"). Page has no date, so "as shown October 2026" added to the guide | SE9 |
| 13 Two previous years | CONFIRMED | quoted as in the log | SE9 |
| 14 EUR 100,000 EU-wide | CONFIRMED | "överstiger 100 000 euro"; home-country notification required | SE9 |
| 15 Non-EU seat not exempt | CONFIRMED | "kan ditt företag inte vara undantaget från momsplikt i Sverige" | SE9 |
| 16 VAT 25/12/6 (2026) | CONFIRMED | "25 procent ... 12 ... 6", page "inkomstår 2026" | SE10 |
| 17 Food VAT 6% from 1 Apr 2026 | CONFIRMED | quoted | SE10 |
| 18 Corporate tax 20.6% (2026) | CONFIRMED | "20,6 procent" | SE10 |
| 19 Egenavgifter 28.97% (2025, 2026) | CONFIRMED | born 1959 or later 28,97 in both years; SE10 "Summa 28,97 procent" | SE11, SE10 |
| 20 Standard deduction 25% | CONFIRMED | "högst 25 procent" (SE17), 25 percent in table (SE11) | SE11, SE17 |
| 21 Employer contributions 31.42% | CONFIRMED | "Summa 31,42 procent" (born 1959 to 2002; other age groups differ, guide text does not claim otherwise) | SE10 |
| 22 Prisbasbelopp 59,200; inkomstbasbelopp 83,400 | CONFIRMED | both quoted for 2026 | SE10 |
| 23 Simplified invoice SEK 4,000 | CONFIRMED | "inte överstiger 4 000 kronor inklusive moms" | SE15 |
| 24 SEK 3 million simplified closing and cash method | CONFIRMED | "normalt understiger 3 miljoner kronor"; applies to sole traders. Page undated, as-of added | SE16 |
| 25 Keep books 7 years | CONFIRMED | "minst 7 år" (page does not say when the period starts; guide does not claim it) | SE16 |
| 26 Fiscal year max 18 months | CONFIRMED | not used in guide text | SE16 |
| 27 E-invoice public procurement from 1 Apr 2019 | CONFIRMED | raw text; applies to suppliers "oavsett i vilket land" | SE18 |
| 28 EN 16931, UBL/CII, PDF not e-invoice | CONFIRMED | raw text | SE18 |
| 29 Tax-debt documents, 2 years, 3 months old | CONFIRMED | "högst tre månader gamla"; company: every manager and controlling person under 2 years in Sweden | SE12, SE13 |
| 30 Verksamt: not older than 3 months | CONFIRMED | EN quote | SE4 |
| 31 Samordningsnummer under 1 year, 5 years | CONFIRMED | plus an extra condition (see A3) | SE14 |
| 32 SKV 4620 | CONFIRMED | paper form if the e-service cannot be used | SE3, SE6 |
| 33 SKV 5703 | CONFIRMED (wording tightened, see A4) | | SE13 |
| 34 Karens 7 days; 1/14/30/60/90 | CONFIRMED | raw text | SE19 |
| 35 Sickness benefit 25/50/75/100% | CONFIRMED | raw text | SE19 |
| 36 Report sick day 1 or within 7 days | CONFIRMED | raw text | SE19 |
| 37 SGI different first 36 months | CONFIRMED | "kortare tid än 36 månader finns undantag" | SE20 |
| 38 SEK 200,000 own funds | CONFIRMED (visa, hold) | "minst 200 000 kronor på ett bankkonto i ditt namn"; need not be in Sweden | SE21 |
| 39 SEK 100,000 spouse, 50,000 child | CONFIRMED (hold) | raw text | SE21 |
| 40 Bank statements 3 months | CONFIRMED (hold) | raw text | SE21 |
| 41 Fees 2,000 / 1,500 / 750 | CONFIRMED (hold) | also on SE22; page notes Japan citizens pay no fee (not in guide) | SE21, SE22 |
| 42 Own at least 51% | CONFIRMED (hold) | raw text | SE21, SE22 |
| 43 Permit max 2 years | CONFIRMED (hold) | raw text | SE21 |
| 44 11 months (incomplete), too few complete cases | CONFIRMED (hold) | statistics from last 12 months | SE21 |
| 45 19 months; apply 2 months before expiry | CONFIRMED (hold) | "tidigast två månader innan" | SE22 |
| 46 Permanent residence after 2 years; SEK 6,243 | CONFIRMED (hold) | "För 2026 ... 6 243 kronor per månad" | SE22 |
| 47 Form 124011 | CONFIRMED (hold) | raw text | SE21 |
| 48 next_check_due 2027-01-15 | CONFIRMED (logic) | Base amounts, egenavgifter and fees reset each 1 Jan; verksamt fee pages were updated 29 Sep 2026; date is sensible | n/a |
| A1 Property as capital valued by "an authorised auditor" | CORRECTED | Verksamt: "godkänd eller auktoriserad revisor" (approved or authorised); statute 2 kap. 19 § says only "en revisor". Wording fixed in guide and faq se-ab-capital | SE2, SE5, SE8 |
| A2 F-skatt refusal "seriously failed" | CORRECTED | Page: failed to report or pay "beyond a trivial extent" (not "seriously"); a further ground (unpaid ROT/RUT/green-technology repayment claim) was missing | SE12 |
| A3 Samordningsnummer | CORRECTED | Page also requires showing a connection to Sweden and a need for the number, and an in-person identity check; added | SE14 |
| A4 VAT representative | CORRECTED | Page: non-EU businesses must be represented by a Sweden-based agent; SKV 5703 or another document; the softer "generally" wording replaced | SE13 |
| A5 Invoice content list | CONFIRMED | date, serial number, seller VAT number, names/addresses, goods/services, VAT rate and amount; same for paper and electronic | SE15 |
| A6 Name protection county vs nationwide; auditor optional | CONFIRMED | sole trader and handelsbolag county; AB nationwide; "Det flesta nystartade aktiebolag kan välja att inte ha revisor" | SE1, SE2, SE3 |
| A7 Board members personally liable (unpaid taxes); half of capital used up | CONFIRMED | quoted | SE2 |
| A8 Handelsbolag always bookkeeping-obliged; AB annual report; owner treated as employee | CONFIRMED | "Aktiebolag, handelsbolag och ekonomiska föreningar är alltid bokföringsskyldiga"; "Bolagsverket registrerar inte vilka som är aktieägare" | SE16, SE2 |

Counts (55 claims checked: 47 numbered rows + A1 to A8; row 48 is editorial): CONFIRMED 49, CORRECTED 6 (rows 6, 7, A1, A2, A3, A4), UNSUPPORTED 0, CONFLICT 0, COULD NOT OPEN 0 for rendered claims. Not checkable and still held: the Bolagsverket "about 7 working days" processing time (company-steps, confidence hold, no source; Bolagsverket pages not opened by me either).

Other checks. Visa section hold:true with all claims confidence hold: yes. quick_facts has exactly structure/cost/time/gotcha: yes. No sentence promises speed or certainty: none found. Source type tags: verksamt.se pages "government_portal" and all others "official" are right; no rendered claim rests on a secondary source. Every annual figure shows its year (2026 / 2025 and 2026 / "October 2026"); the SEK 120,000 and SEK 3 million figures, which are not annual rates but carry no date on the page, now say "as shown October 2026". The Migrationsverket page (SE21) does show a news item that stricter conduct (vandel) rules apply from 13 July, so that open question is partly answered (details unread; visa is held anyway).

Changes made by this check.
guide.json:
1. quick_facts.cost source_ids: SE2, SE3, SE5 changed to SE1, SE2, SE5, SE7.
2. legal-forms fee claim source_ids: added SE1.
3. legal-forms capital claim: "authorised auditor" changed to "approved or authorised auditor".
4. f-skatt-and-ids refusal claim: "seriously failed" changed to "failed ... to more than a trivial extent"; ROT/RUT/green-technology ground added.
5. f-skatt-and-ids samordningsnummer claim: connection-to-Sweden condition and in-person identity check added.
6. company-steps step 4 (board): statute rule added (deputy needed if fewer than three members); SE8 added to source_ids.
7. taxes: "(limit as shown by Skatteverket in October 2026)" added to the VAT exemption; "(as shown October 2026)" added to the SEK 3 million rule.
8. taxes VAT representative: reworded to "must appoint a VAT representative based in Sweden (form SKV 5703, or another document ...)".
9. visa open_questions_for_reviewer item 3: vandel update (hold section, not rendered).
faq.json: se-ab-capital auditor wording; se-sole-cost source_ids SE3, SE6 changed to SE1, SE3, SE6.
sources.json: unchanged (no new source opened beyond the existing 23).
