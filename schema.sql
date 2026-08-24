CREATE TABLE IF NOT EXISTS companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  website VARCHAR(255) NULL,
  industry VARCHAR(80) NOT NULL,
  size VARCHAR(20) NULL,
  founded_year SMALLINT NULL,
  country VARCHAR(80) NOT NULL,
  city VARCHAR(80) NOT NULL,
  location VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
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

INSERT INTO companies (slug, name, website, industry, size, country, city, location, description, status) VALUES
('monsoon-media', 'Monsoon Media', 'monsoon.example', 'Media / Creative', '1–10', 'Austria', 'Vienna', 'Vienna, Austria', 'A creative media company building from Vienna.', 'approved'),
('cardamom-commerce', 'Cardamom Commerce', 'cardamom.example', 'Retail / Consumer', '11–50', 'Belgium', 'Brussels', 'Brussels, Belgium', 'Consumer products inspired by Kerala and made for Europe.', 'approved'),
('kerala-ventures', 'Kerala Ventures Studio', 'keralaventures.example', 'Professional Services', '1–10', 'Spain', 'Barcelona', 'Barcelona, Spain', 'Helping founders build and grow across Europe.', 'approved');

INSERT INTO founders (company_id, name, show_email) VALUES
((SELECT id FROM companies WHERE slug='monsoon-media'), 'Rahul Mathew', 0),
((SELECT id FROM companies WHERE slug='cardamom-commerce'), 'Deepa Kurian', 0),
((SELECT id FROM companies WHERE slug='kerala-ventures'), 'Nikhil George', 0);
