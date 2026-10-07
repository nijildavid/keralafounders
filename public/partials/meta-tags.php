<?php
// Shared <title>/meta description/OG/Twitter block. Set $pageTitle,
// $metaDescription, $canonicalUrl (all raw/unescaped) before including this
// — plus optional $ogType (default 'website') and $canonicalId (only
// founders.php needs this, for its pagination JS), $ogTitle (preview title
// when it should differ from the <title>) and $ogImage — then include right
// after head-common.php.
require_once __DIR__ . '/../assets/render-helpers.php';
$ogType = $ogType ?? 'website';
$ogTitle = $ogTitle ?? $pageTitle;
$ogImage = $ogImage ?? 'https://keralafounders.eu/assets/og-image.png';
$canonicalAttr = isset($canonicalId) ? ' id="' . h($canonicalId) . '"' : '';
?>
<title><?= h($pageTitle) ?></title><meta name="description" content="<?= h($metaDescription) ?>">
<link rel="canonical"<?= $canonicalAttr ?> href="<?= h($canonicalUrl) ?>">
<meta property="og:type" content="<?= h($ogType) ?>"><meta property="og:site_name" content="Kerala Founders"><meta property="og:title" content="<?= h($ogTitle) ?>"><meta property="og:description" content="<?= h($metaDescription) ?>"><meta property="og:url" content="<?= h($canonicalUrl) ?>"><meta property="og:image" content="<?= h($ogImage) ?>"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= h($ogTitle) ?>"><meta name="twitter:description" content="<?= h($metaDescription) ?>"><meta name="twitter:image" content="<?= h($ogImage) ?>">
