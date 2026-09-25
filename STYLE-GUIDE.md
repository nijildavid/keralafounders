# Kerala Founders — Front-End Style Guide

Reference for CSS conventions in `public/assets/style.css`. Frozen-ish, like
`TAXONOMY.md` — add to it as new patterns emerge, but don't rewrite existing
hardcoded values just to match it (see "Adoption policy" below).

## Spacing scale

Defined in `style.css`'s `:root` (top of file):

| Token | Value |
|---|---|
| `--space-1` | 4px |
| `--space-2` | 8px |
| `--space-3` | 12px |
| `--space-4` | 16px |
| `--space-5` | 20px |
| `--space-6` | 24px |
| `--space-7` | 32px |
| `--space-8` | 40px |
| `--space-9` | 48px |

## Border-radius scale

| Token | Value | Matches |
|---|---|---|
| `--radius-sm` | 10px | `.field`/`.select`/`.textarea` |
| `--radius-md` | 14px | `.category-card` |
| `--radius-lg` | 18px | `.company-card`, `.panel`, `.company-detail` |
| `--radius-xl` | 22px | `.map-panel`, `.ecosystem-map` |
| `--radius-pill` | 999px | `.pill`, chips, badges |

## Breakpoints (written convention, not CSS variables)

CSS custom properties can't be used inside `@media` conditions (no build
step to work around that), so this is a naming convention only:

- **600px** — phone. The main mobile breakpoint.
- **900px** — tablet.
- **1150px** — nav-overflow only. Specific to the header's editorial nav
  cards not fitting above this width; not a general-purpose breakpoint.
- **700px** — legacy, non-canonical. Appears in a handful of older rules.
  Don't use it for new work; use 600 or 900 instead.

## Adoption policy

These tokens are **additive**. Existing hardcoded px values throughout
`style.css` were **not** mass-migrated onto them — that would be a large,
purely cosmetic change with no visible benefit and real risk of
regressions. Use the tokens for **new or already-being-touched** CSS going
forward; don't go rewrite unrelated rules just to use a token.

## See also

- `CLAUDE.md` — hard constraints (PHP 7.4, no build step, `--accent2` for
  orange text, etc.) and repo conventions.
