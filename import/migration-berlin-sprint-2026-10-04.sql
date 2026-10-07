-- Kerala Founders: add the 4 new Berlin records from the 4 Oct 2026 research
-- sprint (source: berlin_research_sprint_2026-10-04.csv, project Drive folder).
-- Run once in phpMyAdmin against the live database. Safe to re-run: INSERT
-- IGNORE skips slugs that already exist (slug is UNIQUE) and the email
-- UPDATEs only fill a column that is still empty.
--
-- Decisions made while turning the CSV into rows (change before running if
-- you disagree):
--   * Industry and business type use only the frozen taxonomy lists in
--     config/reference.php. Per TAXONOMY.md, cultural associations are
--     classified by primary activity ("event-running cultural association"
--     -> Education). A Berlin amateur football club has no matching industry;
--     Education is the closest and the gap should be logged for the next
--     taxonomy checkpoint. That record is 'pending' anyway, so it is not
--     shown publicly.
--   * `verified` follows the research label, using the rule in the site's
--     Listing Policy ("Verified" = sufficient evidence found for the key
--     listing information). The research marked KCAB "Verified" (official
--     site and court registry), so it gets verified=1. KOKOLAND, WMC Berlin
--     and Kombans FC were "Partially verified" or "Unverified", and the site
--     only has two display states (Verified / Not yet verified), so they stay
--     0. The research confidence is also written in each description in plain
--     words.
--   * Berlin Kerala Samajam is deliberately left out: it is not a company and
--     its current activity could not be confirmed.
--   * No founders are inserted. None were identified for KOKOLAND, and the
--     organisations have office-bearers, not founders; naming private
--     individuals as "founders" would be wrong and is personal data.
--   * No street addresses, phone numbers or WhatsApp numbers are stored. Public
--     location is just "Berlin, Germany".
--   * Only the two organisation mailboxes that the organisations publish
--     themselves are stored (as admin-only contact_email, same pattern as
--     import/migration-import-contacts-batch1.sql). Both were seen on the
--     organisations' own sites on 4 Oct 2026.
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
 'approved', 0),
('kcab-berlin-malayalees', 'Kerala Cultural Association of Berlin (Berlin Malayalees)', 'https://www.berlinmalayalees.com/', 'Education', 'Association', NULL, NULL, 'Germany', 'Berlin', 'Berlin, Germany',
 'Non-profit cultural association for Malayalees in Berlin and the surrounding area. It runs cultural programmes, family activities and social events. Registered as Kerala Cultural Association of Berlin e.V. in the German court register. Research confidence: verified.',
 'approved', 1),
('wmc-berlin', 'World Malayalee Council, Berlin Chapter', 'https://wmcberlin.de/', 'Education', 'Community Organisation', NULL, NULL, 'Germany', 'Berlin', 'Berlin, Germany',
 'Berlin chapter of the World Malayalee Council, the international Malayalee diaspora organisation. It hosts cultural events such as Onam and professional networking meetups for the Malayalam community in Berlin. Research confidence: the organisation is clearly Malayalee-oriented, but no office-bearers were found to cross-check.',
 'approved', 0),
('kombans-fc-berlin', 'Kombans FC Berlin', NULL, 'Education', 'Community Organisation', NULL, NULL, 'Germany', 'Berlin', 'Berlin, Germany',
 'Amateur football club of the Malayali diaspora in Berlin, playing in the Kerala European Football Federation (KEFF). Research confidence: unverified. Evidence is a single fixture listing, and the name may be confused with a club of the same name in Thiruvananthapuram. Kept unpublished until a person confirms the club.',
 'pending', 0);

-- Admin-only contact emails (published by the organisations themselves).
UPDATE companies
   SET contact_email = 'kcab@berlinmalayalees.com',
       email_type = 'Business/public contact',
       email_source = 'Official website',
       email_confidence = 'High',
       email_source_url = 'https://www.berlinmalayalees.com/'
 WHERE slug = 'kcab-berlin-malayalees' AND (contact_email IS NULL OR contact_email = '');

UPDATE companies
   SET contact_email = 'worldmalayaleecouncil.berlin@gmail.com',
       email_type = 'Business/public contact',
       email_source = 'Official website',
       email_confidence = 'High',
       email_source_url = 'https://wmcberlin.de/'
 WHERE slug = 'wmc-berlin' AND (contact_email IS NULL OR contact_email = '');

-- Check after running: expect 4 rows (3 approved, 1 pending), and the
-- approved Berlin count should now be 9 (the 6 existing plus 3 new).
SELECT slug, status, verified, business_type, industry FROM companies
 WHERE slug IN ('kokoland-berlin','kcab-berlin-malayalees','wmc-berlin','kombans-fc-berlin');
SELECT COUNT(*) AS approved_berlin FROM companies WHERE city = 'Berlin' AND status = 'approved';
