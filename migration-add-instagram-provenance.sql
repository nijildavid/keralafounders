-- Kerala Founders: track where an Instagram handle came from, so a
-- founder-submitted value always wins over a research-sourced guess and a
-- medium-confidence guess can be held back from the public profile.
-- Additive only, safe to run regardless of production's current row/id state.

ALTER TABLE companies
  ADD COLUMN instagram_source VARCHAR(20) NULL AFTER instagram,
  ADD COLUMN instagram_confidence VARCHAR(20) NULL AFTER instagram_source,
  ADD COLUMN instagram_note VARCHAR(255) NULL AFTER instagram_confidence;
