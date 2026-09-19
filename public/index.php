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

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Kerala Founders',
    'url' => 'https://keralafounders.eu/',
    'description' => 'Keralite founders and companies building across the European Union.',
];
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<title>Kerala Founders — From Kerala, across Europe</title><meta name="description" content="Keralite founders and companies building across the European Union.">
<link rel="canonical" href="https://keralafounders.eu/">
<meta property="og:type" content="website"><meta property="og:site_name" content="Kerala Founders"><meta property="og:title" content="Kerala Founders — From Kerala, across Europe"><meta property="og:description" content="Keralite founders and companies building across the European Union."><meta property="og:url" content="https://keralafounders.eu/"><meta property="og:image" content="https://keralafounders.eu/assets/og-image.png"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="Kerala Founders — From Kerala, across Europe"><meta name="twitter:description" content="Keralite founders and companies building across the European Union."><meta name="twitter:image" content="https://keralafounders.eu/assets/og-image.png">
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="hero gridbg"><div class="hero-visual"></div><div class="wrap hero-inner"><div>
<h1>Keralites building<br><span>across the EU.</span></h1>
<p class="hero-copy">A place to discover founders and businesses from Kerala building across Europe in technology and AI to food, manufacturing, healthcare and much more.</p>
<div class="hero-actions"><a class="pill" href="founders.php">Explore the directory →</a><a class="pill light" href="add-company.php">Add your company</a></div>
<div class="hero-stats"><div class="hero-stat"><strong id="statCompanies">0</strong><span>Companies</span></div><div class="hero-stat"><strong id="statFounders">0</strong><span>Founders</span></div><div class="hero-stat"><strong id="statCountries">0</strong><span>Countries</span></div><div class="hero-stat"><strong id="statCities">0</strong><span>Cities</span></div></div>
</div></div></section>
<section class="section" style="padding-bottom:0"><div class="wrap">
<div class="section-head"><div class="eyebrow">Explore by category</div><a class="arrow" href="founders.php">View all categories →</a></div>
<div class="category-grid">
<?php foreach ($categoryIcons as $industry => $icon): $n = (int)($industryCounts[$industry] ?? 0); ?>
<a class="category-card" href="founders.php?industry=<?= rawurlencode($industry) ?>">
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
