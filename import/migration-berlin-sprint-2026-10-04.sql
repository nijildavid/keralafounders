-- Kerala Founders: add the new Berlin record (KOKOLAND) from the 4 Oct 2026 research
-- sprint (source: berlin_research_sprint_2026-10-04.csv, project Drive folder).
-- Run once in phpMyAdmin against the live database. Safe to re-run: INSERT
-- IGNORE skips slugs that already exist (slug is UNIQUE).
--
-- Decisions made while turning the CSV into rows (change before running if
-- you disagree):
--   * Industry and business type use only the frozen taxonomy lists in
--     config/reference.php.
--   * `verified` stays 0: the research rated KOKOLAND "Partially verified",
--     and the site only has two display states (Verified / Not yet verified).
--     The research confidence is also written in the description in plain
--     words.
--   * Berlin Kerala Samajam, the Kerala Cultural Association of Berlin, the
--     World Malayalee Council Berlin chapter and Kombans FC Berlin are
--     deliberately left out: they are community associations or a sports
--     club, not companies.
--   * No founders are inserted. None were identified for KOKOLAND; naming
--     private individuals as "founders" without evidence would be wrong and
--     is personal data.
--   * No street addresses, phone numbers or WhatsApp numbers are stored. Public
--     location is just "Berlin, Germany".
--   * NILA Restaurants Berlin: the CSV recommends moving it from "Not yet
--     verified" to "Partially verified". The site has no such display level
--     (only Verified / Not yet verified), and setting verified=1 would show
--     a full Verified badge on evidence the research itself rated partial,
--     so nothing is changed for NILA here. It can be switched to Verified in
--     admin-edit.php once someone has confirmed the key details.

INSERT IGNORE INTO companies
  (slug, name, website, industry, business_type, size, founded_year, country, city, location, description, status, verified)
VALUES
('kokoland-berlin', 'KOKOLAND', 'https://kokoland.de/', 'Food & Hospitality', 'Restaurant', NULL, NULL, 'Germany', 'Berlin', 'Berlin, Germany',
 'Cloud kitchen in Berlin serving Kerala and South Indian food (biryanis, curries, porottas) through delivery platforms. It also runs a community events brand, Kokoland Kollective. Research confidence: the business is clearly Kerala-branded, but no founder or owner name could be confirmed from public sources.',
 'approved', 0);

-- Check after running: expect 1 row (approved), and the
-- approved Berlin count should now be 7 (the 6 existing plus 1 new).
SELECT slug, status, verified, business_type, industry FROM companies
 WHERE slug = 'kokoland-berlin';
SELECT COUNT(*) AS approved_berlin FROM companies WHERE city = 'Berlin' AND status = 'approved';
