-- Kerala Founders: add a "rejected" status for company submissions.
--
-- WHAT IT DOES: lets the admin "Reject" button mark a pending submission as
-- rejected instead of deleting it. Rejected companies are hidden from the public
-- site (it only shows 'approved'), stay visible in admin under Status = Rejected,
-- and can be restored to pending. Existing rows are not changed.
--
-- WHEN TO RUN: once, in phpMyAdmin, before (or right after) merging the
-- quick approve/reject change. Until it is run, the Reject buttons stay hidden;
-- nothing breaks. Safe to re-run: it just re-applies the same column definition.
--
-- UNDO: ALTER TABLE companies MODIFY status ENUM('pending','approved') NOT NULL DEFAULT 'pending';
--       (only after restoring or deleting any rejected rows, otherwise MySQL refuses).

ALTER TABLE companies
  MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending';

-- Check afterwards (should list pending / approved / rejected with counts):
-- SELECT status, COUNT(*) FROM companies GROUP BY status;
