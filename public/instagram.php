<?php
// Link-in-bio landing page for the @keralafounders.eu Instagram profile.
// Every link carries a utm_content tag per post type so Google Analytics can
// show which kind of post sends visitors. Not in the sitemap, noindex.
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/assets/render-helpers.php';

$pageTitle = 'Kerala Founders on Instagram';
$metaDescription = 'The latest companies, stories and ways to get listed on Kerala Founders, from our Instagram.';
$canonicalUrl = 'https://keralafounders.eu/instagram.php';

function ig_href(string $path, string $type): string
{
    $sep = strpos($path, '?') === false ? '?' : '&';
    return $path . $sep . 'utm_source=instagram&utm_medium=social&utm_campaign=bio&utm_content=' . rawurlencode($type);
}

$recent = [];
try {
    $recent = fetch_recent_approved_companies(get_db(), 3);
} catch (Throwable $e) {
    $recent = [];
}
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<meta name="robots" content="noindex,follow">
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="section"><div class="wrap" style="max-width:640px">
<div class="eyebrow">From Instagram</div>
<h1 style="font-family:Georgia,serif;font-size:40px;line-height:1.1;margin:12px 0 18px">Welcome from @keralafounders.eu</h1>
<p class="muted" style="font-size:18px;line-height:1.7">The directory of Kerala-origin founders and businesses across Europe. Start here.</p>

<?php if ($recent): ?>
<div class="panel" style="margin-top:28px">
<h2>Newest on the directory</h2>
<?php foreach ($recent as $c): ?>
<div class="founder-row"><a class="arrow" href="<?= h(ig_href('company.php?id=' . rawurlencode($c['id']), 'company')) ?>"><?= h($c['name']) ?> · <?= h($c['city']) ?>, <?= h($c['country']) ?> →</a></div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="panel" style="margin-top:20px">
<h2>More</h2>
<div class="founder-row"><a class="arrow" href="<?= h(ig_href('stories.php', 'story')) ?>">Listen to the Stories podcast →</a></div>
<div class="founder-row"><a class="arrow" href="<?= h(ig_href('cities.php', 'city')) ?>">Browse companies by city →</a></div>
<div class="founder-row"><a class="arrow" href="<?= h(ig_href('add-company.php', 'add_company')) ?>">Add your company →</a></div>
</div>

<p style="margin-top:28px"><a class="pill light" href="https://www.instagram.com/keralafounders.eu/" target="_blank" rel="noopener">Back to Instagram</a></p>
</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
