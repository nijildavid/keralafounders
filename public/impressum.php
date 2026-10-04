<?php
require __DIR__ . '/assets/legal-helpers.php';

// null until config/legal.php has a name, street and city (see
// config/legal.example.php); the page then shows a "registration in
// progress" notice instead of the operator's details.
$legal = kf_legal_details();

function legal_h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$pageTitle = 'Impressum — Kerala Founders';
$metaDescription = 'Legal notice (Impressum) for Kerala Founders: who runs this website and how to reach them.';
$canonicalUrl = 'https://keralafounders.eu/impressum';
$country = $legal !== null ? trim((string)($legal['country'] ?? '')) : '';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="section"><div class="wrap" style="max-width:850px">
<div class="eyebrow">Legal notice</div>
<h1 style="font-family:Georgia,serif;font-size:55px;margin:12px 0 6px">Impressum</h1>
<p class="hint" style="margin-bottom:25px">Information in line with § 5 Digitale-Dienste-Gesetz (DDG).</p>

<?php if ($legal === null): ?>
<div class="panel">
<h2>Who runs this website</h2>
<p class="muted" style="margin:0;line-height:1.8">Kerala Founders is currently being set up as a registered business. The operator's full legal name and address will be published on this page as soon as the registration is complete.</p>
</div>

<div class="panel" style="margin-top:20px">
<h2>Contact</h2>
<p class="muted" style="margin:0;line-height:1.8">
Email: <a class="arrow" href="mailto:hello@keralafounders.eu">hello@keralafounders.eu</a><br>
Contact form: <a class="arrow" href="contact.php">keralafounders.eu/contact.php</a>
</p>
</div>
<?php else: ?>
<div class="panel">
<h2>Who runs this website</h2>
<p class="muted" style="margin:0;line-height:1.8">
<strong style="color:var(--ink)"><?= legal_h($legal['name']) ?></strong><br>
<?= legal_h($legal['street']) ?><br>
<?= legal_h($legal['city']) ?><?= $country !== '' ? '<br>' . legal_h($country) : '' ?>
</p>
</div>

<div class="panel" style="margin-top:20px">
<h2>Contact</h2>
<p class="muted" style="margin:0;line-height:1.8">
Email: <a class="arrow" href="mailto:<?= legal_h($legal['email']) ?>"><?= legal_h($legal['email']) ?></a><br>
<?php if (trim((string)($legal['phone'] ?? '')) !== ''): ?>Phone: <?= legal_h($legal['phone']) ?><br><?php endif; ?>
Contact form: <a class="arrow" href="contact.php">keralafounders.eu/contact.php</a>
</p>
</div>

<?php if (trim((string)($legal['register'] ?? '')) !== '' || trim((string)($legal['vat_id'] ?? '')) !== ''): ?>
<div class="panel" style="margin-top:20px">
<h2>Registration and tax</h2>
<p class="muted" style="margin:0;line-height:1.8">
<?php if (trim((string)($legal['register'] ?? '')) !== ''): ?>Registration: <?= legal_h($legal['register']) ?><br><?php endif; ?>
<?php if (trim((string)($legal['vat_id'] ?? '')) !== ''): ?>VAT ID: <?= legal_h($legal['vat_id']) ?><br><?php endif; ?>
</p>
</div>
<?php endif; ?>

<div class="panel" style="margin-top:20px">
<h2>Responsible for content</h2>
<p class="muted" style="margin:0;line-height:1.8">Responsible for the content of this website (§ 18 Abs. 2 Medienstaatsvertrag): <?= legal_h($legal['name']) ?>, address as above.</p>
</div>
<?php endif; ?>

<p class="hint" style="margin-top:25px">See also our <a href="privacy.php">Privacy Policy</a>, <a href="terms.php">Terms of Use</a> and <a href="listing-policy.php#accuracy">Disclaimer</a>.</p>

</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
