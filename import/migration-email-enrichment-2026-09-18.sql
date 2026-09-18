-- KeralaFounders.eu — Email enrichment migration
-- Generated 2026-09-18 from a Cowork research pass over the 88 companies
-- that had no contact_email and no founder email on file (the missing_email
-- report). Every row below has a real, published source (email_source_url) —
-- nothing was guessed or pattern-constructed.
--
-- Scope: contact_email only. Founder-name leads and cross-record flags
-- surfaced by the same research pass are intentionally NOT included here —
-- several are hedged as unconfirmed, third-party-sourced, or contradict
-- existing source data, and misattributing a person to a company is a real
-- risk, not just a data-quality nit. See the accompanying notes doc for
-- those; they need a human decision, not an automatic write.
--
-- Two flags worth reading before running this:
--   1. karinkada-ayurveda and sonnentag-kerala-ayurveda-shop resolve to the
--      exact same website, Impressum, and email (office@sonnentag.at) —
--      almost certainly the same operation listed twice. This migration
--      updates both rows as given rather than silently merging or deleting
--      either one; consider merging them as a separate, deliberate step.
--   2. kerala-restaurant-munich's contact_email comes from a companion
--      domain (restaurantkerala.de) that Cowork matched by name/city/entity,
--      not from the domain already on file (keralarestaurant.de, which
--      renders as an unreadable JS shell). Worth a manual check of whether
--      the `website` column itself should be corrected.
--
-- smaqo-foods's email is a careers-specific inbox (Low confidence) — real
-- and sourced, but probably not the right address for a "claim your
-- listing" outreach send; worth excluding from that specific campaign even
-- though it's fine to keep on file.

START TRANSACTION;

UPDATE `companies` SET `contact_email`='adukkala3124@gmail.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='Medium', `email_source_url`='https://adukkala.nl/contact' WHERE `slug`='adukkala';
UPDATE `companies` SET `contact_email`='info@insider-magazine.gr', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='Medium', `email_source_url`='https://www.athensinsider.com/contact-us/' WHERE `slug`='athens-insider';
UPDATE `companies` SET `contact_email`='hello@charliespickles.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://charliespickles.com/pages/contact' WHERE `slug`='charlies-pickles';
UPDATE `companies` SET `contact_email`='jibin@cied.eu', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://rightorigins.com' WHERE `slug`='cied-bv-rightorigins';
UPDATE `companies` SET `contact_email`='askus@codelattice.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='Medium', `email_source_url`='https://www.codelattice.com' WHERE `slug`='codelattice-netherlands';
UPDATE `companies` SET `contact_email`='curryleaveshannover@gmail.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='Medium', `email_source_url`='https://curryleavesrestaurant.de/' WHERE `slug`='curry-leaves-kerala-restaurant-hannover';
UPDATE `companies` SET `contact_email`='info@dekkaan.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.dekkaan.com/contact-us.html' WHERE `slug`='dekkaan-enterprises';
UPDATE `companies` SET `contact_email`='info@dotin.restaurant', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://dotin.restaurant' WHERE `slug`='dot-in';
UPDATE `companies` SET `contact_email`='query@igcsindia.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://igcsindia.com' WHERE `slug`='eu-language-institute';
UPDATE `companies` SET `contact_email`='office@sonnentag.at', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.keralaayurvedashop.at/impressum/' WHERE `slug`='karinkada-ayurveda';
UPDATE `companies` SET `contact_email`='info@keralakitchen.se', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://keralakitchen.se/contact' WHERE `slug`='kerala-kitchen-nykoping';
UPDATE `companies` SET `contact_email`='kontakt@kerala-restaurant.de', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.restaurantkerala.de/colofon' WHERE `slug`='kerala-restaurant-munich';
UPDATE `companies` SET `contact_email`='contact@madrasbistro.pl', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://madrasbistro.pl/' WHERE `slug`='madras-bistro';
UPDATE `companies` SET `contact_email`='Malabar.ug12@gmail.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='Medium', `email_source_url`='https://malabaronline.eu/' WHERE `slug`='malabar-restaurant-burgdorf';
UPDATE `companies` SET `contact_email`='contact@mercado360.eu', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.mercado360.eu/' WHERE `slug`='mercado360-ayurvod';
UPDATE `companies` SET `contact_email`='info@methi-restaurant.de', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://methi-restaurant.de/' WHERE `slug`='methi-kerala-restaurant-munich';
UPDATE `companies` SET `contact_email`='info@mezrestaurants.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://mezrestaurants.com' WHERE `slug`='mez-indo-coastal-cuisine';
UPDATE `companies` SET `contact_email`='office@prosi.at', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.prosi.at/' WHERE `slug`='prosi-pallikunnel';
UPDATE `companies` SET `contact_email`='info@seriousrooster.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.seriousrooster.com/' WHERE `slug`='serious-rooster';
UPDATE `companies` SET `contact_email`='careers@smaqo.com', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='Low', `email_source_url`='https://smaqo.com' WHERE `slug`='smaqo-foods';
UPDATE `companies` SET `contact_email`='office@sonnentag.at', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://www.keralaayurvedashop.at/impressum/' WHERE `slug`='sonnentag-kerala-ayurveda-shop';
UPDATE `companies` SET `contact_email`='info@venturevillage.world', `email_type`='company_public', `email_source`='Cowork research pass, 2026-09-18', `email_confidence`='High', `email_source_url`='https://venturevillage.world/' WHERE `slug`='venturevillage';

-- Validation
SELECT COUNT(*) AS companies_updated FROM `companies`
  WHERE `email_source` = 'Cowork research pass, 2026-09-18';
SELECT COUNT(*) AS still_missing_any_email FROM `companies` c
  WHERE (c.contact_email IS NULL OR TRIM(c.contact_email) = '')
    AND NOT EXISTS (
        SELECT 1 FROM founders f
        WHERE f.company_id = c.id AND f.email IS NOT NULL AND TRIM(f.email) <> ''
    );
-- Expected: companies_updated = 22. still_missing_any_email should have
-- dropped by 21 from its pre-migration value (22 UPDATEs, but two rows —
-- karinkada-ayurveda and sonnentag-kerala-ayurveda-shop — are believed to
-- be the same operation; see the flag above).

COMMIT;
