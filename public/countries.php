<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
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
?>
<!doctype html>
<html lang="en"><head><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("consent","default",{ad_storage:"denied",ad_user_data:"denied",ad_personalization:"denied",analytics_storage:"denied"});</script><script src="https://cdn.cookiehub.eu/c2/a2366e42.js"></script><script type="text/javascript">document.addEventListener("DOMContentLoaded",function(event){var cpm={};if(window.cookiehub){window.cookiehub.load(cpm);}});</script><!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-EJ9D0P01RH"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","G-EJ9D0P01RH");</script><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $pageTitle ?></title><meta name="description" content="<?= $metaDescription ?>">
<link rel="canonical" href="<?= h($canonicalUrl) ?>">
<meta property="og:type" content="website"><meta property="og:site_name" content="<?= h($siteName) ?>"><meta property="og:title" content="<?= $pageTitle ?>"><meta property="og:description" content="<?= $metaDescription ?>"><meta property="og:url" content="<?= h($canonicalUrl) ?>">
<meta name="twitter:card" content="summary"><meta name="twitter:title" content="<?= $pageTitle ?>"><meta name="twitter:description" content="<?= $metaDescription ?>">
<?php if ($jsonLd): ?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap">
<div class="eyebrow">Explore by geography</div>
<?php if ($selected !== ''): ?>
<h1>Keralite founders in <?= h($selected) ?></h1>
<p class="muted section-intro">Discover Keralite-founded companies building in <?= h($selected) ?>.</p>
<a class="arrow" href="countries.php">← All countries</a>
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
