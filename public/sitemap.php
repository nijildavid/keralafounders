<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/guidance-helpers.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://keralafounders.eu';
$db = get_db();

$companies = $db->query(
    "SELECT slug, country, industry, business_type, city, created_at FROM companies WHERE status = 'approved' ORDER BY id"
)->fetchAll();

$countries = [];
$industries = [];
$businessTypes = [];
$cities = [];
foreach ($companies as $c) {
    if ($c['country'] !== '') {
        $countries[$c['country']] = true;
    }
    if ($c['industry'] !== '') {
        $industries[$c['industry']] = true;
    }
    if ($c['business_type'] !== '') {
        $businessTypes[$c['business_type']] = true;
    }
    if ($c['city'] !== '') {
        $cities[$c['city']] = true;
    }
}

function url(string $loc, ?string $lastmod = null, string $changefreq = 'weekly'): string
{
    $xml = "  <url>\n    <loc>{$loc}</loc>\n";
    if ($lastmod) {
        $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
    }
    $xml .= "    <changefreq>{$changefreq}</changefreq>\n  </url>\n";
    return $xml;
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

echo url("{$baseUrl}/", null, 'daily');
echo url("{$baseUrl}/founders.php", null, 'daily');
echo url("{$baseUrl}/countries.php", null, 'weekly');
echo url("{$baseUrl}/industries.php", null, 'weekly');
echo url("{$baseUrl}/business-types.php", null, 'weekly');
echo url("{$baseUrl}/cities.php", null, 'weekly');
echo url("{$baseUrl}/about.php", null, 'monthly');
echo url("{$baseUrl}/add-company.php", null, 'monthly');
echo url("{$baseUrl}/privacy.php", null, 'yearly');
echo url("{$baseUrl}/guidance.php", null, 'monthly');
echo url("{$baseUrl}/guidance-faq.php", null, 'weekly');
echo url("{$baseUrl}/guidance-method.php", null, 'yearly');

foreach (guidance_load_countries() as $gc) {
    if ($gc['status'] === 'live') {
        echo url("{$baseUrl}/guidance-country.php?country=" . rawurlencode($gc['slug']), $gc['last_checked'], 'monthly');
    }
}

foreach (array_keys($countries) as $country) {
    echo url("{$baseUrl}/countries.php?country=" . rawurlencode($country));
}

foreach (array_keys($industries) as $industry) {
    echo url("{$baseUrl}/industries.php?industry=" . rawurlencode($industry));
}

foreach (array_keys($businessTypes) as $businessType) {
    echo url("{$baseUrl}/business-types.php?type=" . rawurlencode($businessType));
}

foreach (array_keys($cities) as $city) {
    echo url("{$baseUrl}/cities.php?city=" . rawurlencode($city));
}

foreach ($companies as $c) {
    $lastmod = date('Y-m-d', strtotime($c['created_at']));
    echo url("{$baseUrl}/company.php?id=" . rawurlencode($c['slug']), $lastmod, 'monthly');
}

echo "</urlset>\n";
