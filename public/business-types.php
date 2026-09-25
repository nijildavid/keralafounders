<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
$db = get_db();

const PAGE_SIZE = 12;

$selected = isset($_GET['type']) ? (string)$_GET['type'] : '';
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

$siteName = 'Kerala Founders';
if ($selected !== '') {
    $pageTitle = $selected . ' founders — ' . $siteName;
    $metaDescription = 'Discover Keralite-founded ' . $selected . ' businesses building across the European Union.';
    $canonicalUrl = 'https://keralafounders.eu/business-types.php?type=' . rawurlencode($selected);
    if ($page > 1) {
        $canonicalUrl .= '&page=' . $page;
    }
} else {
    $pageTitle = 'Explore by business type — ' . $siteName;
    $metaDescription = 'Explore the Kerala founder network business type by business type across the European Union.';
    $canonicalUrl = 'https://keralafounders.eu/business-types.php';
}

$companies = $selected !== '' ? fetch_approved_companies_by_business_type($db, $selected) : [];
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

$typeCounts = [];
if ($selected === '') {
    $counts = $db->query("SELECT business_type, COUNT(*) AS n FROM companies WHERE status = 'approved' GROUP BY business_type")
        ->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($KF_BUSINESS_TYPES as $type) {
        $typeCounts[$type] = (int)($counts[$type] ?? 0);
    }
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
    ['name' => 'Business Types', 'url' => 'https://keralafounders.eu/business-types.php'],
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
<div class="eyebrow">Explore by business type</div>
<?php if ($selected !== ''): ?>
<h1><?= h($selected) ?> founders</h1>
<p class="muted section-intro">Discover Keralite-founded <?= h($selected) ?> businesses building across the European Union.</p>
<a class="arrow" href="business-types.php">← All business types</a>
<div class="cards" style="margin-top:20px">
<?php if ($pageRows): foreach ($pageRows as $c): echo company_card_html($c); endforeach; else: ?>
  <div class="panel" style="grid-column:1/-1;text-align:center">No companies published in this business type yet.</div>
<?php endif; ?>
</div>
<?= ssr_pagination_html('business-types.php', ['type' => $selected], $page, $totalPages) ?>
<?php else: ?>
<h1>Keralite founders by business type.</h1>
<p class="muted section-intro">Explore the Kerala founder network business type by business type across the European Union.</p>
<div class="country-grid">
<?php foreach ($typeCounts as $type => $n): ?>
  <a class="country-card" href="business-types.php?type=<?= rawurlencode($type) ?>"><strong><?= h($type) ?></strong><small><?= $n ?> compan<?= $n === 1 ? 'y' : 'ies' ?> →</small></a>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
