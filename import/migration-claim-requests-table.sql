-- Kerala Founders: add the claim_requests table for the new "Claim this listing" feature.
-- Purely additive — creates one new table, touches nothing existing.

CREATE TABLE IF NOT EXISTS claim_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  claimant_name VARCHAR(160) NOT NULL,
  claimant_email VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
