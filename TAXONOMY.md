# Kerala Founders — Taxonomy

Current version: **v1.1**. Governed per the Taxonomy Improvement & Governance
Plan — improve deliberately at named checkpoints, not ad hoc, per Section 8.

## Industry (10, as of v1.1)

Technology · Healthcare · Food & Hospitality · Professional Services ·
Construction & Trades · Logistics & Transport · Education · Real Estate ·
Retail & E-commerce · **Finance**

## Business Type (10)

Startup · SME / Local Business · Restaurant · Café · Consultancy ·
Medical Practice · Community Organisation · Association · Non-profit ·
Professional Practice

## Specific type (new in v1.1 — hierarchical layer, not frozen)

An **optional, unconstrained** free-text field under Industry
(`companies.industry_detail`). It exists so genuine variety within an
Industry doesn't force a new top-level category every time something
doesn't fit neatly — e.g. Industry=Professional Services,
Specific type="Financial consultancy" — without ever touching the frozen
10-category list. Not shown as a filter or a card chip (deliberately
low-commitment); shown on the company detail page under Industry, and
included in directory search.

Backfilled for all 120 existing companies for free: each one's
`description` still started with its pre-v1.0 detailed industry text
("<detail> company founded by/in ..."), since only `industry` and
`business_type` were touched in the v1.0 migration — so the original
specificity was recovered by extraction, not re-guessed. 17 of those
recovered values were themselves generic leftovers ("Professional
Services", "Retail / Consumer", "Food / Consumer") that added nothing
beyond the new Industry column, or in 2 cases (Seal Tech, Mercado 360 /
Ayurvod) actively contradicted the new classification — those were
blanked out rather than shown as misleading detail. 103 of 120 companies
now carry real added specificity.

## v1.0 → v1.1 changes

- Added **Finance** as a 10th Industry. Confirmed missing during
  real-world testing (a finance company had nowhere to go) — a genuine
  gap per Section 8's review triggers, not an ad hoc add.
- Added the `industry_detail` hierarchical field described above.
- Ran the Section 6 real-data validation stress test (below) against the
  now-10 Industries before considering the list settled again.

## Section 6 stress test results

Tested the 10 Industries against the plan's own validation list, plus the
two real cases that prompted this update:

| Business type tested | Fits an existing Industry? | Notes |
|---|---|---|
| Restaurants, cafés, catering | Yes — Food & Hospitality | Already proven (57 real companies) |
| Doctors, clinics, healthcare | Yes — Healthcare | |
| Technology companies | Yes — Technology | |
| Consultants, lawyers, accountants | Yes — Professional Services | |
| Construction and trades | Yes — Construction & Trades | |
| Logistics, import/export | Yes — Logistics & Transport | |
| Recruitment and immigration | Yes — Professional Services | |
| Retail and e-commerce | Yes — Retail & E-commerce | |
| Real estate | Yes — Real Estate | Category exists, 0 companies so far |
| Education | Yes — Education | |
| Finance | **No → added as 10th Industry** | |
| Car painting/customizing studio | Yes — Construction & Trades | Business Type: SME / Local Business |
| Travel agencies | Soft fit — Professional Services | Booking/advisory service, not a distinct sector; revisit if volume grows |
| Community organisations, associations, non-profits | **Structural friction, not a missing Industry** | These are Business Types (already exist) but don't pair cleanly with any Industry — a cultural association doesn't really have a "sector." Classify by primary activity per the plan's own rule (e.g. an event-running cultural association → Education) until real examples show a better pattern. |

Only one genuine top-level gap (Finance). The community-org pairing issue
is logged below for the next real checkpoint, not fixed speculatively.

## Current distribution (120 companies)

| Industry | Count |
|---|---|
| Food & Hospitality | 57 |
| Professional Services | 27 |
| Technology | 11 |
| Retail & E-commerce | 10 |
| Healthcare | 9 |
| Education | 3 |
| Construction & Trades | 2 |
| Logistics & Transport | 1 |
| Real Estate | 0 |
| Finance | 0 (new, no companies yet) |

| Business Type | Count |
|---|---|
| Restaurant | 46 |
| SME / Local Business | 29 |
| Consultancy | 24 |
| Startup | 13 |
| Café | 4 |
| Professional Practice | 3 |
| Medical Practice | 1 |
| Community Organisation / Association / Non-profit | 0 |

## Taxonomy Issue Log

Per the plan's Section 5 — genuine gaps to revisit at the next checkpoint,
not one-off oddities to patch now.

| Business | Current classification | Problem | Suggested change | Frequency | Decision |
|---|---|---|---|---|---|
| CAT Entertainments, Girelle Production, Insider Publications / Athens Insider | Professional Services | Media/entertainment/publishing doesn't fit any Industry — forced into "Professional Services" as closest | Add "Media & Entertainment" if this segment grows | 3 so far | Defer |
| Karinkada Ayurveda GmbH | Retail & E-commerce | It's a manufacturer of consumer products, not a retailer — no "Manufacturing" Industry exists | Add "Manufacturing" if more product-makers join | 1 so far | Defer |
| Hire and Care DP GmbH | Professional Services / Consultancy | Named as "HR Tech" — could be a software platform (Technology/Startup) rather than a staffing agency | Reclassify once confirmed | 1 | Keep, revisit |
| Chef Michael Paul Zachariah | Food & Hospitality / Restaurant | Likely a private chef/catering individual, not a restaurant | Confirm actual business model | 1 | Keep, revisit |
| FOOD PACK d.o.o. | Food & Hospitality / SME / Local Business | Name suggests packaged-goods distribution, not dine-in | Flagging the call, no action needed | 1 | Keep |
| Community orgs / associations / non-profits generally | N/A — no examples yet | Business Type exists but has no natural Industry pairing (see stress test above) | Watch for a real example, decide pattern then | 0 so far | Defer |
| ~9 generic-named holding companies (AVN Kerala, AYAAN, Euro Middle East, EUROXIS, Instamat Cyprus, Golden Ratio, JPN International, Ayush Consultants, Sandeep Soman) | Professional Services / Consultancy | Classified from company name alone with no real distinguishing detail — "Consultancy" is a default guess, not a confirmed fact for most of these | Confirm actual business model for each when you have the chance | 9 | Keep, low confidence |

## Not in scope

Tags (city/origin, founder-led, women-led, B2B/B2C, etc.) are explicitly
excluded per the plan — introduce them later once enough records exist to
show which tags are actually useful.
