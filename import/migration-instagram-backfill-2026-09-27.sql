-- KeralaFounders.eu — Instagram handle backfill
-- Generated 2026-09-27 from an external research pass over the ~130
-- companies already in the directory (handoff: "backfill Instagram handles
-- on existing company profiles"). Every row below carries a confidence
-- rating from that research pass, already reviewed against the standard
-- this project holds itself to elsewhere (don't present a guess as a fact).
--
-- Requires migration-add-instagram-provenance.sql to have been run first
-- (adds instagram_source / instagram_confidence / instagram_note).
--
-- Split:
--   - High confidence (71 rows): confirmed against the company's own site,
--     or independently cross-checked (Instagram location tag, business
--     registry, matching Facebook page). Written as
--     instagram_confidence='high' — company.php shows these on the public
--     profile immediately.
--   - Medium confidence (7 rows): plausible but not independently verified,
--     or (2 of them) a founder's personal account rather than a company
--     page. Written as instagram_confidence='medium' — company.php hides
--     these from the public profile until approved via admin-edit.php
--     (the instagram_note column carries the reviewer's reasoning).
--   - No match found (52 companies): not included here at all — nothing to
--     backfill, no placeholder written.
--
-- Two listings the research flagged as likely duplicates (not merged here —
-- that's a separate, deliberate decision for Nijil, not an automatic one):
--   1. karinkada-ayurveda and sonnentag-kerala-ayurveda-shop — same website
--      (keralaayurvedashop.at) and director. Same pair the 2026-09-18 email
--      enrichment migration already flagged for the same reason.
--   2. igcs-indo-german-services and igcs-consultancy — same founder (Saju
--      Jakob) and website (igcsvisa.de); may be two distinct legal entities
--      of the same practice rather than duplicate listings, so treated as a
--      weaker signal than the Ayurveda pair.
--
-- Every UPDATE is guarded so it never overwrites a handle a founder already
-- entered themselves through the form (instagram_source='founder_submitted').

START TRANSACTION;

UPDATE `companies` SET `instagram`='3leavesblackrock', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='3-leaves' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='adukkala.nl', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='adukkala' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='anila.thekkan', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='ready-to-care' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='ayuraayurveda', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='ayura-ayurveda' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='bollyfoods', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='bollyfoods' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='carvestartuplabs', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='carve-startup-labs' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='charliespickles.amsterdam', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='charlies-pickles' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='cied_group', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='cied-bv-rightorigins' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='costadomalabar', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='costa-do-malabar' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='curryleaveshannover', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='curry-leaves-kerala-restaurant-hannover' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='dixonskitchen', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='dixons-kitchen-dortmund' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='dot.in_._', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='dot-in' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='foodeza.de', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='foodeza' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='fuddagency', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='fudd-marketing' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='hireandcare', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='hire-and-care' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='inaivaa_de', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='inaivaa-dortmund' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='indianaderezo', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='indian-aderezo' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='indilicious.maastricht', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='indilicious-maastricht' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='igcs_frankfurt', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='igcs-consultancy' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='athensinsider', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='athens-insider' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='jhasvikrestaurant', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='jhasvik-duesseldorf' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='kalluandco', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kallu-and-co' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='keralaayurvedashop', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='karinkada-ayurveda' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='kasicafedublin', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kasi-cafe' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='keraoriental', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kera-oriental' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='keralaexotic', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kerala-exotic-supermarket' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='keralakitchen.de', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kerala-kitchen-sankt-augustin' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='kerala.de', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kerala-restaurant-munich' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='keralarestaurantlisbon', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kerala-restaurant-lisbon' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='k2k_restaurant', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kerala2krakow' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='kvzgmbh01', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kolner-visa-zentrum' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='kumbarestaurant', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kumba-frankfurt' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='letterstoabroad_', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='letters-to-abroad' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='restaurante.littleindia', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='little-india-boadilla' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='madrasbistro', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='madras-bistro' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='malabarkeralarestaurant', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='malabar-restaurant-burgdorf' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='malayali.rocks', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='malayali-beer' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='mamaspicebydevaky', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='mama-spice' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='maya.ayurveda', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='maya-ayurveda' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='ayurvod', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='mercado360-ayurvod' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='methi_kerala_restaurant', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='methi-kerala-restaurant-munich' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='mez_malta', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='mez-indo-coastal-cuisine' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='my.indian.kitchenn', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='my-indian-kitchen-eindhoven' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='naanbarmalta', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='naan-bar' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='nanma_south_holland', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='nanma-south-holland' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='nestabide', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='nestabide' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='gmbhnetwalk', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='netwalk' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='nilakitchenberlin', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='nila-restaurants-berlin' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='nilavararestaurant', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='nilavara-restaurant-frechen' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='olivez.ie', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='olivez' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='pflege_de', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='pflege-de-consultancy' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='prosiexoticworld', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='prosi-pallikunnel' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='pulari.ie', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='pulari-restaurant' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='saraswati.fi', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='saraswati' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='searchindie', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='search-indie' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='sisu_agrosolutions', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='sisu-agro-solutions' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='smaqofoods', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='smaqo-foods' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='keralaayurvedashop', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='sonnentag-kerala-ayurveda-shop' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='spiceroute.de', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='spice-route' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='sreekrishna.ayurveda', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='sreekrishna-ayurveda' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='restaurante_swaad', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='swaad' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='thanima_hamburg', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='thanima-kerala-restaurant-hamburg' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='thebestexotickeralakitchen', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='best-exotic-kerala-kitchen' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='thekalikutstory', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='kalikut-story' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='thinnan.app', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='thinnan' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='utrechtayurveda', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='utrecht-ayurveda' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='veda.naturals', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='veda-naturals' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='venturevillage.world', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='venturevillage' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='wanakamofficial', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='wanakam-amsterdam' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='yoga.vihar', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='yoga-vihar-berlin' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='jithubakers', `instagram_source`='research', `instagram_confidence`='high', `instagram_note`=NULL WHERE `slug`='yummy-bites-by-jithu' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='jijialon', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Founder Jijimon Chandran''s personal account, not a company page' WHERE `slug`='acsia-systems-deutschland' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='ammasfood_do', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Handle surfaces in Dortmund-related search results, but a second similar account also exists and the bio could not be independently viewed to confirm' WHERE `slug`='ammas-food-dortmund' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='jrajmohanpillai', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Founder Dr J Rajmohan Pillai''s personal account, not a company page' WHERE `slug`='beta-group' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='igcs_frankfurt', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Shares founder and website with igcs-consultancy (already high-confidence); no separate account confirmed for this specific legal entity' WHERE `slug`='igcs-indo-german-services' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='indiankoffiehouse', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Exact name/domain match but bio could not be independently verified' WHERE `slug`='indian-koffie-house-almere' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='kalikut1498', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Exact name match but no city-specific confirmation; a same-named beer brand in the UK could be a confusable account' WHERE `slug`='kalikut-1498' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');
UPDATE `companies` SET `instagram`='malabar_restaurantecafe', `instagram_source`='research', `instagram_confidence`='medium', `instagram_note`='Business independently confirmed in Brandenburg an der Havel, but the handle itself could not be cross-linked to the official site' WHERE `slug`='malabar-cafe-brandenburg' AND (`instagram_source` IS NULL OR `instagram_source` <> 'founder_submitted');

-- Validation
SELECT COUNT(*) AS companies_updated FROM `companies`
  WHERE `instagram_source` = 'research';
SELECT COUNT(*) AS high_confidence_published FROM `companies`
  WHERE `instagram_source` = 'research' AND `instagram_confidence` = 'high';
SELECT COUNT(*) AS medium_confidence_held_for_review FROM `companies`
  WHERE `instagram_source` = 'research' AND `instagram_confidence` = 'medium';
-- Expected: companies_updated = 78 (71 high + 7 medium), unless a founder
-- had already submitted their own handle for one of these slugs first (the
-- WHERE guard above intentionally skips those, so the count would be lower —
-- that's correct, not a bug). high_confidence_published should be 71,
-- medium_confidence_held_for_review should be 7 (allowing for the same
-- guard).

-- Any slug below listed here does NOT exist in this database under that
-- exact slug — the site's internal slug differs from what the research
-- matched against, and needs a manual look rather than silent skipping.
SELECT expected.slug AS slug_not_found_in_companies_table
FROM (
  SELECT '3-leaves' AS slug
  UNION ALL SELECT 'adukkala'
  UNION ALL SELECT 'ready-to-care'
  UNION ALL SELECT 'ayura-ayurveda'
  UNION ALL SELECT 'bollyfoods'
  UNION ALL SELECT 'carve-startup-labs'
  UNION ALL SELECT 'charlies-pickles'
  UNION ALL SELECT 'cied-bv-rightorigins'
  UNION ALL SELECT 'costa-do-malabar'
  UNION ALL SELECT 'curry-leaves-kerala-restaurant-hannover'
  UNION ALL SELECT 'dixons-kitchen-dortmund'
  UNION ALL SELECT 'dot-in'
  UNION ALL SELECT 'foodeza'
  UNION ALL SELECT 'fudd-marketing'
  UNION ALL SELECT 'hire-and-care'
  UNION ALL SELECT 'inaivaa-dortmund'
  UNION ALL SELECT 'indian-aderezo'
  UNION ALL SELECT 'indilicious-maastricht'
  UNION ALL SELECT 'igcs-consultancy'
  UNION ALL SELECT 'athens-insider'
  UNION ALL SELECT 'jhasvik-duesseldorf'
  UNION ALL SELECT 'kallu-and-co'
  UNION ALL SELECT 'karinkada-ayurveda'
  UNION ALL SELECT 'kasi-cafe'
  UNION ALL SELECT 'kera-oriental'
  UNION ALL SELECT 'kerala-exotic-supermarket'
  UNION ALL SELECT 'kerala-kitchen-sankt-augustin'
  UNION ALL SELECT 'kerala-restaurant-munich'
  UNION ALL SELECT 'kerala-restaurant-lisbon'
  UNION ALL SELECT 'kerala2krakow'
  UNION ALL SELECT 'kolner-visa-zentrum'
  UNION ALL SELECT 'kumba-frankfurt'
  UNION ALL SELECT 'letters-to-abroad'
  UNION ALL SELECT 'little-india-boadilla'
  UNION ALL SELECT 'madras-bistro'
  UNION ALL SELECT 'malabar-restaurant-burgdorf'
  UNION ALL SELECT 'malayali-beer'
  UNION ALL SELECT 'mama-spice'
  UNION ALL SELECT 'maya-ayurveda'
  UNION ALL SELECT 'mercado360-ayurvod'
  UNION ALL SELECT 'methi-kerala-restaurant-munich'
  UNION ALL SELECT 'mez-indo-coastal-cuisine'
  UNION ALL SELECT 'my-indian-kitchen-eindhoven'
  UNION ALL SELECT 'naan-bar'
  UNION ALL SELECT 'nanma-south-holland'
  UNION ALL SELECT 'nestabide'
  UNION ALL SELECT 'netwalk'
  UNION ALL SELECT 'nila-restaurants-berlin'
  UNION ALL SELECT 'nilavara-restaurant-frechen'
  UNION ALL SELECT 'olivez'
  UNION ALL SELECT 'pflege-de-consultancy'
  UNION ALL SELECT 'prosi-pallikunnel'
  UNION ALL SELECT 'pulari-restaurant'
  UNION ALL SELECT 'saraswati'
  UNION ALL SELECT 'search-indie'
  UNION ALL SELECT 'sisu-agro-solutions'
  UNION ALL SELECT 'smaqo-foods'
  UNION ALL SELECT 'sonnentag-kerala-ayurveda-shop'
  UNION ALL SELECT 'spice-route'
  UNION ALL SELECT 'sreekrishna-ayurveda'
  UNION ALL SELECT 'swaad'
  UNION ALL SELECT 'thanima-kerala-restaurant-hamburg'
  UNION ALL SELECT 'best-exotic-kerala-kitchen'
  UNION ALL SELECT 'kalikut-story'
  UNION ALL SELECT 'thinnan'
  UNION ALL SELECT 'utrecht-ayurveda'
  UNION ALL SELECT 'veda-naturals'
  UNION ALL SELECT 'venturevillage'
  UNION ALL SELECT 'wanakam-amsterdam'
  UNION ALL SELECT 'yoga-vihar-berlin'
  UNION ALL SELECT 'yummy-bites-by-jithu'
  UNION ALL SELECT 'acsia-systems-deutschland'
  UNION ALL SELECT 'ammas-food-dortmund'
  UNION ALL SELECT 'beta-group'
  UNION ALL SELECT 'igcs-indo-german-services'
  UNION ALL SELECT 'indian-koffie-house-almere'
  UNION ALL SELECT 'kalikut-1498'
  UNION ALL SELECT 'malabar-cafe-brandenburg'
) AS expected
LEFT JOIN `companies` c ON c.slug = expected.slug
WHERE c.slug IS NULL;

COMMIT;
