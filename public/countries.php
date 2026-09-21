<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
require __DIR__ . '/assets/guidance-helpers.php';
$db = get_db();

$selected = isset($_GET['country']) ? (string)$_GET['country'] : '';

$siteName = 'Kerala Founders';
if ($selected !== '') {
    $pageTitle = h('Keralite founders in ' . $selected) . ' — ' . $siteName;
    $metaDescription = h('Discover Keralite-founded companies building in ' . $selected . '.');
    $canonicalUrl = 'https://keralafounders.eu/countries.php?country=' . rawurlencode($selected);
} else {
    $pageTitle = 'Explore places — ' . $siteName;
    $metaDescription = 'Explore the Kerala founder network country by country across the European Union.';
    $canonicalUrl = 'https://keralafounders.eu/countries.php';
}

$companies = $selected !== '' ? fetch_approved_companies($db, $selected) : [];

$guidanceGuideSlug = null;
if ($selected !== '' && !empty($KF_GUIDANCE_NAV_LIVE)) {
    foreach (guidance_load_countries() as $gc) {
        if ($gc['status'] === 'live' && $gc['directory_country_name'] === $selected) {
            $guidanceGuideSlug = $gc['slug'];
            break;
        }
    }
}

$countryCounts = [];
if ($selected === '') {
    $counts = $db->query("SELECT country, COUNT(*) AS n FROM companies WHERE status = 'approved' GROUP BY country")
        ->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach (array_keys($KF_COUNTRIES) as $country) {
        $countryCounts[$country] = (int)($counts[$country] ?? 0);
    }
}

$jsonLd = null;
if ($selected !== '' && $companies) {
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => array_map(fn($c, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'url' => 'https://keralafounders.eu/company.php?id=' . rawurlencode($c['id']),
            'name' => $c['name'],
        ], $companies, array_keys($companies)),
    ];
}

$breadcrumbTrail = [
    ['name' => 'Home', 'url' => 'https://keralafounders.eu/'],
    ['name' => 'Countries', 'url' => 'https://keralafounders.eu/countries.php'],
];
if ($selected !== '') {
    $breadcrumbTrail[] = ['name' => $selected, 'url' => $canonicalUrl];
}
$breadcrumbJsonLd = breadcrumb_json_ld($breadcrumbTrail);
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<title><?= $pageTitle ?></title><meta name="description" content="<?= $metaDescription ?>">
<link rel="canonical" href="<?= h($canonicalUrl) ?>">
<meta property="og:type" content="website"><meta property="og:site_name" content="<?= h($siteName) ?>"><meta property="og:title" content="<?= $pageTitle ?>"><meta property="og:description" content="<?= $metaDescription ?>"><meta property="og:url" content="<?= h($canonicalUrl) ?>"><meta property="og:image" content="https://keralafounders.eu/assets/og-image.png"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= $pageTitle ?>"><meta name="twitter:description" content="<?= $metaDescription ?>"><meta name="twitter:image" content="https://keralafounders.eu/assets/og-image.png">
<?php if ($jsonLd): ?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap">
<div class="eyebrow">Explore by geography</div>
<?php if ($selected !== ''): ?>
<h1>Keralite founders in <?= h($selected) ?></h1>
<p class="muted section-intro">Discover Keralite-founded companies building in <?= h($selected) ?>.</p>
<a class="arrow" href="countries.php">← All countries</a>
<?php if ($guidanceGuideSlug): ?>
<p class="muted" style="margin-top:12px"><a class="arrow" href="guidance-country.php?country=<?= rawurlencode($guidanceGuideSlug) ?>">How to start a company in <?= h($selected) ?> →</a></p>
<?php endif; ?>
<div class="cards" style="margin-top:20px">
<?php if ($companies): foreach ($companies as $c): echo company_card_html($c); endforeach; else: ?>
  <div class="panel" style="grid-column:1/-1;text-align:center">No companies published in this location yet.</div>
<?php endif; ?>
</div>
<?php else: ?>
<h1>Keralite founders across the EU.</h1>
<p class="muted section-intro">Explore the Kerala founder network country by country across the European Union.</p>
<div class="country-grid">
<?php foreach ($countryCounts as $country => $n): ?>
  <a class="country-card" href="countries.php?country=<?= rawurlencode($country) ?>"><strong><?= h($country) ?></strong><small><?= $n ?> compan<?= $n === 1 ? 'y' : 'ies' ?> →</small></a>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
