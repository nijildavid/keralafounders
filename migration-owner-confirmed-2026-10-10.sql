-- Adds the "Owner confirmed" badge flag. Run in phpMyAdmin BEFORE deploying the
-- code that uses it. Safe to run once; it does not change anything visible by itself.
ALTER TABLE companies ADD COLUMN owner_confirmed TINYINT(1) NOT NULL DEFAULT 0 AFTER verified;

-- STEP 2 (review first): which verified companies have a resolved claim?
-- These are the candidates for "Owner confirmed". Check the list looks right.
-- SELECT c.id, c.name, cr.claimant_name, cr.claimant_email, cr.created_at
--   FROM companies c JOIN claim_requests cr ON cr.company_id = c.id
--   WHERE c.verified = 1 AND cr.status = 'resolved' ORDER BY c.name;

-- STEP 3: once the list is right, mark exactly those companies.
-- UPDATE companies c SET c.owner_confirmed = 1
--   WHERE c.verified = 1
--     AND EXISTS (SELECT 1 FROM claim_requests cr WHERE cr.company_id = c.id AND cr.status = 'resolved');
