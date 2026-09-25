-- Adds storage for the Guidance section's "Was this useful?" feedback widget.
-- Run this once against an already-seeded database (schema.sql itself now
-- includes this table too, for anyone loading a fresh database).

CREATE TABLE IF NOT EXISTS guidance_feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  target_type ENUM('guide','section','faq') NOT NULL,
  target_id VARCHAR(120) NOT NULL,
  vote ENUM('up','down') NOT NULL,
  comment TEXT NULL,
  ip_hash CHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_target (target_type, target_id),
  INDEX idx_ip_hash_created (ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
