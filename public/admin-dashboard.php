<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

$totalCompanies = (int)$db->query('SELECT COUNT(*) FROM companies')->fetchColumn();
$pendingCompanies = (int)$db->query("SELECT COUNT(*) FROM companies WHERE status = 'pending'")->fetchColumn();
$verifiedCompanies = (int)$db->query('SELECT COUNT(*) FROM companies WHERE verified = 1')->fetchColumn();
$pendingClaims = (int)$db->query("SELECT COUNT(*) FROM claim_requests WHERE status = 'pending'")->fetchColumn();
$contactsFound = (int)$db->query('SELECT COUNT(*) FROM companies WHERE contact_email IS NOT NULL')->fetchColumn();
$emailedCount = (int)$db->query("SELECT COUNT(*) FROM companies WHERE outreach_status = 'sent'")->fetchColumn();
$toEmailCount = (int)$db->query("SELECT COUNT(*) FROM companies WHERE contact_email IS NOT NULL AND outreach_status = 'not_contacted'")->fetchColumn();

$activeAdminPage = 'dashboard';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Kerala Founders</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.png" alt="Kerala Founders">Kerala Founders</a>
<nav class="navlinks"><a href="founders.php">Directory</a><a href="countries.php">Explore places</a><a href="about.html">About</a></nav>
<div class="navright"><a class="pill" href="add-company.html">Add your company</a></div>
</div></header><main id="main">
<section class="page-head"><div class="wrap">
<div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>Dashboard.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div>
<p class="muted">Overview of submissions, claims and the outreach/verification pipeline.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>
<div class="stats" style="margin-top:24px;border-radius:18px;border:1px solid var(--line)"><div class="wrap stats-grid" style="padding:0">
  <div class="stat"><strong><?= $totalCompanies ?></strong><span>Companies</span></div>
  <div class="stat"><strong><?= $pendingCompanies ?></strong><span>Pending review</span></div>
  <div class="stat"><strong><?= $verifiedCompanies ?></strong><span>Owner verified</span></div>
  <div class="stat"><strong><?= $pendingClaims ?></strong><span>Pending claims</span></div>
</div></div>
<div class="stats" style="margin-top:16px;border-radius:18px;border:1px solid var(--line)"><div class="wrap stats-grid" style="padding:0">
  <div class="stat"><strong><?= $contactsFound ?></strong><span>Contact emails found</span></div>
  <div class="stat"><strong><?= $emailedCount ?></strong><span>Verification emails sent</span></div>
  <div class="stat"><strong><?= $toEmailCount ?></strong><span>Found, not yet emailed</span></div>
  <div class="stat"><strong>&nbsp;</strong><span></span></div>
</div></div>
<div style="margin-top:24px;display:flex;gap:16px;flex-wrap:wrap">
  <a class="panel" style="flex:1;min-width:220px;text-decoration:none;color:inherit" href="admin.php">
    <h2 style="font-size:18px">All submissions →</h2>
    <p class="muted" style="margin-top:6px">Review, approve, edit and mark companies as emailed/verified.</p>
  </a>
  <a class="panel" style="flex:1;min-width:220px;text-decoration:none;color:inherit" href="admin-claims.php">
    <h2 style="font-size:18px">Listing claims →</h2>
    <p class="muted" style="margin-top:6px">Correction and verification requests from business owners.</p>
  </a>
</div>
</div></section></main><footer>
  <div class="footer-bottom">
    <div class="wrap footer-inner">
      <a class="footer-brand" href="index.php"><img class="footer-brand-mark" src="assets/logo.png" alt="">Kerala Founders</a>
      <div class="footer-tagline">From Kerala, across Europe.</div>
      <nav class="footer-links">
        <a href="founders.php">Directory</a>
        <a href="countries.php">Explore places</a>
        <a href="about.html">About</a>
      </nav>
      <div class="footer-copy">© 2026 Kerala Founders</div>
    </div>
  </div>
</footer></body></html>
