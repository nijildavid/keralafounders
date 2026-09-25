<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
$db = get_db();

const PAGE_SIZE = 12;

$selected = isset($_GET['city']) ? (string)$_GET['city'] : '';
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

$siteName = 'Kerala Founders';
if ($selected !== '') {
    $pageTitle = $selected . ' founders — ' . $siteName;
    $metaDescription = 'Discover Keralite-founded companies building in ' . $selected . '.';
    $canonicalUrl = 'https://keralafounders.eu/cities.php?city=' . rawurlencode($selected);
    if ($page > 1) {
        $canonicalUrl .= '&page=' . $page;
    }
} else {
    $pageTitle = 'Explore by city — ' . $siteName;
    $metaDescription = 'Explore the Kerala founder network city by city across the European Union.';
    $canonicalUrl = 'https://keralafounders.eu/cities.php';
}

$companies = $selected !== '' ? fetch_approved_companies_by_city($db, $selected) : [];
$totalPages = 1;
$pageRows = $companies;
if ($selected !== '') {
    $total = count($companies);
    $totalPages = max(1, (int)ceil($total / PAGE_SIZE));
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    $start = ($page - 1) * PAGE_SIZE;
    $pageRows = array_slice($companies, $start, PAGE_SIZE);
}

// Real cities only — no zero-fill. $KF_COUNTRIES' nested city lists run to
// ~140 entries; showing every one (most with 0 companies) would be exactly
// the thin-page problem this page needs to avoid.
$cityCounts = [];
if ($selected === '') {
    $cityCounts = $db->query("SELECT city, COUNT(*) AS n FROM companies WHERE status = 'approved' GROUP BY city ORDER BY n DESC")
        ->fetchAll(PDO::FETCH_KEY_PAIR);
}

$jsonLd = null;
if ($selected !== '' && $pageRows) {
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => array_map(fn($c, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'url' => 'https://keralafounders.eu/company.php?id=' . rawurlencode($c['id']),
            'name' => $c['name'],
        ], $pageRows, array_keys($pageRows)),
    ];
}

$breadcrumbTrail = [
    ['name' => 'Home', 'url' => 'https://keralafounders.eu/'],
    ['name' => 'Cities', 'url' => 'https://keralafounders.eu/cities.php'],
];
if ($selected !== '') {
    $breadcrumbTrail[] = ['name' => $selected, 'url' => $canonicalUrl];
}
$breadcrumbJsonLd = breadcrumb_json_ld($breadcrumbTrail);
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<?php if ($jsonLd): ?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap">
<div class="eyebrow">Explore by city</div>
<?php if ($selected !== ''): ?>
<h1><?= h($selected) ?> founders</h1>
<p class="muted section-intro">Discover Keralite-founded companies building in <?= h($selected) ?>.</p>
<a class="arrow" href="cities.php">← All cities</a>
<div class="cards" style="margin-top:20px">
<?php if ($pageRows): foreach ($pageRows as $c): echo company_card_html($c); endforeach; else: ?>
  <div class="panel" style="grid-column:1/-1;text-align:center">No companies published in this city yet.</div>
<?php endif; ?>
</div>
<?= ssr_pagination_html('cities.php', ['city' => $selected], $page, $totalPages) ?>
<?php else: ?>
<h1>Keralite founders by city.</h1>
<p class="muted section-intro">Explore the Kerala founder network city by city across the European Union.</p>
<div class="country-grid">
<?php foreach ($cityCounts as $city => $n): ?>
  <a class="country-card" href="cities.php?city=<?= rawurlencode($city) ?>"><strong><?= h($city) ?></strong><small><?= $n ?> compan<?= $n === 1 ? 'y' : 'ies' ?> →</small></a>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
