-- Fix: the public page for "KPH / Kerala Product Hunt Netherlands community"
-- shows an internal team note as its description:
--   "Relevant ecosystem lead but insufficient evidence of a Netherlands
--    business/entity. Do not import as a Dutch company yet."
-- Run ONCE by Nijil in phpMyAdmin (cPanel), against the live database.
-- NOT run by Claude. Pick ONE option below and delete the other.
--
-- Step 0 (check first, changes nothing): confirm the row and its current text.
--   SELECT id, slug, status, description FROM companies
--   WHERE slug = 'kph-kerala-product-hunt-netherlands-community';
-- It should return exactly 1 row. If it returns 0 rows, stop: the slug differs.

-- OPTION A (recommended): hide the listing until there is evidence it is a
-- real Dutch business. 'pending' = not shown on the public site; nothing is
-- deleted and it can be switched back later.
UPDATE companies
SET status = 'pending'
WHERE slug = 'kph-kerala-product-hunt-netherlands-community';

-- OPTION B: keep it visible with a plain description. Edit the wording so it
-- says only what you can stand behind, then remove the comment marks.
-- UPDATE companies
-- SET description = 'REPLACE WITH ONE PLAIN SENTENCE ABOUT THE COMMUNITY'
-- WHERE slug = 'kph-kerala-product-hunt-netherlands-community';
