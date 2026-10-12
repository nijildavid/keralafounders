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
