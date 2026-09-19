<?php
require __DIR__ . '/../config/db.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://keralafounders.eu';
$db = get_db();

$companies = $db->query(
    "SELECT slug, country, industry, created_at FROM companies WHERE status = 'approved' ORDER BY id"
)->fetchAll();

$countries = [];
$industries = [];
foreach ($companies as $c) {
    if ($c['country'] !== '') {
        $countries[$c['country']] = true;
    }
    if ($c['industry'] !== '') {
        $industries[$c['industry']] = true;
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
echo url("{$baseUrl}/about.php", null, 'monthly');
echo url("{$baseUrl}/add-company.php", null, 'monthly');
echo url("{$baseUrl}/privacy.php", null, 'yearly');

foreach (array_keys($countries) as $country) {
    echo url("{$baseUrl}/countries.php?country=" . rawurlencode($country));
}

foreach (array_keys($industries) as $industry) {
    echo url("{$baseUrl}/industries.php?industry=" . rawurlencode($industry));
}

foreach ($companies as $c) {
    $lastmod = date('Y-m-d', strtotime($c['created_at']));
    echo url("{$baseUrl}/company.php?id=" . rawurlencode($c['slug']), $lastmod, 'monthly');
}

echo "</urlset>\n";
