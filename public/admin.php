<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/render-helpers.php';
$db = get_db();
$csrfToken = csrf_token();

const PAGE_SIZE = 30;

$statusFilter = $_GET['status'] ?? 'all';
if (!in_array($statusFilter, ['all', 'pending', 'approved'], true)) {
    $statusFilter = 'all';
}
$verifiedFilter = $_GET['verified'] ?? 'all';
if (!in_array($verifiedFilter, ['all', 'verified', 'owner', 'unverified', 'confirmed', 'gap'], true)) {
    $verifiedFilter = 'all';
}
$outreachFilter = $_GET['outreach'] ?? 'all';
if (!in_array($outreachFilter, ['all', 'found', 'ready', 'sent', 'none'], true)) {
    $outreachFilter = 'all';
}
$claimFilter = $_GET['claim'] ?? 'all';
if (!in_array($claimFilter, ['all', 'pending', 'any', 'none'], true)) {
    $claimFilter = 'all';
}
$q = trim((string)($_GET['q'] ?? ''));

// The Owner confirmed filter needs the owner_confirmed column (added by
// migration-owner-confirmed-2026-10-10.sql); fall back to "all" if it is missing.
if ($verifiedFilter === 'owner') {
    try {
        $db->query('SELECT owner_confirmed FROM companies LIMIT 0');
    } catch (PDOException $e) {
        $verifiedFilter = 'all';
    }
}

// Sortable columns. Keys are the only values ever accepted from the URL;
// the SQL fragments are fixed strings, never built from user input.
$sortColumns = [
    'needs'    => ['label' => 'Needs action first', 'sql' => [], 'default_dir' => 'desc'],
    'name'     => ['label' => 'Company', 'sql' => ['c.name'], 'default_dir' => 'asc'],
    'industry' => ['label' => 'Industry', 'sql' => ['c.industry'], 'default_dir' => 'asc'],
    'country'  => ['label' => 'Location', 'sql' => ['c.country', 'c.city'], 'default_dir' => 'asc'],
    'founders' => ['label' => 'Founders', 'sql' => ['founder_count'], 'default_dir' => 'desc'],
    'created'  => ['label' => 'Submitted', 'sql' => ['c.created_at'], 'default_dir' => 'desc'],
    'status'   => ['label' => 'Status', 'sql' => ['c.status', 'c.verified'], 'default_dir' => 'asc'],
    'contact'  => ['label' => 'Contact', 'sql' => ['contact_rank'], 'default_dir' => 'asc'],
    'claims'   => ['label' => 'Claim', 'sql' => ['pending_claims', 'total_claims'], 'default_dir' => 'desc'],
];
$sort = (string)($_GET['sort'] ?? 'needs');
if (!isset($sortColumns[$sort])) {
    $sort = 'needs';
}
$dir = (string)($_GET['dir'] ?? $sortColumns[$sort]['default_dir']);
if ($dir !== 'asc' && $dir !== 'desc') {
    $dir = $sortColumns[$sort]['default_dir'];
}

