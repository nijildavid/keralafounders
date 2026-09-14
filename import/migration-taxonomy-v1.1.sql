-- Taxonomy v1.1: hierarchical layer under the frozen Industry list.
--
-- Adds industry_detail: an OPTIONAL, unconstrained free-text field that sits
-- under the frozen top-level Industry (e.g. Industry=Professional Services,
-- industry_detail="Financial consultancy"). It is not itself frozen — new
-- values can be added here without ever touching the 9(+1)-category list.
--
-- Also adds 'Finance' as a 10th top-level Industry (a genuine missing major
-- category per the plan's Section 8 review triggers, not an ad hoc add).
--
-- Backfill: every existing company's `description` still starts with its
-- pre-freeze detailed industry text ("<detail> company founded by/in ..."),
-- since only `industry`/`business_type` were touched in the v1.0 migration.
-- This recovers that original detail with zero new guessing.

ALTER TABLE companies ADD COLUMN IF NOT EXISTS industry_detail VARCHAR(160) NULL AFTER business_type;

UPDATE companies
SET industry_detail = REGEXP_REPLACE(description, ' company (founded by|in).*$', '')
WHERE industry_detail IS NULL;

-- Some recovered values are themselves generic leftovers from the old free-text
-- list ("Professional Services", "Retail / Consumer", "Food / Consumer", "Other")
-- and add zero information beyond the new Industry column — for a few companies
-- (e.g. Seal Tech, Mercado 360/Ayurvod) they actively contradict the v1.0
-- reclassification. Blank those out rather than show a redundant or misleading detail.
UPDATE companies
SET industry_detail = NULL
WHERE industry_detail IN ('Professional Services', 'Retail / Consumer', 'Food / Consumer', 'Other');
