<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/render-helpers.php';
require __DIR__ . '/assets/guidance-helpers.php';
$db = get_db();

$slug = (string)($_GET['id'] ?? '');
$stmt = $db->prepare("SELECT * FROM companies WHERE slug = ? AND status = 'approved'");
$stmt->execute([$slug]);
$company = $stmt->fetch();

$founders = [];
$branches = [];
if ($company) {
    $fs = $db->prepare('SELECT name, email, linkedin, show_email FROM founders WHERE company_id = ? ORDER BY id');
    $fs->execute([$company['id']]);
    $founders = $fs->fetchAll();

    $bs = $db->prepare('SELECT country FROM branches WHERE company_id = ? ORDER BY id');
    $bs->execute([$company['id']]);
    $branches = $bs->fetchAll(PDO::FETCH_COLUMN);
} else {
    http_response_code(404);
}

$siteName = 'Kerala Founders';
if ($company) {
    $pageTitle = $company['name'] . ' — ' . $siteName;
    $metaDescription = truncate_meta($company['description']);
    $canonicalUrl = 'https://keralafounders.eu/company.php?id=' . rawurlencode($slug);
} else {
    $pageTitle = 'Company not found — ' . $siteName;
    $metaDescription = 'This company could not be found in the Kerala Founders directory.';
    $canonicalUrl = 'https://keralafounders.eu/company.php';
}

$jsonLd = null;
if ($company) {
    $address = ['@type' => 'PostalAddress', 'addressCountry' => $company['country']];
    if ($company['city']) {
        $address['addressLocality'] = $company['city'];
    }
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $company['name'],
        'description' => $company['description'],
        'address' => $address,
    ];
    if ($company['website'] && strpos($company['website'], '.example') === false) {
        $jsonLd['url'] = preg_match('#^https?://#i', $company['website'])
            ? $company['website']
            : 'https://' . $company['website'];
    }
    if ($founders) {
        $jsonLd['founder'] = array_map(fn($f) => ['@type' => 'Person', 'name' => $f['name']], $founders);
    }
}

$guidanceGuideSlug = null;
if ($company && !empty($KF_GUIDANCE_NAV_LIVE)) {
    foreach (guidance_load_countries() as $gc) {
        if ($gc['status'] === 'live' && $gc['directory_country_name'] === $company['country']) {
            $guidanceGuideSlug = $gc['slug'];
            break;
        }
    }
}

$breadcrumbJsonLd = $company ? breadcrumb_json_ld([
    ['name' => 'Home', 'url' => 'https://keralafounders.eu/'],
    ['name' => 'Founders', 'url' => 'https://keralafounders.eu/founders.php'],
    ['name' => $company['name'], 'url' => $canonicalUrl],
]) : null;
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<?php if ($jsonLd): ?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<?php if ($breadcrumbJsonLd): ?><script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>