$where = [];
$params = [];
if ($statusFilter !== 'all') {
    $where[] = 'c.status = ?';
    $params[] = $statusFilter;
}
if ($verifiedFilter === 'verified') {
    $where[] = 'c.verified = 1';
} elseif ($verifiedFilter === 'owner') {
    $where[] = 'c.verified = 1 AND c.owner_confirmed = 1';
} elseif ($verifiedFilter === 'unverified') {
    $where[] = 'c.verified = 0';
} elseif ($verifiedFilter === 'confirmed' || $verifiedFilter === 'gap') {
    // Fixed SQL, mirrors company_contact_tier() in assets/render-helpers.php.
    $confirmedSql = "(COALESCE(c.contact_email, '') <> '' AND COALESCE(c.email_source_url, '') <> '') OR (COALESCE(c.instagram, '') <> '' AND c.instagram_confidence = 'high')";
    $where[] = $verifiedFilter === 'confirmed' ? "c.verified = 0 AND ($confirmedSql)" : "c.verified = 0 AND NOT ($confirmedSql)";
}
if ($outreachFilter === 'found') {
    $where[] = 'c.contact_email IS NOT NULL';
} elseif ($outreachFilter === 'ready') {
    $where[] = "c.contact_email IS NOT NULL AND c.outreach_status = 'not_contacted'";
} elseif ($outreachFilter === 'sent') {
    $where[] = "c.outreach_status = 'sent'";
} elseif ($outreachFilter === 'none') {
    $where[] = 'c.contact_email IS NULL';
}
if ($claimFilter === 'pending') {
    $where[] = "EXISTS (SELECT 1 FROM claim_requests cr WHERE cr.company_id = c.id AND cr.status = 'pending')";
} elseif ($claimFilter === 'any') {
    $where[] = 'EXISTS (SELECT 1 FROM claim_requests cr WHERE cr.company_id = c.id)';
} elseif ($claimFilter === 'none') {
    $where[] = 'NOT EXISTS (SELECT 1 FROM claim_requests cr WHERE cr.company_id = c.id)';
}
if ($q !== '') {
    $where[] = '(c.name LIKE ? OR c.slug LIKE ? OR c.city LIKE ? OR c.country LIKE ? OR EXISTS (SELECT 1 FROM founders fs WHERE fs.company_id = c.id AND fs.name LIKE ?))';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like);
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

$countStmt = $db->prepare('SELECT COUNT(*) FROM companies c' . $whereSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / PAGE_SIZE));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * PAGE_SIZE;

if ($sort === 'needs') {
    $orderSql = "(c.status = 'pending') DESC, pending_claims DESC, c.created_at DESC, c.id DESC";
} else {
    $parts = [];
    foreach ($sortColumns[$sort]['sql'] as $col) {
        $parts[] = $col . ' ' . strtoupper($dir);
    }
    $orderSql = implode(', ', $parts) . ', c.id DESC';
}

