-- Kerala Founders: find listings whose public description contains an internal
-- research note (for example "Research confidence: ... could be confirmed from
-- public sources.").
--
-- READ-ONLY. This only looks; it changes nothing. Safe to run any time in
-- phpMyAdmin (SQL tab) against the live database.
--
-- How to read the result: one row per affected listing. "note_found" shows the
-- text from the first matching phrase to the end of the description, so you can
-- see exactly what visitors read. If the list looks right, ask for the cleanup
-- file (a separate UPDATE that removes only that sentence) and run it after
-- checking these rows.
--
-- Phrases searched (case-insensitive): "research confidence", "confidence:",
-- "could be confirmed", "from public sources", "partially verified",
-- "research sprint", "no founder or owner name". Add more at the bottom if you
-- spot other wording.

SELECT
  id,
  slug,
  name,
  country,
  status,
  verified,
  SUBSTRING(
    description,
    GREATEST(1, LEAST(
      IFNULL(NULLIF(LOCATE('research confidence', LOWER(description)), 0), 99999),
      IFNULL(NULLIF(LOCATE('confidence:', LOWER(description)), 0), 99999),
      IFNULL(NULLIF(LOCATE('could be confirmed', LOWER(description)), 0), 99999),
      IFNULL(NULLIF(LOCATE('from public sources', LOWER(description)), 0), 99999),
      IFNULL(NULLIF(LOCATE('partially verified', LOWER(description)), 0), 99999),
      IFNULL(NULLIF(LOCATE('research sprint', LOWER(description)), 0), 99999),
      IFNULL(NULLIF(LOCATE('no founder or owner name', LOWER(description)), 0), 99999)
    ))
  ) AS note_found,
  description AS full_description
FROM companies
WHERE LOWER(description) LIKE '%research confidence%'
   OR LOWER(description) LIKE '%confidence:%'
   OR LOWER(description) LIKE '%could be confirmed%'
   OR LOWER(description) LIKE '%from public sources%'
   OR LOWER(description) LIKE '%partially verified%'
   OR LOWER(description) LIKE '%research sprint%'
   OR LOWER(description) LIKE '%no founder or owner name%'
ORDER BY name;
