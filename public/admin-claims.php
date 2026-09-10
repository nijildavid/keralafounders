<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();
$csrfToken = csrf_token();

$statusFilter = $_GET['status'] ?? 'pending';
if (!in_array($statusFilter, ['all', 'pending', 'resolved', 'dismissed'], true)) {
    $statusFilter = 'pending';
}

$q = trim((string)($_GET['q'] ?? ''));

$sql = "SELECT cr.*, c.name AS company_name, c.slug AS company_slug,
        c.website AS cur_website, c.industry AS cur_industry, c.size AS cur_size,
        c.founded_year AS cur_founded_year, c.country AS cur_country, c.city AS cur_city,
        c.location AS cur_location, c.description AS cur_description
        FROM claim_requests cr
        JOIN companies c ON c.id = cr.company_id";
$where = [];
$params = [];
if ($statusFilter !== 'all') {
    $where[] = 'cr.status = ?';
    $params[] = $statusFilter;
}
if ($q !== '') {
    $where[] = '(c.name LIKE ? OR cr.claimant_name LIKE ? OR cr.claimant_email LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like);
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY (cr.status = "pending") DESC, cr.created_at DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$claims = $stmt->fetchAll();

$founderNameStmt = $db->prepare('SELECT name FROM founders WHERE company_id = ? ORDER BY id');
$branchStmt = $db->prepare('SELECT country FROM branches WHERE company_id = ? ORDER BY id');
foreach ($claims as &$cl) {
    $founderNameStmt->execute([$cl['company_id']]);
    $cl['cur_founder_names'] = $founderNameStmt->fetchAll(PDO::FETCH_COLUMN);
    $branchStmt->execute([$cl['company_id']]);
    $cl['cur_branches'] = $branchStmt->fetchAll(PDO::FETCH_COLUMN);
}
unset($cl);

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

function claim_diff_rows(array $cl): array
{
    $proposed = $cl['proposed_changes'] ? json_decode($cl['proposed_changes'], true) : null;
    if (!is_array($proposed)) {
        return [];
    }

    $fieldLabels = [
        'company' => 'Company name',
        'website' => 'Website',
        'industry' => 'Industry',
        'size' => 'Company size',
        'founded' => 'Founded year',
        'country' => 'Country',
        'city' => 'City',
        'location' => 'Location',
        'description' => 'Description',
    ];
    $currentMap = [
        'company' => $cl['company_name'],
        'website' => $cl['cur_website'],
        'industry' => $cl['cur_industry'],
        'size' => $cl['cur_size'],
        'founded' => $cl['cur_founded_year'],
        'country' => $cl['cur_country'],
        'city' => $cl['cur_city'],
        'location' => $cl['cur_location'],
        'description' => $cl['cur_description'],
    ];

    $rows = [];
    foreach ($fieldLabels as $key => $label) {
        $newVal = trim((string)($proposed[$key] ?? ''));
        $curVal = trim((string)($currentMap[$key] ?? ''));
        if ($newVal !== '' && $newVal !== $curVal) {
            $rows[] = [$label, $curVal !== '' ? $curVal : '—', $newVal];
        }
    }

    $newFounders = array_values(array_filter(array_map(fn($f) => trim((string)($f['name'] ?? '')), $proposed['founders'] ?? [])));
    $curFounders = $cl['cur_founder_names'];
    $newSorted = $newFounders;
    $curSorted = $curFounders;
    sort($newSorted);
    sort($curSorted);
    if ($newSorted !== $curSorted) {
        $rows[] = ['Founders', $curFounders ? implode(', ', $curFounders) : '—', $newFounders ? implode(', ', $newFounders) : '—'];
    }

    $newBranches = array_values(array_filter(array_map('trim', $proposed['branches'] ?? [])));
    $curBranches = $cl['cur_branches'];
    $newBSorted = $newBranches;
    $curBSorted = $curBranches;
    sort($newBSorted);
    sort($curBSorted);
    if ($newBSorted !== $curBSorted) {
        $rows[] = ['Branches', $curBranches ? implode(', ', $curBranches) : 'None', $newBranches ? implode(', ', $newBranches) : 'None'];
    }

    return $rows;
}

function admin_claims_filter_url(string $status, string $q = ''): string
{
    $url = 'admin-claims.php?status=' . urlencode($status);
    if ($q !== '') {
        $url .= '&q=' . urlencode($q);
    }
    return $url;
}

$activeAdminPage = 'claims';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Claims — Admin — Kerala Founders</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.png" alt="Kerala Founders">Kerala Founders</a>
<nav class="navlinks"><a href="founders.php">Directory</a><a href="countries.php">Explore places</a><a href="about.html">About</a></nav>
<div class="navright"><a class="pill" href="add-company.html">Add your company</a></div>
</div></header><main id="main">
<section class="page-head"><div class="wrap"><div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>Listing claims.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div><p class="muted">Claim and correction requests submitted via "Claim this listing" on company pages.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>
<form method="get" action="admin-claims.php" style="margin-top:20px;max-width:360px">
  <input type="hidden" name="status" value="<?= h($statusFilter) ?>">
  <input class="field" type="search" name="q" value="<?= h($q) ?>" placeholder="Search by company or claimant…" style="margin:0">
</form>
<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-top:16px">
  <div class="toggle">
    <a href="<?= admin_claims_filter_url('pending', $q) ?>" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="<?= admin_claims_filter_url('resolved', $q) ?>" class="<?= $statusFilter === 'resolved' ? 'active' : '' ?>">Resolved</a>
    <a href="<?= admin_claims_filter_url('dismissed', $q) ?>" class="<?= $statusFilter === 'dismissed' ? 'active' : '' ?>">Dismissed</a>
    <a href="<?= admin_claims_filter_url('all', $q) ?>" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">All</a>
  </div>
  <span class="muted" style="font-size:13px"><?= count($claims) ?> <?= count($claims) === 1 ? 'claim' : 'claims' ?></span>
</div>
<div style="margin-top:20px">
<?php if (!$claims): ?>
  <div class="panel">No claims <?= $statusFilter === 'all' ? 'yet' : 'match this filter' ?>.</div>
<?php else: foreach ($claims as $cl): ?>
  <div class="panel" style="margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;gap:15px;align-items:flex-start">
      <div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <strong><?= h($cl['company_name']) ?></strong>
          <?php if ($cl['status'] === 'pending'): ?>
            <span class="chip" style="color:#9a3412;border-color:#9a3412">Pending</span>
          <?php elseif ($cl['status'] === 'resolved'): ?>
            <span class="chip" style="color:#166534;border-color:#166534">Resolved</span>
          <?php else: ?>
            <span class="chip">Dismissed</span>
          <?php endif; ?>
        </div>
        <div class="meta">From <?= h($cl['claimant_name']) ?> &lt;<?= h($cl['claimant_email']) ?>&gt; · <?= h(date('j M Y', strtotime($cl['created_at']))) ?></div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <a class="pill light" href="admin-edit.php?id=<?= (int)$cl['company_id'] ?>">Edit company</a>
        <?php $diffs = claim_diff_rows($cl); ?>
        <?php if ($cl['status'] !== 'resolved' && $diffs): ?>
        <form method="post" action="api/claim-action.php" onsubmit="return confirm('Apply these changes to the live listing and mark it verified?')">
          <input type="hidden" name="id" value="<?= (int)$cl['id'] ?>">
          <input type="hidden" name="action" value="apply">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <button class="pill" type="submit">Apply &amp; mark verified</button>
        </form>
        <?php endif; ?>
        <?php if ($cl['status'] !== 'resolved'): ?>
        <form method="post" action="api/claim-action.php">
          <input type="hidden" name="id" value="<?= (int)$cl['id'] ?>">
          <input type="hidden" name="action" value="resolve">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <button class="pill light" type="submit">Mark resolved</button>
        </form>
        <?php endif; ?>
        <?php if ($cl['status'] !== 'dismissed'): ?>
        <form method="post" action="api/claim-action.php">
          <input type="hidden" name="id" value="<?= (int)$cl['id'] ?>">
          <input type="hidden" name="action" value="dismiss">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <button class="pill light" type="submit">Dismiss</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($cl['message'] !== ''): ?>
    <p class="muted" style="margin-top:12px"><?= nl2br(h($cl['message'])) ?></p>
    <?php endif; ?>
    <?php if ($diffs): ?>
    <div class="panel" style="margin-top:12px;background:var(--soft)">
      <div class="eyebrow" style="margin-bottom:8px">Proposed changes</div>
      <?php foreach ($diffs as [$label, $old, $new]): ?>
        <div style="font-size:14px;margin-bottom:6px"><strong><?= h($label) ?>:</strong> <span class="muted"><?= h($old) ?></span> → <?= h($new) ?></div>
      <?php endforeach; ?>
    </div>
    <?php elseif ($cl['proposed_changes']): ?>
    <p class="muted" style="margin-top:12px;font-size:13px">No changes suggested — just confirming the listing is accurate.</p>
    <?php endif; ?>
  </div>
<?php endforeach; endif; ?>
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
