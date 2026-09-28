-- Kerala Founders: add indexes behind the filters the directory actually
-- uses (status+country, status+industry, status+business_type,
-- status+city — see founders.php, countries.php, industries.php,
-- business-types.php, cities.php). Invisible at ~130 companies; without
-- these, every one of those pages does a full table scan as the directory
-- grows. Additive only, safe to run regardless of production's current
-- row/id state.

ALTER TABLE companies
  ADD INDEX idx_status_country (status, country),
  ADD INDEX idx_status_industry (status, industry),
  ADD INDEX idx_status_business_type (status, business_type),
  ADD INDEX idx_status_city (status, city);
