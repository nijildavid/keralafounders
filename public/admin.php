<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();
$csrfToken = csrf_token();

$statusFilter = $_GET['status'] ?? 'all';
if (!in_array($statusFilter, ['all', 'pending', 'approved'], true)) {
    $statusFilter = 'all';
}
$verifiedFilter = $_GET['verified'] ?? 'all';
if (!in_array($verifiedFilter, ['all', 'verified', 'unverified'], true)) {
    $verifiedFilter = 'all';
}
$outreachFilter = $_GET['outreach'] ?? 'all';
if (!in_array($outreachFilter, ['all', 'ready', 'sent', 'none'], true)) {
    $outreachFilter = 'all';
}
$q = trim((string)($_GET['q'] ?? ''));

$where = [];
$params = [];
if ($statusFilter !== 'all') {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}
if ($verifiedFilter === 'verified') {
    $where[] = 'verified = 1';
} elseif ($verifiedFilter === 'unverified') {
    $where[] = 'verified = 0';
}
if ($outreachFilter === 'ready') {
    $where[] = "contact_email IS NOT NULL AND outreach_status = 'not_contacted'";
} elseif ($outreachFilter === 'sent') {
    $where[] = "outreach_status = 'sent'";
} elseif ($outreachFilter === 'none') {
    $where[] = 'contact_email IS NULL';
}
if ($q !== '') {
    $where[] = '(name LIKE ? OR slug LIKE ? OR city LIKE ? OR country LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}

$sql = 'SELECT * FROM companies';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= " ORDER BY (status = 'pending') DESC, created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$companies = $stmt->fetchAll();

function admin_filter_url(string $status, string $verified, string $outreach, string $q = ''): string
{
    $url = 'admin.php?status=' . urlencode($status) . '&verified=' . urlencode($verified) . '&outreach=' . urlencode($outreach);
    if ($q !== '') {
        $url .= '&q=' . urlencode($q);
    }
    return $url;
}

$currentUrl = admin_filter_url($statusFilter, $verifiedFilter, $outreachFilter, $q);

foreach ($companies as &$row) {
    $founders = $db->prepare('SELECT name, email, linkedin, show_email FROM founders WHERE company_id = ? ORDER BY id');
    $founders->execute([$row['id']]);
    $row['founders'] = $founders->fetchAll();
}
unset($row);

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

$activeAdminPage = 'submissions';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Kerala Founders</title><meta name="description" content="Keralite founders and companies building across the European Union."><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap"><div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>All submissions.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div><p class="muted">Every company ever submitted via "Add your company", with its current status.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>
<form method="get" action="admin.php" style="margin-top:20px;max-width:360px">
  <input type="hidden" name="status" value="<?= h($statusFilter) ?>">
  <input type="hidden" name="verified" value="<?= h($verifiedFilter) ?>">
  <input type="hidden" name="outreach" value="<?= h($outreachFilter) ?>">
  <input class="field" type="search" name="q" value="<?= h($q) ?>" placeholder="Search by name, city or country…" style="margin:0">
</form>
<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-top:16px">
  <div class="toggle">
    <a href="<?= admin_filter_url('all', $verifiedFilter, $outreachFilter, $q) ?>" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">All status</a>
    <a href="<?= admin_filter_url('pending', $verifiedFilter, $outreachFilter, $q) ?>" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="<?= admin_filter_url('approved', $verifiedFilter, $outreachFilter, $q) ?>" class="<?= $statusFilter === 'approved' ? 'active' : '' ?>">Approved</a>
  </div>
  <div class="toggle">
    <a href="<?= admin_filter_url($statusFilter, 'all', $outreachFilter, $q) ?>" class="<?= $verifiedFilter === 'all' ? 'active' : '' ?>">All</a>
    <a href="<?= admin_filter_url($statusFilter, 'verified', $outreachFilter, $q) ?>" class="<?= $verifiedFilter === 'verified' ? 'active' : '' ?>">Verified</a>
    <a href="<?= admin_filter_url($statusFilter, 'unverified', $outreachFilter, $q) ?>" class="<?= $verifiedFilter === 'unverified' ? 'active' : '' ?>">Not yet verified</a>
  </div>
  <div class="toggle">
    <a href="<?= admin_filter_url($statusFilter, $verifiedFilter, 'all', $q) ?>" class="<?= $outreachFilter === 'all' ? 'active' : '' ?>">Any outreach</a>
    <a href="<?= admin_filter_url($statusFilter, $verifiedFilter, 'ready', $q) ?>" class="<?= $outreachFilter === 'ready' ? 'active' : '' ?>">Ready to email</a>
    <a href="<?= admin_filter_url($statusFilter, $verifiedFilter, 'sent', $q) ?>" class="<?= $outreachFilter === 'sent' ? 'active' : '' ?>">Emailed</a>
    <a href="<?= admin_filter_url($statusFilter, $verifiedFilter, 'none', $q) ?>" class="<?= $outreachFilter === 'none' ? 'active' : '' ?>">No email found</a>
  </div>
  <span class="muted" style="font-size:13px"><?= count($companies) ?> <?= count($companies) === 1 ? 'company' : 'companies' ?></span>
</div>
<div style="margin-top:20px">
<?php if (!$companies): ?>
  <div class="panel"><?= ($statusFilter === 'all' && $verifiedFilter === 'all' && $outreachFilter === 'all' && $q === '') ? 'No submissions yet.' : 'No companies match this filter.' ?></div>
<?php else: foreach ($companies as $c): ?>
  <div class="panel" style="margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;gap:15px;align-items:center">
      <div>
        <div style="display:flex;gap:8px;align-items:center">
          <strong><?= h($c['name']) ?></strong>
          <?php if ($c['status'] === 'approved'): ?>
            <span class="chip" style="color:#166534;border-color:#166534">Approved</span>
          <?php else: ?>
            <span class="chip" style="color:#9a3412;border-color:#9a3412">Pending</span>
          <?php endif; ?>
        </div>
        <div class="meta"><?= h($c['city']) ?>, <?= h($c['country']) ?> · <?= h($c['industry']) ?></div>
        <div class="meta"><?= h(implode(', ', array_map(fn($f) => $f['name'], $c['founders']))) ?></div>
        <div class="meta"><?= $c['contact_email'] ? h($c['contact_email']) . ' (' . h($c['email_confidence'] ?? '') . ' · ' . h($c['email_source'] ?? '') . ')' : '<span class="muted">No email found</span>' ?></div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <label class="email-switch" style="margin:0;min-height:auto">
          <input type="checkbox" class="outreach-checkbox" data-id="<?= (int)$c['id'] ?>" <?= $c['outreach_status'] === 'sent' ? 'checked' : '' ?>>
          <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
          <span class="email-switch-text">Emailed</span>
        </label>
        <span class="outreach-saved-flash muted" style="font-size:12px;min-width:44px"></span>
        <form method="post" action="api/admin-action.php" style="display:flex;align-items:center">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <input type="hidden" name="action" value="toggle-verified">
          <input type="hidden" name="redirect" value="<?= h($currentUrl) ?>">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <label class="email-switch" style="margin:0;min-height:auto">
            <input type="checkbox" onchange="this.form.submit()" <?= $c['verified'] ? 'checked' : '' ?>>
            <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
            <span class="email-switch-text">Verified</span>
          </label>
        </form>
        <a class="pill light" href="admin-edit.php?id=<?= (int)$c['id'] ?>">Edit</a>
        <?php if ($c['status'] !== 'approved'): ?>
        <form method="post" action="api/admin-action.php">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <input type="hidden" name="action" value="approve">
          <input type="hidden" name="redirect" value="<?= h($currentUrl) ?>">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <button class="pill" type="submit">Approve</button>
        </form>
        <?php endif; ?>
        <form method="post" action="api/admin-action.php" onsubmit="return confirm('Delete this company permanently?')">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="redirect" value="<?= h($currentUrl) ?>">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <button class="pill light" type="submit">Delete</button>
        </form>
      </div>
    </div>
    <p class="muted" style="margin-top:12px"><?= h($c['description']) ?></p>
  </div>
<?php endforeach; endif; ?>
</div>
</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?>
<script>
const csrfToken = <?= json_encode($csrfToken) ?>;
document.querySelectorAll('.outreach-checkbox').forEach(function (box) {
  box.addEventListener('change', function () {
    var flash = box.closest('div').querySelector('.outreach-saved-flash');
    var body = 'id=' + encodeURIComponent(box.dataset.id) + '&emailed=' + (box.checked ? '1' : '0') + '&csrf_token=' + encodeURIComponent(csrfToken);
    fetch('api/admin-outreach.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body
    }).then(function (res) {
      if (!res.ok) throw new Error('save failed');
      if (flash) {
        flash.textContent = 'Saved ✓';
        setTimeout(function () { flash.textContent = ''; }, 1500);
      }
    }).catch(function () {
      box.checked = !box.checked;
      if (flash) flash.textContent = 'Failed';
    });
  });
});
</script>
</body></html>
