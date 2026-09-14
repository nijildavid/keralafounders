-- Kerala Founders: add contact-enrichment + outreach tracking fields to companies
-- Additive only, safe to run regardless of production's current row/id state.

ALTER TABLE companies
  ADD COLUMN contact_email VARCHAR(255) NULL AFTER created_at,
  ADD COLUMN email_type VARCHAR(60) NULL AFTER contact_email,
  ADD COLUMN email_source VARCHAR(120) NULL AFTER email_type,
  ADD COLUMN email_confidence VARCHAR(20) NULL AFTER email_source,
  ADD COLUMN email_source_url VARCHAR(500) NULL AFTER email_confidence,
  ADD COLUMN outreach_status ENUM('not_contacted','sent','responded') NOT NULL DEFAULT 'not_contacted' AFTER email_source_url,
  ADD COLUMN outreach_sent_at TIMESTAMP NULL AFTER outreach_status,
  ADD COLUMN outreach_responded_at TIMESTAMP NULL AFTER outreach_sent_at;