<main id="main">
<section class="profile">
  <div class="wrap">
    <a class="arrow" href="founders.php">← Back to directory</a>

    <?php if (!$company): ?>
    <div class="company-detail">
      <h1>Company not found</h1>
      <p>This company could not be found in the directory.</p>
    </div>
    <?php else: ?>
    <div class="company-detail">
      <span id="companyVerified" style="position:absolute;top:24px;right:24px"><?= $company['verified'] ? verified_badge_strong_html() : verified_chip_html(false) ?></span>
      <div class="company-detail-top">
        <div class="logo company-detail-logo"><?= h(initials($company['name'])) ?></div>
        <div>
          <h1><?= h($company['name']) ?></h1>
          <p class="muted"><?= h(implode(', ', array_column($founders, 'name'))) ?></p>
        </div>
      </div>

      <div class="company-detail-meta">
        <?php if ($company['country']): ?><span class="chip"><?= h($company['country']) ?></span><?php endif; ?>
        <?php if ($company['city']): ?><span class="chip"><?= h($company['city']) ?></span><?php endif; ?>
        <?php if ($company['industry']): ?><span class="chip"><?= h($company['industry']) ?></span><?php endif; ?>
        <?php if (!empty($company['business_type'])): ?><span class="chip"><?= h($company['business_type']) ?></span><?php endif; ?>
        <?php if ($company['size']): ?><span class="chip"><?= h($company['size']) ?></span><?php endif; ?>
      </div>
      <?php if ($branches): ?>
      <div class="company-detail-meta" style="margin-top:10px">
        <span class="muted" style="font-size:13px;align-self:center">Also operates in:</span>
        <?php foreach ($branches as $b): ?><span class="chip"><?= h($b) ?></span><?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="company-detail-body">
        <div>
          <div class="eyebrow">About the company</div>
          <p><?= h($company['description']) ?></p>
        </div>
        <div class="company-detail-side">
          <?php if ($company['industry']): ?>
          <div class="side-section">
            <div class="eyebrow">Industry</div>
            <p><?= h($company['industry']) ?></p>
            <?php if (!empty($company['industry_detail'])): ?><p class="muted" style="font-size:13px;margin-top:2px"><?= h($company['industry_detail']) ?></p><?php endif; ?>
          </div>
          <?php endif; ?>
          <?php if (!empty($company['business_type'])): ?>
          <div class="side-section">
            <div class="eyebrow">Business type</div>
            <p><?= h($company['business_type']) ?></p>
          </div>
          <?php endif; ?>
          <?php if ($company['country']): ?>
          <div class="side-section">
            <div class="eyebrow">Country</div>
            <p><?= h($company['country']) ?></p>
            <?php if ($guidanceGuideSlug): ?>
            <p class="muted" style="font-size:13px;margin-top:4px"><a class="arrow" href="guidance-country.php?country=<?= rawurlencode($guidanceGuideSlug) ?>">How to start a company in <?= h($company['country']) ?> →</a></p>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <?php if ($company['location']): ?>
          <div class="side-section">
            <div class="eyebrow">Address</div>
            <p><?= h($company['location']) ?></p>
          </div>
          <?php endif; ?>
          <div class="side-section">
            <div class="eyebrow">Website</div>
            <?php
              $raw = $company['website'] ?? '';
              if ($raw && strpos($raw, '.example') === false):
                  $href = preg_match('#^https?://#i', $raw) ? $raw : 'https://' . $raw;
            ?>
            <a class="arrow" href="<?= h($href) ?>" target="_blank" rel="noopener"><?= h(preg_replace('#^https?://#i', '', $raw)) ?></a>
            <?php else: ?>
            <span><?= $raw ? h($raw) : 'Website not provided' ?></span>
            <?php endif; ?>
          </div>
          <?php if ($founders): ?>
          <div class="side-section">
            <div class="eyebrow">Founders</div>
            <?php foreach ($founders as $f): ?>
            <div class="founder-mini">
              <div style="font-weight:600"><?= h($f['name']) ?></div>
              <?php if (!empty($f['show_email']) && !empty($f['email'])): ?>
              <a class="arrow" href="mailto:<?= h($f['email']) ?>" style="margin-top:2px;display:inline-block"><?= h($f['email']) ?></a>
              <?php else: ?>
              <span class="chip" style="font-size:11px;margin-top:4px;display:inline-block">Email hidden</span>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div style="margin-top:20px">
        <?php if (!$company['verified']): ?>
        <div class="eyebrow">Is this your business?</div>
        <h2 style="font-size:20px;margin:6px 0 8px">Claim this listing</h2>
        <p class="muted" style="margin:0 0 16px">Confirm your details are correct, add anything missing, and get the verified badge.</p>
        <?php else: ?>
        <div class="eyebrow">Spot something outdated?</div>
        <p class="muted" style="margin:6px 0 16px">Let us know what's changed and we'll update the listing.</p>
        <?php endif; ?>
        <a class="pill light" href="claim.php?id=<?= rawurlencode($slug) ?>"><?= $company['verified'] ? 'Suggest an edit' : 'Claim this listing' ?> →</a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
