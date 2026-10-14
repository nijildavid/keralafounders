# Country guide drafts

Drafts written to `COUNTRY-GUIDE-STANDARD.md`. **Not served by the site** (this
folder is outside `public/`) and **not live**. Nothing here changes the hub.

One folder per country:
- `guide.json` — same schema as `public/content/guidance/<slug>/guide.json`
- `sources.json` — new sources only, IDs prefixed per country (`PL1`...)
- `faq.json` — new FAQ entries only
- `hub-entry.json` — the country's hub fields (`last_checked`, `next_check_due`)
- `evidence-log.md` — each figure: value, source ID, how verified, date, checker
- `open-questions.md` — what a human or lawyer must confirm

Check a draft: `php scripts/validate-guide-drafts.php [slug]`.
Moving a draft live (copy files into `public/content/guidance/`, set hub status)
needs Nijil's approval (CG-13).
