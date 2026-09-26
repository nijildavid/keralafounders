<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/reference.php';
require_once __DIR__ . '/../assets/render-helpers.php';
$db = get_db();
$footerTopCountries = fetch_top_countries($db, 10);
?>
<footer>
  <div class="footer-cta">
    <div class="eyebrow">Your place on the map</div>
    <h2>Building something<br>from Europe?</h2>
    <p>Make it easier for fellow Keralites to find you.</p>
    <a class="pill" href="add-company.php">Add your company <span aria-hidden="true">→</span></a>
  </div>
  <div class="footer-main">
    <div class="wrap footer-grid">
      <div class="footer-col footer-brand-col">
        <a class="footer-brand" href="index.php"><img class="footer-brand-mark" src="assets/logo.svg" alt="Kerala Founders">Kerala Founders</a>
        <p class="footer-tagline">Connecting Kerala's global ecosystem of businesses, founders and communities.</p>
      </div>
      <div class="footer-col">
        <h3 class="footer-col-title">Explore</h3>
        <ul>
          <li><a href="founders.php">Companies</a></li>
          <li><a href="business-types.php">Business Types</a></li>
          <li><a href="cities.php">Cities</a></li>
          <li><a href="industries.php">Industries</a></li>
          <li><a href="stories.php">Stories</a></li>
          <?php if (!empty($KF_GUIDANCE_NAV_LIVE)): ?>
          <li><a href="guidance.php">Guidance</a></li>
          <?php else: ?>
          <li><span class="footer-link-disabled" aria-disabled="true">Guidance</span></li>
          <?php endif; ?>
          <li><a href="about.php">About</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3 class="footer-col-title">Countries</h3>
        <ul class="footer-country-list">
          <?php foreach ($footerTopCountries as $country => $n): ?>
          <li><a href="countries.php?country=<?= rawurlencode($country) ?>"><span class="footer-flag" aria-hidden="true"><?= $KF_COUNTRY_FLAGS[$country] ?? '' ?></span><span class="footer-country-name"><?= h($country) ?></span><span class="footer-country-count">(<?= (int)$n ?>)</span></a></li>
          <?php endforeach; ?>
        </ul>
        <a class="arrow" href="countries.php">View all countries →</a>
      </div>
      <div class="footer-col">
        <h3 class="footer-col-title">For Businesses</h3>
        <ul>
          <li><a href="add-company.php">Add Your Company</a></li>
          <li><a href="founders.php">Search &amp; Claim Your Listing</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3 class="footer-col-title">Contact</h3>
        <ul>
          <li><a href="mailto:hello@keralafounders.eu">Contact Us</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-bottom-bar">
    <div class="wrap footer-bottom-inner">
      <div class="footer-copy">© <?= date('Y') ?> Kerala Founders. All rights reserved.</div>
      <nav class="footer-legal">
        <a href="privacy.php">Privacy Policy</a>
        <a href="terms.php">Terms of Use</a>
        <a href="listing-policy.php#accuracy">Disclaimer</a>
        <?php if (!empty($KF_GUIDANCE_NAV_LIVE)): ?><a href="guidance-method.php">How we check our information</a><?php endif; ?>
        <a href="mailto:hello@keralafounders.eu">Contact Us</a>
      </nav>
    </div>
  </div>
</footer>
