<?php
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
require __DIR__ . '/assets/guidance-helpers.php';

$slug = isset($_GET['country']) ? (string)$_GET['country'] : '';
$countries = guidance_load_countries();

$country = null;
foreach ($countries as $c) {
    if ($c['slug'] === $slug) {
        $country = $c;
        break;
    }
}

$siteName = 'Kerala Founders';
$robotsMeta = empty($KF_GUIDANCE_NAV_LIVE) ? '<meta name="robots" content="noindex">' : '';
$canonicalUrl = 'https://keralafounders.eu/guidance-country.php?country=' . rawurlencode($slug);

if ($country === null) {
    http_response_code(404);
    $pageTitle = 'Guide not found — ' . $siteName;
    $metaDescription = 'This Guidance page could not be found.';
    $guide = null;
    $fullRender = false;
} else {
    $guide = guidance_load_guide($country['slug']);
    $fullRender = guidance_should_render_full($country['status'], $guide !== null);
    $pageTitle = ($fullRender ? $guide['title'] : 'How to start a company in ' . $country['name']) . ' — ' . $siteName;
    $metaDescription = $fullRender
        ? truncate_meta($guide['summary'])
        : 'This country guide isn\'t ready yet.';
}

$sourcesById = guidance_load_sources();
$usedSourceIds = [];
$sectionsHtml = '';
$overviewHtml = '';
$articleJsonLd = null;

if ($fullRender) {
    foreach ($guide['sections'] as $section) {
        if ($section['id'] === 'overview') {
            $text = $section['expert_interpretation'][0]['text'] ?? '';
            $overviewHtml = '<p class="muted" style="font-size:18px;line-height:1.85;max-width:760px">' . h($text) . '</p>';
            continue;
        }
        [$html, $usedSourceIds] = guidance_section_html($section, $sourcesById, $usedSourceIds);
        $sectionsHtml .= $html;
    }
    $articleJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $guide['title'],
        'dateModified' => $guide['last_checked'],
        'author' => ['@type' => 'Organization', 'name' => $siteName],
    ];
}

$breadcrumbTrail = [
    ['name' => 'Home', 'url' => 'https://keralafounders.eu/'],
    ['name' => 'Guidance', 'url' => 'https://keralafounders.eu/guidance.php'],
];
if ($country !== null) {
    $breadcrumbTrail[] = ['name' => $country['name'], 'url' => $canonicalUrl];
}
$breadcrumbJsonLd = breadcrumb_json_ld($breadcrumbTrail);

$faq = $country !== null ? guidance_load_faq() : [];
$relatedFaq = array_values(array_filter($faq, fn($e) => $e['country'] === ($country['slug'] ?? null) && empty($e['hold'])));
$relatedFaq = array_slice($relatedFaq, 0, 5);

$nextCountryTeaser = null;
if ($country !== null) {
    foreach ($countries as $c) {
        if ($c['status'] === 'coming_soon' && $c['slug'] !== $country['slug']) {
            $nextCountryTeaser = $c;
            break;
        }
    }
}
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?= $robotsMeta ?>
<?php $ogType = 'article'; include __DIR__ . '/partials/meta-tags.php'; ?>
<?php if ($articleJsonLd): ?><script type="application/ld+json"><?= json_encode($articleJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script><script src="assets/guidance.js" defer></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head">

<?php if ($country === null): ?>
<div class="wrap" style="max-width:820px">
  <div class="eyebrow">Guidance</div>
  <h1>Guide not found</h1>
  <p class="muted section-intro">We couldn't find that country guide.</p>
  <a class="arrow" href="guidance.php">← Back to Guidance</a>
</div>

<?php elseif (!$fullRender): ?>
<div class="wrap" style="max-width:820px">
  <div class="eyebrow">Guidance</div>
  <h1>How to start a company in <?= h($country['name']) ?></h1>
  <div class="coming-soon-panel" style="margin-top:20px">
    <div class="eyebrow">Coming soon</div>
    <h2 style="font-size:22px;margin:10px 0 12px">This guide isn't ready yet.</h2>
    <p class="muted" style="margin:0"><?php if (!empty($country['target_month'])): ?>Target: <?= h(guidance_format_month($country['target_month'])) ?>.<?php else: ?>No date yet — check back soon.<?php endif; ?></p>
  </div>
  <a class="arrow" href="guidance.php" style="margin-top:16px;display:inline-block">← Back to Guidance</a>
</div>

<?php else: ?>
<div class="wrap">
  <div class="eyebrow">Guidance</div>
  <h1><?= h($guide['title']) ?></h1>
  <?= guidance_date_stamp_html($guide['last_checked'], $guide['next_check_due']) ?>
  <?= guidance_outdated_banner_html($guide['last_checked'], $guide['next_check_due']) ?>
  <?= guidance_disclaimer_html('full', $guide['last_checked'], $guide['next_check_due']) ?>

  <?= $overviewHtml ?>

  <div class="guidance-body">
    <div class="guidance-main">
      <?= $sectionsHtml ?>

      <?= guidance_sources_box_html($usedSourceIds, $sourcesById) ?>

      <?= guidance_feedback_widget_html('guide', $guide['slug']) ?>

      <div class="panel" style="margin-top:30px">
      <h2>Related</h2>
      <?php if ($relatedFaq): ?>
      <div class="eyebrow" style="margin-bottom:8px">Questions about <?= h($country['name']) ?></div>
      <ul style="margin:0 0 16px;padding-left:20px;color:#57534e;line-height:2">
      <?php foreach ($relatedFaq as $entry): ?>
        <?= guidance_faq_preview_item_html($entry) ?>
      <?php endforeach; ?>
      </ul>
      <?php endif; ?>
      <p class="muted" style="margin:0 0 8px"><a class="arrow" href="countries.php?country=<?= rawurlencode($country['directory_country_name']) ?>">Kerala founders in <?= h($country['name']) ?> →</a></p>
      <?php if ($nextCountryTeaser): ?>
      <p class="muted" style="margin:0">Coming next: <strong><?= h($nextCountryTeaser['name']) ?></strong><?php if (!empty($nextCountryTeaser['target_month'])): ?> (<?= h(guidance_format_month($nextCountryTeaser['target_month'])) ?>)<?php endif; ?></p>
      <?php endif; ?>
      </div>
    </div>
    <aside class="guidance-side" aria-label="Contents">
      <?= guidance_toc_html($guide['sections']) ?>
    </aside>
  </div>

  <?= guidance_disclaimer_html('full', $guide['last_checked'], $guide['next_check_due']) ?>
</div>
<?php endif; ?>

</section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
