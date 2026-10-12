# Country Guide Standard — DRAFT v0.1 (CG-01)

Status: **draft for Nijil's approval.** Written 12 Oct 2026 from the six live
guides (Germany, Netherlands, Spain, Belgium, Greece, Slovenia) and
`scripts/validate-guidance-content.php`. Nothing here changes the live site.
Until Nijil approves, every guide written to it is a draft in `drafts/`.

## 1. Purpose
A Guidance guide helps a Kerala-origin founder understand how to start and run a
company in one EU country. It explains the rules and points to the official
source. It is **general information, not legal or tax advice.**

## 2. Required content (one guide = one `guide.json`)
| Part | Rule |
|---|---|
| `title`, `summary` | "How to start a company in <Country>". Summary names the main routes (sole trader / limited company) and the one-stop shop if there is one. |
| `quick_facts` | Exactly four: `structure`, `cost`, `time`, `gotcha`. Each has `source_ids`. |
| `founder_checklist` | 6 to 10 action steps; each links to a section by `section_id`. |
| Section `overview` | The big picture, plain language, marked editorial (no figures that are not cited elsewhere in the guide). |
| Section `legal-forms` | Comparison table: form, minimum capital, liability, notary, typical setup time, bookkeeping, status tag. |
| Country-specific section(s) | Only where the country has a trap founders hit (for example freelance vs trade, regulated professions, a flat-rate regime). 0 to 2 sections. |
| Section `sole-steps` | Steps to register as a sole trader / freelancer. |
| Section `company-steps` | Steps to form the main limited company. |
| Section `taxes` | VAT rate and small-business threshold, income or corporate tax basics, invoicing/e-invoicing rules in force. |
| Section `social` | Social security for the self-employed and for company directors. |
| Section `visa` | **Non-EU residence permit.** Always present. Written in full, but `hold: true` until CG-06 defines "reviewed" and Nijil lifts it. See section 5. |
| FAQ | At least 5 entries per country in `faq.json`, each with a source and `last_checked`. |

## 3. Source rules
1. **Official first.** A fact is sourced from the authority that owns the rule: government portal, company register, tax authority, social-security body, immigration authority. Chambers of commerce are tagged `chamber`; government one-stop portals `government_portal`.
2. **Read in the original language** where the official page exists in it. English pages of an authority can lag the national-language page. Record the language (`language`) on the source.
3. **Secondary sources are allowed only to explain, never alone to state a figure.** A secondary source (advisor, company blog, news) is tagged `secondary`. A claim that rests only on secondary sources is `confidence: "hold"` (not rendered) or is dropped.
4. **Conflicts are shown, not hidden.** If two sources disagree, keep the official national-language one, and record the conflict in the evidence log and as a `hold` claim for the reviewer.
5. **Source IDs** use a country prefix (`PL1`, `PL2`, ...). Each source has `title`, `publisher`, `type`, `url`, `language`, `accessed` (date it was read).
6. **No statement of speed or certainty the source does not make** ("same day", "guaranteed"). Quote the source's range.

## 4. Dates and figures
- **Every annual figure carries its year** in the text ("EUR 60,000 (2026)" or "since 1 Jan 2025"). Fees, thresholds, minimum wages and social-security bases are annual.
- Each guide has `last_checked` and `next_check_due`; `next_check_due` is at most `last_checked` + 6 months. If a figure resets on a known date (for example 1 January), set the re-check for then.
- Figures live in the claim text with their source IDs. No figure appears unsourced.

## 5. Non-EU residence-permit section (`visa`)
- Always present, in every guide, even when held.
- Content: who needs a permit, which routes exist for founders and self-employed, the official link, the steps, the costs and the processing time, each dated and sourced to the immigration authority.
- `hold: true`, `review_status: "pending"` and `hold_note` in the standard wording until Nijil lifts it. The `internal_notes` and `open_questions_for_reviewer` fields list what the reviewer must confirm.
- The text agent-drafted for this section is labelled **"agent review, not legal advice"** in `internal_notes`. It may not be described as "reviewed by a lawyer" (CG-06 decides what "reviewed" may mean).

## 6. FAQ rules
At least 5 entries. Questions are the ones a Kerala-origin founder actually asks (can I start remotely, how much does it cost, VAT, social security, do I need a bank account first, permit). Each answer is 1 to 3 sentences, has `source_ids`, `last_checked`, `origin: "research"`, and `status: "render"` (or `"hold"` for visa answers).

## 7. The five QA checks (a guide is not "ready for review" until all pass)
1. **Structure check.** `php scripts/validate-guide-drafts.php` passes: valid JSON, every `source_id` exists, every rendered claim has a source, dates follow the 6-month rule, required sections present, 4 quick facts, 5 or more FAQ.
2. **Source check.** Every figure traces to an official source (or is held). Secondary-only claims are held. Original-language pages were used where they exist. Each source has an accessed date.
3. **Dating check.** Every annual figure shows its year. Fee-driven guides have a January re-check date.
4. **Independent re-verification.** A second agent that did not write the draft re-opens every number, threshold and deadline at its source, logs the result in `evidence-log.md`, and lists disagreements. Draft author and checker are never the same agent.
5. **Reader check.** The rendered page was read on a phone width and by keyboard; citations open the source they name; the visa section is held; no sentence implies legal review, advice or a guarantee.

## 8. Publishing gates
Draft → agent re-verification (QA 4) → legal pass labelled per CG-06 → design/mobile check → **Nijil approves the publish** (CG-13). Only then a country's hub status changes from `planned` and its files move from `drafts/` into `public/content/guidance/`.

## 9. Open points for Nijil
- Is a 4-fact `quick_facts` strip and a held `visa` section in every guide what you want? (Matches the six live guides.)
- Should a draft be allowed to cite an English-only official page when no national-language page exists? (Standard says yes, flagged.)
- CG-06 must still define what "reviewed" may mean.
