CREATE TABLE IF NOT EXISTS companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  website VARCHAR(255) NULL,
  industry VARCHAR(80) NOT NULL,
  business_type VARCHAR(60) NOT NULL DEFAULT '',
  industry_detail VARCHAR(160) NULL,
  size VARCHAR(20) NULL,
  founded_year SMALLINT NULL,
  country VARCHAR(80) NOT NULL,
  city VARCHAR(80) NOT NULL,
  location VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  verified TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contact_email VARCHAR(255) NULL,
  email_type VARCHAR(60) NULL,
  email_source VARCHAR(120) NULL,
  email_confidence VARCHAR(20) NULL,
  email_source_url VARCHAR(500) NULL,
  outreach_status ENUM('not_contacted','sent','responded') NOT NULL DEFAULT 'not_contacted',
  outreach_sent_at TIMESTAMP NULL,
  outreach_responded_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS founders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(255) NULL,
  linkedin VARCHAR(255) NULL,
  show_email TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS branches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  country VARCHAR(80) NOT NULL,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS claim_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  claimant_name VARCHAR(160) NOT NULL,
  claimant_email VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  proposed_changes TEXT NULL,
  status ENUM('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS story_signups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  ip_hash CHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_email (email),
  INDEX idx_ip_hash_created (ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO companies (slug, name, website, industry, business_type, size, country, city, location, description, status) VALUES
('monsoon-media', 'Monsoon Media', 'monsoon.example', 'Professional Services', 'SME / Local Business', '1–10', 'Austria', 'Vienna', 'Vienna, Austria', 'A creative media company building from Vienna.', 'approved'),
('cardamom-commerce', 'Cardamom Commerce', 'cardamom.example', 'Retail & E-commerce', 'SME / Local Business', '11–50', 'Belgium', 'Brussels', 'Brussels, Belgium', 'Consumer products inspired by Kerala and made for Europe.', 'approved'),
('kerala-ventures', 'Kerala Ventures Studio', 'keralaventures.example', 'Professional Services', 'Consultancy', '1–10', 'Spain', 'Barcelona', 'Barcelona, Spain', 'Helping founders build and grow across Europe.', 'approved');

INSERT INTO founders (company_id, name, show_email) VALUES
((SELECT id FROM companies WHERE slug='monsoon-media'), 'Rahul Mathew', 0),
((SELECT id FROM companies WHERE slug='cardamom-commerce'), 'Deepa Kurian', 0),
((SELECT id FROM companies WHERE slug='kerala-ventures'), 'Nikhil George', 0);
