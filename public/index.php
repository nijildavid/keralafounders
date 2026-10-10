<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/render-helpers.php';
$db = get_db();

// First-paint for SEO/no-JS clients, mirroring the <script> block's
// d.slice(0,6) below — the existing JS still runs and re-renders this
// identically for JS-enabled clients, unchanged from before.
$recentCompanies = fetch_recent_approved_companies($db, 6);

$categoryIcons = [
    'Technology' => 'technology-laptop',
    'Food & Hospitality' => 'food-utensils',
    'Healthcare' => 'healthcare-heart',
    'Professional Services' => 'professional-briefcase',
    'Retail & E-commerce' => 'retail-cart',
    'Construction & Trades' => 'construction-wrench',
];
$industryCounts = $db->query(
    "SELECT industry, COUNT(*) AS n FROM companies WHERE status = 'approved' GROUP BY industry"
)->fetchAll(PDO::FETCH_KEY_PAIR);

// First-paint counters for crawlers and no-script visitors; app.js overwrites
// them with the same figures (approved companies, their founders, distinct
// non-empty countries and cities).
$heroStats = $db->query(
    "SELECT COUNT(*) AS companies, COUNT(DISTINCT NULLIF(country, '')) AS countries, COUNT(DISTINCT NULLIF(city, '')) AS cities
     FROM companies WHERE status = 'approved'"
)->fetch();
$heroStats['founders'] = (int)$db->query(
    "SELECT COUNT(*) FROM founders f JOIN companies c ON c.id = f.company_id WHERE c.status = 'approved'"
)->fetchColumn();

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Kerala Founders',
    'url' => 'https://keralafounders.eu/',
    'description' => 'Keralite founders and companies building across the European Union.',
];
$pageTitle = 'Kerala Founders — From Kerala, across Europe';
$metaDescription = 'Keralite founders and companies building across the European Union.';
$canonicalUrl = 'https://keralafounders.eu/';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="hero gridbg"><div class="hero-visual"></div><div class="wrap hero-inner"><div>
<h1>Keralites building<br><span>across the EU.</span></h1>
<p class="hero-copy">A place to discover founders and businesses from Kerala building across Europe in technology and AI to food, manufacturing, healthcare and much more.</p>
<div class="hero-actions"><a class="pill" href="founders.php">Explore the directory →</a><a class="pill light" href="add-company.php">Add your company</a></div>
<div class="hero-stats"><div class="hero-stat"><strong id="statCompanies"><?= (int)$heroStats['companies'] ?></strong><span>Companies</span></div><div class="hero-stat"><strong id="statFounders"><?= (int)$heroStats['founders'] ?></strong><span>Founders</span></div><div class="hero-stat"><strong id="statCountries"><?= (int)$heroStats['countries'] ?></strong><span>Countries</span></div><div class="hero-stat"><strong id="statCities"><?= (int)$heroStats['cities'] ?></strong><span>Cities</span></div></div>
</div></div></section>
<section class="section" style="padding-bottom:0"><div class="wrap">
<div class="section-head"><div class="eyebrow">Explore by category</div><a class="arrow" href="industries.php">View all categories →</a></div>
<div class="category-grid">
<?php foreach ($categoryIcons as $industry => $icon): $n = (int)($industryCounts[$industry] ?? 0); ?>
<a class="category-card" href="industries.php?industry=<?= rawurlencode($industry) ?>">
  <img src="assets/icons/<?= h($icon) ?>.svg" alt="">
  <strong><?= h($industry) ?></strong>
  <span>(<?= $n ?>)</span>
</a>
<?php endforeach; ?>
</div>
</div></section>
<section class="section"><div class="wrap"><div class="section-head"><div><div class="eyebrow">Recently added</div><h2>What Keralites are building.</h2><p class="muted section-intro">Companies and founders building across the European Union.</p></div><a class="arrow" href="founders.php">See the full directory →</a></div><div id="homeCards" class="cards"><?php foreach ($recentCompanies as $c) { echo company_card_html($c); } ?></div></div></section>


<script>document.addEventListener('DOMContentLoaded',()=>{const d=KF.all();document.getElementById('homeCards').innerHTML=d.slice(0,6).map(KFUI.companyCard).join('')})</script>
</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
