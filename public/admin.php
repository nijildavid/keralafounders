<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();

$stmt = $db->query("SELECT * FROM companies WHERE status = 'pending' ORDER BY created_at DESC");
$pending = $stmt->fetchAll();

foreach ($pending as &$row) {
    $founders = $db->prepare('SELECT name, email, linkedin, show_email FROM founders WHERE company_id = ? ORDER BY id');
    $founders->execute([$row['id']]);
    $row['founders'] = $founders->fetchAll();
}
unset($row);

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Kerala Founders</title><meta name="description" content="Keralite founders and companies building across the European Union.">
<link rel="stylesheet" href="assets/style.css"><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<body><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.html"><span class="brand-mark">K</span>Kerala Founders</a>
<nav class="navlinks"><a href="founders.html">Directory</a><a href="countries.html">Explore places</a><a href="about.html">About</a></nav>
<div class="navright"><a class="pill" href="add-company.html">Add your company</a></div>
</div></header><main>
<section class="page-head"><div class="wrap"><div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>Pending submissions.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div><p class="muted">Companies submitted via "Add your company", awaiting approval before they appear in the public directory.</p>
<div style="margin-top:30px">
<?php if (!$pending): ?>
  <div class="panel">No pending submissions.</div>
<?php else: foreach ($pending as $c): ?>
  <div class="panel" style="margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;gap:15px;align-items:center">
      <div>
        <strong><?= h($c['name']) ?></strong>
        <div class="meta"><?= h($c['city']) ?>, <?= h($c['country']) ?> · <?= h($c['industry']) ?></div>
        <div class="meta"><?= h(implode(', ', array_map(fn($f) => $f['name'], $c['founders']))) ?></div>
      </div>
      <div style="display:flex;gap:8px">
        <form method="post" action="api/admin-action.php">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <input type="hidden" name="action" value="approve">
          <button class="pill" type="submit">Approve</button>
        </form>
        <form method="post" action="api/admin-action.php">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <input type="hidden" name="action" value="delete">
          <button class="pill light" type="submit">Delete</button>
        </form>
      </div>
    </div>
    <p class="muted" style="margin-top:12px"><?= h($c['description']) ?></p>
  </div>
<?php endforeach; endif; ?>
</div>
</div></section></main><footer>
  <div class="footer-cta">
    <div class="eyebrow">Your place on the map</div>
    <h2>Building something<br>from Europe?</h2>
    <p>Make it easier for fellow Keralites to find you.</p>
    <a class="pill" href="add-company.html">Add your company <span aria-hidden="true">→</span></a>
  </div>
  <div class="footer-bottom">
    <div class="wrap footer-inner">
      <a class="footer-brand" href="index.html"><span class="footer-brand-mark">K</span>Kerala Founders</a>
      <div class="footer-tagline">From Kerala, across Europe.</div>
      <nav class="footer-links">
        <a href="founders.html">Directory</a>
        <a href="countries.html">Explore places</a>
        <a href="about.html">About</a>
      </nav>
      <div class="footer-copy">© 2026 Kerala Founders</div>
    </div>
  </div>
</footer></body></html>
