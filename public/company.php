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
    $place = implode(', ', array_filter([$company['city'], $company['country']]));
    $ogTitle = $company['name'] . ($place !== '' ? ' — ' . $place : '') . ' — ' . $siteName;
    $shareText = $company['name'] . ($place !== '' ? ' (' . $place . ')' : '') . ' on Kerala Founders: ' . $canonicalUrl;
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
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
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
      <?php
        $tier = company_contact_tier($company);
        $siteRaw = $company['website'] ?? '';
        $siteHref = '';
        $siteLabel = '';
        if ($siteRaw && strpos($siteRaw, '.example') === false) {
            $siteHref = preg_match('#^https?://#i', $siteRaw) ? $siteRaw : 'https://' . $siteRaw;
            $siteLabel = rtrim(preg_replace('#^https?://(www\.)?#i', '', $siteRaw), '/');
        }
        $foundersLine = implode(', ', array_column($founders, 'name'));
      ?>
      <div class="company-detail-top">
        <div class="logo company-detail-logo"><?= h(initials($company['name'])) ?></div>
        <div class="company-detail-title">
          <h1><?= h($company['name']) ?></h1>
          <div class="company-detail-sub">
            <span id="companyVerified"><?= verified_chip_html($tier, 'lg') ?></span>
            <?php if ($foundersLine !== ''): ?><span class="muted">Founded by <?= h($foundersLine) ?></span><?php endif; ?>
          </div>
        </div>
        <?php if ($siteHref): ?>
        <a class="pill light company-detail-cta" href="<?= h($siteHref) ?>" target="_blank" rel="noopener"><span class="company-detail-cta-label">Visit <?= h($siteLabel) ?></span> ↗</a>
        <?php endif; ?>
      </div>

      <div class="company-detail-meta">
        <?php if ($company['country']): ?><span class="chip"><?= h($company['country']) ?></span><?php endif; ?>
        <?php if ($company['city']): ?><span class="chip"><?= h($company['city']) ?></span><?php endif; ?>
        <?php if ($company['industry']): ?><span class="chip"><?= h($company['industry']) ?></span><?php endif; ?>
        <?php if (!empty($company['business_type'])): ?><span class="chip"><?= h($company['business_type']) ?></span><?php endif; ?>
        <?php if ($company['size']): ?><span class="chip"><?= h($company['size']) ?></span><?php endif; ?>
        <?php if ($branches): ?>
        <span class="muted company-detail-also">Also in</span>
        <?php foreach ($branches as $b): ?><span class="chip"><?= h($b) ?></span><?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="company-detail-about">
        <div class="eyebrow">About</div>
        <p><?= h($company['description']) ?></p>
        <?php if (!empty($company['industry_detail'])): ?><p class="company-detail-focus"><strong>Focus:</strong> <?= h($company['industry_detail']) ?></p><?php endif; ?>
      </div>

      <?php
        $showInsta = !empty($company['instagram']) && ($company['instagram_confidence'] ?? null) !== 'medium';
        if ($company['location'] || $showInsta || $founders || $guidanceGuideSlug):
      ?>
      <dl class="company-detail-facts">
        <?php if ($company['location']): ?>
        <div><dt class="eyebrow">Address</dt><dd><?= h($company['location']) ?></dd></div>
        <?php endif; ?>
        <?php if ($showInsta): ?>
        <div><dt class="eyebrow">Instagram</dt><dd><a class="arrow" style="margin:0" href="https://www.instagram.com/<?= rawurlencode($company['instagram']) ?>/" target="_blank" rel="noopener">@<?= h($company['instagram']) ?></a></dd></div>
        <?php endif; ?>
        <?php foreach ($founders as $f): ?>
        <div><dt class="eyebrow">Founder</dt><dd><?= h($f['name']) ?><?php if (!empty($f['show_email']) && !empty($f['email'])): ?><br><a class="arrow" style="margin:0" href="mailto:<?= h($f['email']) ?>"><?= h($f['email']) ?></a><?php endif; ?></dd></div>
        <?php endforeach; ?>
        <?php if ($guidanceGuideSlug): ?>
        <div><dt class="eyebrow">Starting up here?</dt><dd><a class="arrow" style="margin:0" href="guidance-country.php?country=<?= rawurlencode($guidanceGuideSlug) ?>">Guide: <?= h($company['country']) ?> →</a></dd></div>
        <?php endif; ?>
      </dl>
      <?php endif; ?>

      <?php if ($company['verified']):
        $kitBase = 'share-kit.php?id=' . rawurlencode($slug);
        $badgeSnippet = '<a href="' . $canonicalUrl . '"><img src="https://keralafounders.eu/' . $kitBase . '&type=badge" alt="Featured on Kerala Founders" width="220" height="56"></a>'; ?>
      <div class="share-kit">
        <div class="eyebrow">Share kit</div>
        <p class="muted" style="margin:6px 0 var(--space-4)">Featured on Kerala Founders. Add this badge to your own website.</p>
        <p style="margin:0 0 var(--space-4)"><img src="<?= h($kitBase) ?>&amp;type=badge" alt="Featured on Kerala Founders badge" width="220" height="56"></p>
        <div class="code-box">
          <code id="kitSnippet"><?= h($badgeSnippet) ?></code>
          <button type="button" class="copy-btn" id="kitCopyBtn" aria-label="Copy badge code" title="Copy badge code" data-kf-event="share_click" data-kf-label="copy_badge_code">
            <svg class="copy-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><rect x="9" y="9" width="11" height="11" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M5 15V6a2 2 0 0 1 2-2h9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <svg class="check-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M5 12.5l4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <span class="copy-msg" id="kitCopyMsg" role="status" aria-live="polite"></span>
        </div>
      </div>
      <script>
      (function(){
        var btn=document.getElementById('kitCopyBtn'),code=document.getElementById('kitSnippet'),msg=document.getElementById('kitCopyMsg');
        if(!btn||!code)return;
        function done(ok){
          msg.textContent=ok?'Copied':'Press Ctrl+C to copy';
          if(ok){btn.classList.add('is-copied');}
          setTimeout(function(){btn.classList.remove('is-copied');msg.textContent='';},2000);
        }
        function fallback(){
          var r=document.createRange();r.selectNodeContents(code);
          var sel=window.getSelection();sel.removeAllRanges();sel.addRange(r);
          var ok=false;try{ok=document.execCommand('copy');}catch(e){}
          done(ok);
        }
        btn.addEventListener('click',function(){
          var text=code.textContent;
          if(navigator.clipboard&&window.isSecureContext){
            navigator.clipboard.writeText(text).then(function(){done(true);},fallback);
          }else{fallback();}
        });
      })();
      </script>
      <?php endif; ?>

      <div class="company-detail-foot">
        <?php if (!$company['verified']): ?>
        <p class="company-detail-foot-claim"><strong>Is this your business?</strong> <a class="arrow" href="claim.php?id=<?= rawurlencode($slug) ?>">Claim this listing →</a></p>
        <?php elseif ($tier === 'owner'): ?>
        <p class="company-detail-foot-claim"><strong>Spot something outdated?</strong> <a class="arrow" href="claim.php?id=<?= rawurlencode($slug) ?>">Suggest an edit →</a></p>
        <?php endif; ?>
        <div class="company-detail-foot-actions">
          <a class="muted company-detail-report" href="contact.php?topic=remove&amp;listing=<?= rawurlencode($slug) ?>">Request a correction or removal</a>
          <a class="company-detail-share" href="https://wa.me/?text=<?= rawurlencode($shareText) ?>" target="_blank" rel="noopener" data-kf-event="share_click" data-kf-share="whatsapp">Share on WhatsApp</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
