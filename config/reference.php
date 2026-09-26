<?php

$KF_COUNTRIES = [
    'Austria' => ['Vienna', 'Graz', 'Linz', 'Salzburg', 'Purkersdorf'],
    'Belgium' => ['Brussels', 'Antwerp', 'Ghent', 'Leuven'],
    'Bulgaria' => ['Sofia', 'Plovdiv', 'Varna'],
    'Croatia' => ['Zagreb', 'Split', 'Rijeka'],
    'Cyprus' => ['Nicosia', 'Limassol', 'Larnaca'],
    'Czechia' => ['Prague', 'Brno', 'Ostrava'],
    'Denmark' => ['Copenhagen', 'Aarhus', 'Odense'],
    'Estonia' => ['Tallinn', 'Tartu'],
    'Finland' => ['Helsinki', 'Espoo', 'Tampere'],
    'France' => ['Paris', 'Lyon', 'Marseille', 'Toulouse'],
    'Germany' => ['Berlin', 'Hamburg', 'Munich', 'Frankfurt', 'Cologne', 'Bad Rappenau', 'Bremen', 'Düsseldorf', 'Koblenz', 'Regensburg', 'Bonn', 'Aachen', 'Brandenburg an der Havel', 'Burgdorf', 'Dortmund', 'Frechen', 'Hannover', 'Höhenkirchen-Siegertsbrunn', 'Leverkusen', 'Mühlacker', 'Sankt Augustin'],
    'Greece' => ['Athens', 'Thessaloniki'],
    'Hungary' => ['Budapest', 'Debrecen'],
    'Ireland' => ['Dublin', 'Cork', 'Galway', 'Ashbourne'],
    'Italy' => ['Rome', 'Milan', 'Turin', 'Bologna'],
    'Latvia' => ['Riga', 'Daugavpils'],
    'Lithuania' => ['Vilnius', 'Kaunas', 'Klaipėda'],
    'Luxembourg' => ['Luxembourg', 'Belvaux'],
    'Malta' => ['Valletta', 'Birkirkara', "St Julian's", "St Paul's Bay", 'Floriana'],
    'Netherlands' => ['Amsterdam', 'Rotterdam', 'The Hague', 'Utrecht', 'Heerlen', 'Best', 'Nieuwegein', 'Egmond aan Zee', 'Eindhoven', 'Delft', 'Almere', 'Maastricht'],
    'Poland' => ['Warsaw', 'Kraków', 'Wrocław', 'Gdańsk'],
    'Portugal' => ['Lisbon', 'Porto', 'Braga', 'Albufeira'],
    'Romania' => ['Bucharest', 'Cluj-Napoca', 'Timișoara', 'Ploiești'],
    'Slovakia' => ['Bratislava', 'Košice', 'Gabčíkovo'],
    'Slovenia' => ['Ljubljana', 'Maribor', 'Domanjševci'],
    'Spain' => ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Boadilla del Monte', 'Valladolid'],
    'Sweden' => ['Stockholm', 'Gothenburg', 'Malmö', 'Nyköping'],
];

// Taxonomy v1.0 — frozen per Kerala Founders Taxonomy Improvement & Governance Plan.
// Do not add categories ad hoc; use the closest existing one and log genuine gaps
// for review at the next checkpoint instead of editing this list.
$KF_INDUSTRIES = [
    'Technology',
    'Healthcare',
    'Food & Hospitality',
    'Professional Services',
    'Construction & Trades',
    'Logistics & Transport',
    'Education',
    'Real Estate',
    'Retail & E-commerce',
    'Finance',
];

$KF_BUSINESS_TYPES = [
    'Startup',
    'SME / Local Business',
    'Restaurant',
    'Café',
    'Consultancy',
    'Medical Practice',
    'Community Organisation',
    'Association',
    'Non-profit',
    'Professional Practice',
];

$KF_SIZES = ['1–10', '11–50', '51–200', '201–500', '500+'];

// The 14 districts of Kerala, for the optional "which district" follow-up
// on add-company.php once someone picks a Kerala-connection option.
$KF_KERALA_DISTRICTS = [
    'Thiruvananthapuram', 'Kollam', 'Pathanamthitta', 'Alappuzha', 'Kottayam',
    'Idukki', 'Ernakulam', 'Thrissur', 'Palakkad', 'Malappuram', 'Kozhikode',
    'Wayanad', 'Kannur', 'Kasaragod',
];

// Options for the "how are you connected to Kerala?" question on
// add-company.php. Free text stored as-is in companies.kerala_connection —
// not shown publicly, used only to help an admin judge Verified status.
$KF_KERALA_CONNECTIONS = [
    'Born and raised in Kerala',
    'Family roots in Kerala',
    'Studied in Kerala',
    'Married into a Kerala family',
    'Other Kerala connection',
];

// Emoji flags for the footer's country list. Keyed by the exact country name
// as stored in companies.country (a free-text column, not constrained to
// $KF_COUNTRIES) — includes a few real countries seen in data that aren't in
// the $KF_COUNTRIES whitelist above. Lookups should fall back gracefully
// (e.g. `$KF_COUNTRY_FLAGS[$country] ?? ''`) for anything not listed here.
$KF_COUNTRY_FLAGS = [
    'Austria' => '🇦🇹',
    'Belgium' => '🇧🇪',
    'Bulgaria' => '🇧🇬',
    'Croatia' => '🇭🇷',
    'Cyprus' => '🇨🇾',
    'Czechia' => '🇨🇿',
    'Denmark' => '🇩🇰',
    'Estonia' => '🇪🇪',
    'Finland' => '🇫🇮',
    'France' => '🇫🇷',
    'Germany' => '🇩🇪',
    'Greece' => '🇬🇷',
    'Hungary' => '🇭🇺',
    'Ireland' => '🇮🇪',
    'Italy' => '🇮🇹',
    'Latvia' => '🇱🇻',
    'Lithuania' => '🇱🇹',
    'Luxembourg' => '🇱🇺',
    'Malta' => '🇲🇹',
    'Netherlands' => '🇳🇱',
    'Poland' => '🇵🇱',
    'Portugal' => '🇵🇹',
    'Romania' => '🇷🇴',
    'Slovakia' => '🇸🇰',
    'Slovenia' => '🇸🇮',
    'Spain' => '🇪🇸',
    'Sweden' => '🇸🇪',
    'United Kingdom' => '🇬🇧',
    'Switzerland' => '🇨🇭',
    'Norway' => '🇳🇴',
];

// Guidance section go-live switch. Keep false until the built pages have
// been reviewed and approved — flipping it to true is what makes the
// header/footer "Guidance" links live and stops them showing "Coming soon".
$KF_GUIDANCE_NAV_LIVE = true;

// Guidance feedback auto-flagging thresholds (doc section 6.4) — kept here,
// not hardcoded in admin-guidance-feedback.php, so they're easy to tune.
$GUIDANCE_FLAG_STREAK = 3;      // N consecutive thumbs-down votes flags a target
$GUIDANCE_FLAG_RATIO = 0.40;    // down-vote ratio above this flags a target...
$GUIDANCE_FLAG_MIN_VOTES = 10;  // ...but only once it has at least this many votes
