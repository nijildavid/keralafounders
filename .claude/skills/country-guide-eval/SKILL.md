---
name: country-guide-eval
description: Run the Country Guide Standard evals on a draft country guide in drafts/country-guides/<slug>/ before it is merged or published. Use when asked to check, eval, QA or approve a country guide, or before moving a guide live.
---

# Country guide eval

Source of truth: `COUNTRY-GUIDE-STANDARD.md` (approved by Nijil on 14 Oct 2026).
A guide is "ready for review" only when all five QA checks pass. Nothing goes live without Nijil's approval of that specific publish.

## Run it

1. **Structure, source and wording checks (automatic).**
   `php scripts/validate-guide-drafts.php <slug>` (or no slug for every draft).
   It merges the draft into a temp copy of the live content, runs `scripts/validate-guidance-content.php`, and fails on: missing sections, quick facts not exactly structure/cost/time/gotcha, fewer than 6 or more than 10 checklist items, fewer than 5 FAQ entries, unknown or duplicate source IDs, a rendered claim backed only by secondary sources, "reviewed by" / "legal review" / "we guarantee" wording, a permit (`visa`) section that is not held or has no official https link in `hold_note_source_id`, and a missing "Independent check" section in `evidence-log.md`.
   It also prints a WARN for rendered figures with no 20xx year. Fix or justify each WARN.
2. **Independent re-verification (QA 4).** Spawn an agent that did not write the draft, with `independent-check-prompt.md` (fill in country, slug, branch). It re-opens every source, corrects or holds what it cannot confirm, and appends an "Independent check" section to `evidence-log.md`. Author and checker are never the same agent.
3. **Reader check (QA 5).** Load the page locally (`php -S 127.0.0.1:8000 -t public`) at about 390 px width. No sideways scroll, citations jump to the source they name, the permit section shows only the hold note and an official link, no sentence implies legal review or advice.
4. **Report** per country: figures checked, confirmed / corrected / unsupported / conflict counts, held claims, what still needs a person with a browser, and the WARN list. Do not call anything "reviewed". Use: "Checked against official sources on <date>. General information, not legal or tax advice."

## Never

- Edit `public/` content or the hub status as part of an eval. Moving a guide live is a separate, approved step.
- Email, post or contact anyone.
- Touch `public/assets/logo.svg`.
