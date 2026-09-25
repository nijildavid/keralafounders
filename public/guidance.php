<?php
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
require __DIR__ . '/assets/guidance-helpers.php';

$countries = guidance_load_countries();
$faq = guidance_load_faq();

$faqPreview = array_values(array_filter($faq, fn($e) => empty($e['hold'])));
usort($faqPreview, fn($a, $b) => strcmp($b['last_checked'], $a['last_checked']));
$faqPreview = array_slice($faqPreview, 0, 5);

$robotsMeta = empty($KF_GUIDANCE_NAV_LIVE) ? '<meta name="robots" content="noindex">' : '';
$pageTitle = 'Guidance — Kerala Founders';
$metaDescription = 'Practical, verified knowledge for building, working and navigating business life in Europe — sourced, dated and re-checked every 6 months.';
$canonicalUrl = 'https://keralafounders.eu/guidance.php';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?= $robotsMeta ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<script type="application/ld+json">{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://keralafounders.eu/"},{"@type":"ListItem","position":2,"name":"Guidance","item":"https://keralafounders.eu/guidance.php"}]}</script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script><script src="assets/guidance.js" defer></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">

<section class="section" style="padding-bottom:0"><div class="wrap" style="max-width:780px">
<div class="coming-soon-icon"><img src="assets/icons/guidance-book.svg" alt=""></div>
<div class="eyebrow">Guidance</div>
<h1 style="font-family:Georgia,serif;font-size:52px;line-height:1.08;margin:12px 0 20px">Learn how to navigate it.</h1>
<p class="muted" style="font-size:19px;line-height:1.8;max-width:640px">Practical knowledge for building, working and navigating business life in Europe — compiled from official sources, chambers of commerce and government portals, and re-checked every 6 months.</p>
</div></section>

<section class="section" style="padding-top:40px"><div class="wrap" style="max-width:900px">

<div class="eyebrow" style="margin-bottom:14px">Country guides</div>
<div class="country-grid">
<?php foreach ($countries as $country): ?>
  <?= guidance_country_card_html($country, $KF_COUNTRY_FLAGS) ?>
<?php endforeach; ?>
</div>

<?php if ($faqPreview): ?>
<div class="panel" style="margin-top:40px">
<h2>Recent questions</h2>
<ul style="margin:14px 0;padding-left:20px;color:#57534e;line-height:2">
<?php foreach ($faqPreview as $entry): ?>
  <?= guidance_faq_preview_item_html($entry) ?>
<?php endforeach; ?>
</ul>
<a class="arrow" href="guidance-faq.php">See all questions →</a>
</div>
<?php endif; ?>

<p class="notice guidance-disclaimer" style="margin-top:30px">General information from the sources shown, not advice. Re-checked every 6 months. Check the source and consult a professional before you act. <a href="guidance-method.php">How we check our information →</a></p>

<div class="panel" style="margin-top:20px">
<h2>Have a question, or a country we should cover next?</h2>
<p class="muted" style="margin:0"><a class="arrow" href="mailto:hello@keralafounders.eu">Get in touch →</a></p>
</div>

</div></section>

</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