// One query for the page, with per-company counts aggregated in derived
// tables (joining the raw founders/branches rows would duplicate companies).
$sql = 'SELECT c.*,
        COALESCE(f.n, 0) AS founder_count,
        COALESCE(b.n, 0) AS branch_count,
        COALESCE(cl.pending, 0) AS pending_claims,
        COALESCE(cl.total, 0) AS total_claims,
        (CASE WHEN c.contact_email IS NULL THEN 0
              WHEN c.outreach_status = \'not_contacted\' THEN 1
              WHEN c.outreach_status = \'sent\' THEN 2 ELSE 3 END) AS contact_rank
    FROM companies c
    LEFT JOIN (SELECT company_id, COUNT(*) AS n FROM founders GROUP BY company_id) f ON f.company_id = c.id
    LEFT JOIN (SELECT company_id, COUNT(*) AS n FROM branches GROUP BY company_id) b ON b.company_id = c.id
    LEFT JOIN (SELECT company_id, COUNT(*) AS total, SUM(status = \'pending\') AS pending FROM claim_requests GROUP BY company_id) cl ON cl.company_id = c.id'
    . $whereSql . ' ORDER BY ' . $orderSql . ' LIMIT ? OFFSET ?';
$stmt = $db->prepare($sql);
$i = 1;
foreach ($params as $param) {
    $stmt->bindValue($i++, $param);
}
$stmt->bindValue($i++, PAGE_SIZE, PDO::PARAM_INT);
$stmt->bindValue($i++, $offset, PDO::PARAM_INT);
$stmt->execute();
$companies = $stmt->fetchAll();

// Founders, branches and claims for the visible page: one query each,
// grouped in PHP (replaces the old one-query-per-row lookup).
$foundersBy = [];
$branchesBy = [];
$claimsBy = [];
if ($companies) {
    $ids = array_map(function ($r) {
        return (int)$r['id'];
    }, $companies);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $fs = $db->prepare("SELECT company_id, name, email, linkedin, show_email FROM founders WHERE company_id IN ($in) ORDER BY id");
    $fs->execute($ids);
    foreach ($fs->fetchAll() as $r) {
        $foundersBy[(int)$r['company_id']][] = $r;
    }
    $bs = $db->prepare("SELECT company_id, country FROM branches WHERE company_id IN ($in) ORDER BY country");
    $bs->execute($ids);
    foreach ($bs->fetchAll() as $r) {
        $branchesBy[(int)$r['company_id']][] = $r;
    }
    $cs = $db->prepare("SELECT company_id, claimant_name, status, created_at FROM claim_requests WHERE company_id IN ($in) ORDER BY created_at DESC");
    $cs->execute($ids);
    foreach ($cs->fetchAll() as $r) {
        $claimsBy[(int)$r['company_id']][] = $r;
    }
}

// Builds an admin.php URL from the current state, overriding some keys.
function admin_url(array $state, array $override = []): string
{
    $s = array_merge($state, $override);
    $defaults = ['status' => 'all', 'verified' => 'all', 'outreach' => 'all', 'claim' => 'all', 'q' => '', 'sort' => 'needs', 'dir' => '', 'page' => 1];
    $pairs = [];
    foreach ($defaults as $key => $def) {
        $val = $s[$key] ?? $def;
        if ($key === 'page') {
            if ((int)$val > 1) {
                $pairs[] = 'page=' . (int)$val;
            }
        } elseif ($key === 'dir') {
            if (($s['sort'] ?? 'needs') !== 'needs' && ($val === 'asc' || $val === 'desc')) {
                $pairs[] = 'dir=' . $val;
            }
        } elseif ((string)$val !== '' && (string)$val !== (string)$def) {
            $pairs[] = $key . '=' . urlencode((string)$val);
        }
    }
    return 'admin.php' . ($pairs ? '?' . implode('&', $pairs) : '');
}

$state = [
    'status' => $statusFilter, 'verified' => $verifiedFilter, 'outreach' => $outreachFilter,
    'claim' => $claimFilter, 'q' => $q, 'sort' => $sort, 'dir' => $dir, 'page' => $page,
];
$currentUrl = admin_url($state);

$paginationExtraParams = [];
foreach (['status' => $statusFilter, 'verified' => $verifiedFilter, 'outreach' => $outreachFilter, 'claim' => $claimFilter] as $k => $v) {
    if ($v !== 'all') {
        $paginationExtraParams[$k] = $v;
    }
}
if ($q !== '') {
    $paginationExtraParams['q'] = $q;
}
if ($sort !== 'needs') {
    $paginationExtraParams['sort'] = $sort;
    $paginationExtraParams['dir'] = $dir;
}

// Lucide icons (https://lucide.dev, ISC licence), inlined so there is no
// external dependency. Decorative: the sort state is conveyed by aria-sort.
function admin_sort_icon(string $name): string
{
    $paths = [
        'arrow-up-down' => '<path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/>',
        'arrow-up'      => '<path d="m5 12 7-7 7 7"/><path d="M12 19V5"/>',
        'arrow-down'    => '<path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>',
    ];
    return '<svg class="adm-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
}

// Header cell: a real link (works without JS) that flips direction when the
// column is already the active sort.
function admin_sort_th(string $key, string $label, array $state, array $cols, string $class = ''): string
{
    $active = $state['sort'] === $key;
    $nextDir = $active ? ($state['dir'] === 'asc' ? 'desc' : 'asc') : $cols[$key]['default_dir'];
    $href = admin_url($state, ['sort' => $key, 'dir' => $nextDir, 'page' => 1]);
    $aria = $active ? ($state['dir'] === 'asc' ? 'ascending' : 'descending') : 'none';
    $icon = admin_sort_icon($active ? ($state['dir'] === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrow-up-down');
    return '<th scope="col" aria-sort="' . $aria . '"' . ($class ? ' class="' . $class . '"' : '') . '>'
        . '<a class="adm-sort' . ($active ? ' is-active' : '') . '" href="' . h($href) . '">' . h($label)
        . ' ' . $icon . '</a></th>';
}

function admin_status_badge(array $c): string
{
    if ($c['status'] === 'approved') {
        $html = '<span class="adm-badge adm-badge-ok"><span aria-hidden="true">&#10003;</span> Approved</span>';
    } else {
        $html = '<span class="adm-badge adm-badge-attn"><span aria-hidden="true">&#9679;</span> Pending</span>';
    }
    if ((int)$c['verified'] === 1) {
        $html .= ' <span class="adm-badge adm-badge-ok"><span aria-hidden="true">&#10003;</span> Verified</span>';
    }
    return $html;
}

function admin_contact_badge(array $c): string
{
    if ($c['contact_email'] === null || $c['contact_email'] === '') {
        return '<span class="adm-badge adm-badge-neutral">No email</span>';
    }
    if ($c['outreach_status'] === 'responded') {
        return '<span class="adm-badge adm-badge-ok"><span aria-hidden="true">&#10003;</span> Responded</span>';
    }
    if ($c['outreach_status'] === 'sent') {
        return '<span class="adm-badge adm-badge-ok"><span aria-hidden="true">&#9993;</span> Emailed</span>';
    }
    return '<span class="adm-badge adm-badge-neutral"><span aria-hidden="true">&#9993;</span> Ready to email</span>';
}

function admin_claim_badge(array $c): string
{
    if ((int)$c['pending_claims'] > 0) {
        return '<span class="adm-badge adm-badge-attn"><span aria-hidden="true">&#9873;</span> ' . (int)$c['pending_claims'] . ' pending</span>';
    }
    if ((int)$c['total_claims'] > 0) {
        return '<span class="adm-badge adm-badge-neutral">Resolved</span>';
    }
    return '<span class="adm-muted">None</span>';
}

function admin_hidden_fields(int $id, string $action, string $redirect, string $csrf): string
{
    return '<input type="hidden" name="id" value="' . $id . '">'
        . '<input type="hidden" name="action" value="' . h($action) . '">'
        . '<input type="hidden" name="redirect" value="' . h($redirect) . '">'
        . '<input type="hidden" name="csrf_token" value="' . h($csrf) . '">';
}

$filtersActive = ($statusFilter !== 'all' || $verifiedFilter !== 'all' || $outreachFilter !== 'all' || $claimFilter !== 'all' || $q !== '');
$sortLabel = $sort === 'needs' ? 'needs action first' : $sortColumns[$sort]['label'] . ($dir === 'asc' ? ', ascending' : ', descending');

$activeAdminPage = 'submissions';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Kerala Founders</title><meta name="description" content="Keralite founders and companies building across the European Union."><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script><script src="assets/admin-table.js?v=<?= (int)@filemtime(__DIR__ . "/assets/admin-table.js") ?>" defer></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap adm-wide"><div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>All submissions.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div><p class="muted">Every company ever submitted via "Add your company", with its current status. Select a company to see the full picture.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>

<form method="get" action="admin.php" class="adm-filters" role="search" aria-label="Filter submissions">
  <div class="adm-filter adm-filter-search">
    <label for="adm-q">Search</label>
    <input class="field" id="adm-q" type="search" name="q" value="<?= h($q) ?>" placeholder="Company, founder, city or country…">
  </div>
  <div class="adm-filter">
    <label for="adm-status">Status</label>
    <select class="select" id="adm-status" name="status">
      <option value="all"<?= $statusFilter === 'all' ? ' selected' : '' ?>>All</option>
      <option value="pending"<?= $statusFilter === 'pending' ? ' selected' : '' ?>>Pending</option>
      <option value="approved"<?= $statusFilter === 'approved' ? ' selected' : '' ?>>Approved</option>
    </select>
  </div>
  <div class="adm-filter">
    <label for="adm-verified">Verified</label>
    <select class="select" id="adm-verified" name="verified">
      <option value="all"<?= $verifiedFilter === 'all' ? ' selected' : '' ?>>All</option>
      <option value="verified"<?= $verifiedFilter === 'verified' ? ' selected' : '' ?>>Verified</option>
      <option value="owner"<?= $verifiedFilter === 'owner' ? ' selected' : '' ?>>Owner confirmed</option>
      <option value="unverified"<?= $verifiedFilter === 'unverified' ? ' selected' : '' ?>>Not yet verified</option>
      <option value="confirmed"<?= $verifiedFilter === 'confirmed' ? ' selected' : '' ?>>Contact confirmed (tier 2)</option>
      <option value="gap"<?= $verifiedFilter === 'gap' ? ' selected' : '' ?>>Needs a contact point (tier 3)</option>
    </select>
  </div>
  <div class="adm-filter">
    <label for="adm-outreach">Contact</label>
    <select class="select" id="adm-outreach" name="outreach">
      <option value="all"<?= $outreachFilter === 'all' ? ' selected' : '' ?>>Any</option>
      <option value="found"<?= $outreachFilter === 'found' ? ' selected' : '' ?>>Email found</option>
      <option value="ready"<?= $outreachFilter === 'ready' ? ' selected' : '' ?>>Ready to email</option>
      <option value="sent"<?= $outreachFilter === 'sent' ? ' selected' : '' ?>>Emailed</option>
      <option value="none"<?= $outreachFilter === 'none' ? ' selected' : '' ?>>No email found</option>
    </select>
  </div>
  <div class="adm-filter">
    <label for="adm-claim">Claim</label>
    <select class="select" id="adm-claim" name="claim">
      <option value="all"<?= $claimFilter === 'all' ? ' selected' : '' ?>>Any</option>
      <option value="pending"<?= $claimFilter === 'pending' ? ' selected' : '' ?>>Pending claim</option>
      <option value="any"<?= $claimFilter === 'any' ? ' selected' : '' ?>>Has any claim</option>
      <option value="none"<?= $claimFilter === 'none' ? ' selected' : '' ?>>No claim</option>
    </select>
  </div>
  <div class="adm-filter adm-sort-mobile">
    <label for="adm-sort">Sort by</label>
    <select class="select" id="adm-sort" name="sort">
      <?php foreach ($sortColumns as $key => $col): ?>
      <option value="<?= h($key) ?>"<?= $sort === $key ? ' selected' : '' ?>><?= h($col['label']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="adm-filter adm-sort-mobile">
    <label for="adm-dir">Order</label>
    <select class="select" id="adm-dir" name="dir">
      <option value="asc"<?= $dir === 'asc' ? ' selected' : '' ?>>Ascending</option>
      <option value="desc"<?= $dir === 'desc' ? ' selected' : '' ?>>Descending</option>
    </select>
  </div>
  <div class="adm-filter adm-filter-actions">
    <button class="pill" type="submit">Apply</button>
    <?php if ($filtersActive): ?><a class="adm-clear" href="admin.php">Clear filters</a><?php endif; ?>
  </div>
</form>

<p class="adm-count" role="status" aria-live="polite"><?= $total ?> <?= $total === 1 ? 'company' : 'companies' ?><?= $filtersActive ? ' match' : '' ?></p>

<?php if (!$companies): ?>
  <div class="panel"><?= $filtersActive ? 'No companies match these filters.' : 'No submissions yet.' ?></div>
<?php else: ?>
<div class="adm-table-wrap">
<table class="adm-table" id="adm-table" data-csrf="<?= h($csrfToken) ?>">
  <caption class="sr-only">All submissions, sorted by <?= h($sortLabel) ?>. Select a company name to open its details.</caption>
  <thead><tr>
    <?= admin_sort_th('name', 'Company', $state, $sortColumns) ?>
    <?= admin_sort_th('country', 'Location', $state, $sortColumns, 'adm-col-opt') ?>
    <?= admin_sort_th('industry', 'Industry', $state, $sortColumns, 'adm-col-opt') ?>
    <?= admin_sort_th('founders', 'Founders', $state, $sortColumns) ?>
    <?= admin_sort_th('created', 'Submitted', $state, $sortColumns, 'adm-col-opt') ?>
    <?= admin_sort_th('status', 'Status', $state, $sortColumns) ?>
    <?= admin_sort_th('contact', 'Contact', $state, $sortColumns) ?>
    <?= admin_sort_th('claims', 'Claim', $state, $sortColumns) ?>
  </tr></thead>
  <tbody>
<?php foreach ($companies as $c):
    $id = (int)$c['id'];
    $founders = $foundersBy[$id] ?? [];
    $branches = $branchesBy[$id] ?? [];
    $claims = $claimsBy[$id] ?? [];
    $hasEmail = $c['contact_email'] !== null && $c['contact_email'] !== '';
    $firstFounder = $founders ? $founders[0]['name'] : '';
    $extraFounders = max(0, count($founders) - 1);
?>
  <tr data-id="<?= $id ?>" data-has-email="<?= $hasEmail ? '1' : '0' ?>">
    <th scope="row" data-label="Company"><a class="adm-name" href="admin-edit.php?id=<?= $id ?>" data-open="<?= $id ?>" aria-haspopup="dialog"><?= h($c['name']) ?></a></th>
    <td data-label="Location" class="adm-col-opt"><?= h($c['city']) ?>, <?= h($c['country']) ?></td>
    <td data-label="Industry" class="adm-col-opt"><?= h($c['industry']) ?></td>
    <td data-label="Founders"><?= $firstFounder !== '' ? h($firstFounder) . ($extraFounders ? ' <span class="adm-muted">+' . $extraFounders . '</span>' : '') : '<span class="adm-muted">None</span>' ?></td>
    <td data-label="Submitted" class="adm-col-opt"><?= h(date('j M Y', strtotime((string)$c['created_at']))) ?></td>
    <td data-label="Status" class="adm-cell-status"><?= admin_status_badge($c) ?></td>
    <td data-label="Contact" class="adm-cell-contact"><?= admin_contact_badge($c) ?></td>
    <td data-label="Claim"><?= admin_claim_badge($c) ?></td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
</div>

<?php foreach ($companies as $c):
    $id = (int)$c['id'];
    $founders = $foundersBy[$id] ?? [];
    $branches = $branchesBy[$id] ?? [];
    $claims = $claimsBy[$id] ?? [];
    $hasEmail = $c['contact_email'] !== null && $c['contact_email'] !== '';
    $openUrl = $currentUrl . (strpos($currentUrl, '?') === false ? '?' : '&') . 'open=' . $id;
?>
<template id="adm-detail-<?= $id ?>">
  <div class="adm-drawer-head">
    <h2 id="adm-drawer-title"><?= h($c['name']) ?></h2>
    <div class="adm-badges"><?= admin_status_badge($c) ?> <?= admin_contact_badge($c) ?> <?= admin_claim_badge($c) ?></div>
    <div class="meta"><?= h($c['city']) ?>, <?= h($c['country']) ?> · <?= h($c['industry']) ?></div>
  </div>

  <div class="adm-actions">
    <a class="pill light" href="admin-edit.php?id=<?= $id ?>">Edit</a>
    <?php if ($c['status'] === 'approved'): ?>
    <a class="pill light" href="company.php?id=<?= h(rawurlencode((string)$c['slug'])) ?>" target="_blank" rel="noopener">View live listing <span aria-hidden="true">&#8599;</span><span class="sr-only">(opens in a new tab)</span></a>
    <?php else: ?>
    <span class="adm-muted">Not public until approved</span>
    <?php endif; ?>
    <?php if ($c['status'] !== 'approved'): ?>
    <form method="post" action="api/admin-action.php">
      <?= admin_hidden_fields($id, 'approve', $openUrl, $csrfToken) ?>
      <button class="pill" type="submit">Approve</button>
    </form>
    <?php endif; ?>
    <form method="post" action="api/admin-action.php" class="adm-switch-form">
      <?= admin_hidden_fields($id, 'toggle-verified', $openUrl, $csrfToken) ?>
      <label class="email-switch">
        <input type="checkbox" onchange="this.form.submit()" <?= $c['verified'] ? 'checked' : '' ?>>
        <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
        <span class="email-switch-text">Verified</span>
      </label>
    </form>
    <label class="email-switch">
      <input type="checkbox" class="outreach-checkbox" data-id="<?= $id ?>" <?= $c['outreach_status'] === 'sent' ? 'checked' : '' ?>>
      <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
      <span class="email-switch-text">Emailed</span>
    </label>
  </div>

  <section class="adm-section" aria-labelledby="adm-s-about-<?= $id ?>">
    <h3 id="adm-s-about-<?= $id ?>">About</h3>
    <p><?= h($c['description']) ?></p>
    <dl class="adm-dl">
      <dt>Industry</dt><dd><?= h($c['industry']) ?><?= $c['business_type'] !== '' ? ' · ' . h($c['business_type']) : '' ?><?= $c['industry_detail'] ? ' · ' . h($c['industry_detail']) : '' ?></dd>
      <?php if ($c['website']): ?><dt>Website</dt><dd><?= h($c['website']) ?></dd><?php endif; ?>
      <?php if ($c['founded_year']): ?><dt>Founded</dt><dd><?= (int)$c['founded_year'] ?></dd><?php endif; ?>
      <?php if ($c['size']): ?><dt>Size</dt><dd><?= h($c['size']) ?></dd><?php endif; ?>
      <?php if ($c['kerala_connection']): ?><dt>Kerala link</dt><dd><?= h($c['kerala_connection']) ?><?= $c['kerala_district'] ? ' (' . h($c['kerala_district']) . ')' : '' ?></dd><?php endif; ?>
      <?php if ($c['instagram']): ?><dt>Instagram</dt><dd>@<?= h($c['instagram']) ?><?php if (($c['instagram_source'] ?? null) === 'research' && ($c['instagram_confidence'] ?? null) === 'medium'): ?> <span class="adm-badge adm-badge-attn">Needs review</span><?php endif; ?></dd><?php endif; ?>
      <dt>Stories / podcast</dt><dd><?= $c['contact_ok_podcast_stories'] ? 'OK to contact' : 'Not opted in' ?></dd>
      <dt>Submitted</dt><dd><?= h(date('j M Y, H:i', strtotime((string)$c['created_at']))) ?></dd>
    </dl>
  </section>

  <section class="adm-section" aria-labelledby="adm-s-founders-<?= $id ?>">
    <h3 id="adm-s-founders-<?= $id ?>">Founders (<?= count($founders) ?>)</h3>
    <?php if (!$founders): ?><p class="adm-muted">No founders listed.</p><?php else: ?>
    <ul class="adm-list">
      <?php foreach ($founders as $f): ?>
      <li><strong><?= h($f['name']) ?></strong>
        <?php if ($f['email']): ?><div class="meta"><?= h($f['email']) ?><?= $f['show_email'] ? ' · shown publicly' : ' · private' ?></div><?php endif; ?>
        <?php if ($f['linkedin']): ?><div class="meta"><?= h($f['linkedin']) ?></div><?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>

  <section class="adm-section" aria-labelledby="adm-s-branches-<?= $id ?>">
    <h3 id="adm-s-branches-<?= $id ?>">Branches (<?= count($branches) ?>)</h3>
    <?php if (!$branches): ?><p class="adm-muted">No other branches.</p><?php else: ?>
    <div class="chips" style="margin-top:0"><?php foreach ($branches as $b): ?><span class="chip"><?= h($b['country']) ?></span><?php endforeach; ?></div>
    <?php endif; ?>
  </section>

  <section class="adm-section" aria-labelledby="adm-s-contact-<?= $id ?>">
    <h3 id="adm-s-contact-<?= $id ?>">Contact &amp; outreach</h3>
    <?php if (!$hasEmail): ?><p class="adm-muted">No email found yet.</p><?php else: ?>
    <dl class="adm-dl">
      <dt>Email</dt><dd><?= h($c['contact_email']) ?></dd>
      <?php if ($c['email_type']): ?><dt>Type</dt><dd><?= h($c['email_type']) ?></dd><?php endif; ?>
      <?php if ($c['email_source']): ?><dt>Source</dt><dd><?= h($c['email_source']) ?></dd><?php endif; ?>
      <?php if ($c['email_confidence']): ?><dt>Confidence</dt><dd><?= h($c['email_confidence']) ?></dd><?php endif; ?>
      <?php if ($c['email_source_url']): ?><dt>Source URL</dt><dd><?= h($c['email_source_url']) ?></dd><?php endif; ?>
      <dt>Outreach</dt><dd><?= h(str_replace('_', ' ', $c['outreach_status'])) ?></dd>
      <?php if ($c['outreach_sent_at']): ?><dt>Emailed on</dt><dd><?= h(date('j M Y', strtotime((string)$c['outreach_sent_at']))) ?></dd><?php endif; ?>
      <?php if ($c['outreach_responded_at']): ?><dt>Responded on</dt><dd><?= h(date('j M Y', strtotime((string)$c['outreach_responded_at']))) ?></dd><?php endif; ?>
    </dl>
    <?php endif; ?>
  </section>

  <section class="adm-section" aria-labelledby="adm-s-claims-<?= $id ?>">
    <h3 id="adm-s-claims-<?= $id ?>">Claims (<?= count($claims) ?>)</h3>
    <?php if (!$claims): ?><p class="adm-muted">Nobody has claimed this listing.</p><?php else: ?>
    <ul class="adm-list">
      <?php foreach ($claims as $cl): ?>
      <li><strong><?= h($cl['claimant_name']) ?></strong> · <?= h($cl['status']) ?>
        <div class="meta"><?= h(date('j M Y', strtotime((string)$cl['created_at']))) ?></div></li>
      <?php endforeach; ?>
    </ul>
    <a class="arrow" href="admin-claims.php">Review claims &rarr;</a>
    <?php endif; ?>
  </section>

  <section class="adm-section adm-danger" aria-labelledby="adm-s-delete-<?= $id ?>">
    <h3 id="adm-s-delete-<?= $id ?>">Delete this company</h3>
    <p>This permanently removes the listing, its founders, branches and claims. It cannot be undone. Type the company name to confirm.</p>
    <form method="post" action="api/admin-action.php" class="adm-delete-form" data-name="<?= h($c['name']) ?>">
      <?= admin_hidden_fields($id, 'delete', $currentUrl, $csrfToken) ?>
      <label for="adm-confirm-<?= $id ?>" class="sr-only">Type <?= h($c['name']) ?> to confirm deletion</label>
      <input class="field" id="adm-confirm-<?= $id ?>" name="confirm_name" type="text" autocomplete="off" placeholder="<?= h($c['name']) ?>">
      <button class="pill adm-delete-btn" type="submit" disabled>Delete permanently</button>
    </form>
  </section>
</template>
<?php endforeach; ?>

<?= ssr_pagination_html('admin.php', $paginationExtraParams, $page, $totalPages) ?>
<?php endif; ?>

<dialog id="adm-drawer" class="adm-drawer" aria-labelledby="adm-drawer-title">
  <div class="adm-drawer-bar">
    <button type="button" class="adm-drawer-close" data-close>Close <span aria-hidden="true">&times;</span></button>
  </div>
  <div class="adm-drawer-body" id="adm-drawer-body"></div>
</dialog>
<p class="sr-only" role="status" aria-live="polite" id="adm-live"></p>
</div></section></main><?php include __DIR__ . '/partials/footer-full.php'; ?>
</body></html>
