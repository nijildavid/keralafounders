-- Adds storage for the Stories page's "notify me" email capture.
-- Run this once against an already-seeded database (schema.sql itself now
-- includes this table too, for anyone loading a fresh database).

CREATE TABLE IF NOT EXISTS story_signups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  ip_hash CHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_email (email),
  INDEX idx_ip_hash_created (ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
