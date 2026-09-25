<?php
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
require __DIR__ . '/assets/guidance-helpers.php';

$countryFilter = isset($_GET['country']) ? (string)$_GET['country'] : '';
$topicFilter = isset($_GET['topic']) ? (string)$_GET['topic'] : '';
$q = trim((string)($_GET['q'] ?? ''));

$allFaq = guidance_load_faq();
$sourcesById = guidance_load_sources();
$countries = guidance_load_countries();

// Held entries don't render at all here — no placeholder, nothing — this is
// stricter than a held guide section (which shows a "coming soon" note),
// per the build doc's own acceptance-checklist wording that held FAQ
// entries must be absent.
$visible = array_values(array_filter($allFaq, fn($e) => empty($e['hold'])));

if ($countryFilter !== '') {
    $visible = array_values(array_filter($visible, fn($e) => $e['country'] === $countryFilter));
}
if ($topicFilter !== '') {
    $visible = array_values(array_filter($visible, fn($e) => $e['topic'] === $topicFilter));
}
if ($q !== '') {
    $needle = mb_strtolower($q);
    $visible = array_values(array_filter($visible, function ($e) use ($needle) {
        return mb_strpos(mb_strtolower($e['question']), $needle) !== false
            || mb_strpos(mb_strtolower($e['answer']), $needle) !== false;
    }));
}

$topics = array_values(array_unique(array_column($allFaq, 'topic')));
sort($topics);

$faqJsonLd = null;
if ($visible) {
    $faqJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($e) => [
            '@type' => 'Question',
            'name' => $e['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $e['answer']],
        ], $visible),
    ];
}

$siteName = 'Kerala Founders';
$robotsMeta = empty($KF_GUIDANCE_NAV_LIVE) ? '<meta name="robots" content="noindex">' : '';
$canonicalUrl = 'https://keralafounders.eu/guidance-faq.php' . ($countryFilter !== '' ? '?country=' . rawurlencode($countryFilter) : '');
$pageTitle = 'Frequently asked questions — Guidance — Kerala Founders';
$metaDescription = 'Short, sourced answers to questions founders actually ask about starting and running a business in Europe.';

function guidance_faq_filter_url(string $country, string $topic, string $q): string
{
    $params = array_filter(['country' => $country, 'topic' => $topic, 'q' => $q], fn($v) => $v !== '');
    return 'guidance-faq.php' . ($params ? '?' . http_build_query($params) : '');
}
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?= $robotsMeta ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<?php if ($faqJsonLd): ?><script type="application/ld+json"><?= json_encode($faqJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<script type="application/ld+json">{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://keralafounders.eu/"},{"@type":"ListItem","position":2,"name":"Guidance","item":"https://keralafounders.eu/guidance.php"},{"@type":"ListItem","position":3,"name":"FAQ","item":"https://keralafounders.eu/guidance-faq.php"}]}</script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script><script src="assets/guidance.js" defer></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap" style="max-width:820px">
<div class="eyebrow">Guidance</div>
<h1>Frequently asked questions.</h1>
<p class="muted section-intro">Short, sourced answers to questions founders and professionals actually ask.</p>

<form method="get" action="guidance-faq.php" style="margin-top:24px">
<div class="toolbar" style="grid-template-columns:2fr 1fr 1fr auto">
  <input class="field" type="search" name="q" value="<?= h($q) ?>" placeholder="Search questions…">
  <select class="select" name="country" onchange="this.form.submit()">
    <option value="">All countries</option>
    <?php foreach ($countries as $c): ?>
      <option value="<?= h($c['slug']) ?>" <?= $countryFilter === $c['slug'] ? 'selected' : '' ?>><?= h($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <select class="select" name="topic" onchange="this.form.submit()">
    <option value="">All topics</option>
    <?php foreach ($topics as $t): ?>
      <option value="<?= h($t) ?>" <?= $topicFilter === $t ? 'selected' : '' ?>><?= h(ucfirst($t)) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="pill" type="submit">Search</button>
</div>
</form>

<div style="margin-top:24px">
<?php if (!$visible): ?>
  <div class="panel">No questions match this filter yet.</div>
<?php else: foreach ($visible as $entry): ?>
  <?= guidance_faq_entry_html($entry, $sourcesById) ?>
<?php endforeach; endif; ?>
</div>

<p class="notice guidance-disclaimer" style="margin-top:20px">General information from the sources shown, not advice. Re-checked every 6 months. Check the source and consult a professional before you act. <a href="guidance-method.php">How we check our information →</a></p>

</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
