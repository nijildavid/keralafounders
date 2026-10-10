-- Adds storage for the "Know a Malayali business?" suggestion form (Growth C2).
-- Run this once against an already-seeded database (schema.sql itself now
-- includes this table too, for anyone loading a fresh database).
-- No personal data about people: only a business name, city, a public
-- website/Instagram link, an "I own it" tick, and a hashed IP for rate limiting.

CREATE TABLE IF NOT EXISTS company_suggestions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  business_name VARCHAR(200) NOT NULL,
  city VARCHAR(120) NOT NULL,
  link VARCHAR(300) NOT NULL,
  is_owner TINYINT(1) NOT NULL DEFAULT 0,
  handled TINYINT(1) NOT NULL DEFAULT 0,
  ip_hash CHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_handled_created (handled, created_at),
  INDEX idx_ip_hash_created (ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
