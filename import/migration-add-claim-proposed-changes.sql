-- Kerala Founders: support structured claim edits (not just a free-text message)
-- Additive only, safe to run regardless of production's current row/id state.

ALTER TABLE claim_requests
  ADD COLUMN proposed_changes TEXT NULL AFTER message;
