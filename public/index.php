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
<html lang="en"><head><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("consent","default",{ad_storage:"denied",ad_user_data:"denied",ad_personalization:"denied",analytics_storage:"denied"});</script><script src="https://cdn.cookiehub.eu/c2/a2366e42.js"></script><script type="text/javascript">document.addEventListener("DOMContentLoaded",function(event){var cpm={};if(window.cookiehub){window.cookiehub.load(cpm);}});</script><!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-EJ9D0P01RH"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","G-EJ9D0P01RH");</script><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kerala Founders — From Kerala, across Europe</title><meta name="description" content="Keralite founders and companies building across the European Union.">
<link rel="canonical" href="https://keralafounders.eu/">
<meta property="og:type" content="website"><meta property="og:site_name" content="Kerala Founders"><meta property="og:title" content="Kerala Founders — From Kerala, across Europe"><meta property="og:description" content="Keralite founders and companies building across the European Union."><meta property="og:url" content="https://keralafounders.eu/">
<meta name="twitter:card" content="summary"><meta name="twitter:title" content="Kerala Founders — From Kerala, across Europe"><meta name="twitter:description" content="Keralite founders and companies building across the European Union.">
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
