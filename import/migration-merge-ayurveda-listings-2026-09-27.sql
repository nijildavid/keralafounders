-- KeralaFounders.eu — merge duplicate Ayurveda listings (Austria)
-- Generated 2026-09-27, at Nijil's request after reviewing the two rows
-- side by side. karinkada-ayurveda and sonnentag-kerala-ayurveda-shop were
-- independently flagged as the same business by two separate research
-- passes (2026-09-18 email enrichment, 2026-09-27 Instagram backfill):
-- same website (keralaayurvedashop.at), same contact email
-- (office@sonnentag.at), same director, same Instagram handle.
--
-- Judgment call made here, easy to reverse before running: this script
-- keeps `sonnentag-kerala-ayurveda-shop` as the surviving slug and removes
-- `karinkada-ayurveda`. Reasoning — "sonnentag-kerala-ayurveda-shop"
-- matches the live website's own name and domain, so it's the more
-- recognizable listing for a visitor searching for this shop. If you'd
-- rather keep `karinkada-ayurveda` instead, swap the two slug literals in
-- the two SET lines directly below before running this file — nothing
-- else needs to change.
--
-- What this does:
--   1. Re-points any founders/branches/claim-request rows from the row
--      being removed onto the row being kept, so nothing is lost. If the
--      same founder was independently entered on both original listings,
--      this can leave a duplicate founder row on the merged listing —
--      this script doesn't try to detect that; check
--      admin-edit.php?id=<kept id> afterward and remove any duplicate
--      founder entry by hand.
--   2. Fills in any field that's empty on the kept row from the row being
--      removed (never overwrites something already on file), keeps the
--      longer of the two descriptions, and OKs verified/podcast-consent
--      flags if either row had them set.
--   3. Sets the Instagram handle directly (rather than relying on
--      migration-instagram-backfill-2026-09-27.sql, so this merge is
--      self-contained regardless of which script runs first).
--   4. Deletes the now-empty duplicate row.
--
-- Known cost, accepted rather than built around: karinkada-ayurveda's URL
-- (company.php?id=karinkada-ayurveda) will 404 after this runs — there's
-- no slug-redirect mechanism in this codebase today, and this listing's
-- traffic doesn't seem to justify building one for a single merge.
--
-- Requires migration-add-instagram-provenance.sql to have already been
-- run (adds instagram_source / instagram_confidence / instagram_note).
-- Safe to run whether or not migration-instagram-backfill-2026-09-27.sql
-- has already run, and safe to re-run if karinkada-ayurveda no longer
-- exists (every statement below is a no-op once it's gone).

START TRANSACTION;

SET @keep_slug = 'sonnentag-kerala-ayurveda-shop';
SET @remove_slug = 'karinkada-ayurveda';

-- Check this returns exactly one row for each slug before going further.
SELECT
  (SELECT COUNT(*) FROM `companies` WHERE `slug` = @keep_slug) AS keep_slug_found,
  (SELECT COUNT(*) FROM `companies` WHERE `slug` = @remove_slug) AS remove_slug_found;

SET @keep_id = (SELECT id FROM `companies` WHERE `slug` = @keep_slug);
SET @remove_id = (SELECT id FROM `companies` WHERE `slug` = @remove_slug);

UPDATE `companies` AS keep
JOIN `companies` AS rem ON rem.id = @remove_id
SET
  keep.website = COALESCE(NULLIF(keep.website, ''), rem.website),
  keep.instagram = 'keralaayurvedashop',
  keep.instagram_source = 'research',
  keep.instagram_confidence = 'high',
  keep.instagram_note = NULL,
  keep.industry_detail = COALESCE(NULLIF(keep.industry_detail, ''), rem.industry_detail),
  keep.kerala_connection = COALESCE(NULLIF(keep.kerala_connection, ''), rem.kerala_connection),
  keep.kerala_district = COALESCE(NULLIF(keep.kerala_district, ''), rem.kerala_district),
  keep.size = COALESCE(NULLIF(keep.size, ''), rem.size),
  keep.founded_year = COALESCE(keep.founded_year, rem.founded_year),
  keep.location = COALESCE(NULLIF(keep.location, ''), rem.location),
  keep.description = CASE WHEN CHAR_LENGTH(rem.description) > CHAR_LENGTH(keep.description) THEN rem.description ELSE keep.description END,
  keep.verified = GREATEST(keep.verified, rem.verified),
  keep.contact_ok_podcast_stories = GREATEST(keep.contact_ok_podcast_stories, rem.contact_ok_podcast_stories),
  keep.contact_email = COALESCE(NULLIF(keep.contact_email, ''), rem.contact_email),
  keep.email_type = COALESCE(NULLIF(keep.email_type, ''), rem.email_type),
  keep.email_source = COALESCE(NULLIF(keep.email_source, ''), rem.email_source),
  keep.email_confidence = COALESCE(NULLIF(keep.email_confidence, ''), rem.email_confidence),
  keep.email_source_url = COALESCE(NULLIF(keep.email_source_url, ''), rem.email_source_url)
WHERE keep.id = @keep_id;

UPDATE `founders` SET `company_id` = @keep_id WHERE `company_id` = @remove_id;
UPDATE `branches` SET `company_id` = @keep_id WHERE `company_id` = @remove_id;
UPDATE `claim_requests` SET `company_id` = @keep_id WHERE `company_id` = @remove_id;

DELETE FROM `companies` WHERE `id` = @remove_id;

-- Validation — check by eye before committing:
--   1. merged_company should show one row, the surviving listing, with
--      website/instagram/etc. filled in.
--   2. karinkada_row_gone should be 0.
--   3. merged_founders lists every founder now on the merged listing —
--      look for an accidental duplicate (same person listed twice).
SELECT `id`, `slug`, `name`, `website`, `instagram`, `verified`, `status`, `description`
  FROM `companies` WHERE `slug` = @keep_slug;
SELECT COUNT(*) AS karinkada_row_gone FROM `companies` WHERE `slug` = @remove_slug;
SELECT `name`, `email`, `linkedin` FROM `founders` WHERE `company_id` = @keep_id;

COMMIT;
