-- Add your company, Phase 1: Kerala-connection context, Instagram, the
-- podcast/stories contact-permission checkbox, and basic spam protection
-- for submit-company.php (which had none before this). Additive only.
-- Run this once against an already-seeded database (schema.sql itself now
-- includes these columns too, for anyone loading a fresh database).

ALTER TABLE companies
  ADD COLUMN kerala_connection VARCHAR(60) NULL AFTER industry_detail,
  ADD COLUMN kerala_district VARCHAR(60) NULL AFTER kerala_connection,
  ADD COLUMN instagram VARCHAR(120) NULL AFTER website,
  ADD COLUMN contact_ok_podcast_stories TINYINT(1) NOT NULL DEFAULT 0 AFTER instagram,
  ADD COLUMN contact_permission_at TIMESTAMP NULL AFTER contact_ok_podcast_stories,
  ADD COLUMN ip_hash CHAR(64) NULL AFTER outreach_responded_at,
  MODIFY COLUMN location VARCHAR(255) NULL,
  ADD INDEX idx_ip_hash_created (ip_hash, created_at);
