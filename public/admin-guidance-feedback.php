<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/reference.php';
require __DIR__ . '/assets/guidance-helpers.php';
$db = get_db();

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

// Small dataset (a personal-project scale of votes) — simplest and most
// portable to fetch everything and roll it up in PHP, rather than relying
// on window functions that may not exist on every MySQL/MariaDB version.
$rows = $db->query('SELECT * FROM guidance_feedback ORDER BY target_type, target_id, created_at DESC')->fetchAll();

$totals = ['up' => 0, 'down' => 0];
$byTarget = [];
foreach ($rows as $row) {
    $totals[$row['vote']]++;
    $key = $row['target_type'] . ':' . $row['target_id'];
    if (!isset($byTarget[$key])) {
        $byTarget[$key] = ['target_type' => $row['target_type'], 'target_id' => $row['target_id'], 'up' => 0, 'down' => 0, 'votes_in_order' => [], 'comments' => []];
    }
    $byTarget[$key][$row['vote']]++;
    $byTarget[$key]['votes_in_order'][] = $row['vote'];
    if (!empty($row['comment'])) {
        $byTarget[$key]['comments'][] = ['comment' => $row['comment'], 'vote' => $row['vote'], 'created_at' => $row['created_at']];
    }
}

foreach ($byTarget as $key => &$t) {
    $total = $t['up'] + $t['down'];
    $ratio = $total > 0 ? $t['down'] / $total : 0;
    $lastThree = array_slice($t['votes_in_order'], 0, $GUIDANCE_FLAG_STREAK);
    $streakFlag = count($lastThree) === $GUIDANCE_FLAG_STREAK && count(array_unique($lastThree)) === 1 && $lastThree[0] === 'down';
    $ratioFlag = $total >= $GUIDANCE_FLAG_MIN_VOTES && $ratio > $GUIDANCE_FLAG_RATIO;
    $t['total'] = $total;
    $t['ratio'] = $ratio;
    $t['flagged'] = $streakFlag || $ratioFlag;
}
unset($t);

usort($byTarget, fn($a, $b) => ($b['flagged'] <=> $a['flagged']) ?: ($b['total'] <=> $a['total']));

$allComments = [];
foreach ($rows as $row) {
    if (!empty($row['comment'])) {
        $allComments[] = $row;
    }
}
usort($allComments, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

$dueSoon = [];
$overdue = [];
$activeAdminPage = 'guidance-feedback';
$today = new DateTimeImmutable('today');
foreach (guidance_load_countries() as $c) {
    if (!in_array($c['status'], ['live', 'ready_for_review'], true) || empty($c['next_check_due'])) {
        continue;
    }
    $due = new DateTimeImmutable($c['next_check_due']);
    $daysLeft = (int)$today->diff($due)->format('%r%a');
    if ($daysLeft < 0) {
        $overdue[] = $c;
    } elseif ($daysLeft <= 30) {
        $dueSoon[] = $c;
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Guidance feedback — Admin — Kerala Founders</title><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap">
<div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>Guidance feedback.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div>
<p class="muted">Votes and comments from the "Was this useful?" widget, plus which guides are due for their 6-month re-check.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>

<?php if ($overdue || $dueSoon): ?>
<div class="panel" style="margin-top:20px">
<h2>Re-check schedule</h2>
<?php if ($overdue): ?>
<div class="error">Overdue: <?php foreach ($overdue as $i => $c): ?><?= $i ? ', ' : '' ?><strong><?= h($c['name']) ?></strong> (was due <?= h($c['next_check_due']) ?>)<?php endforeach; ?></div>
<?php endif; ?>
<?php if ($dueSoon): ?>
<div class="notice">Due within 30 days: <?php foreach ($dueSoon as $i => $c): ?><?= $i ? ', ' : '' ?><strong><?= h($c['name']) ?></strong> (<?= h($c['next_check_due']) ?>)<?php endforeach; ?></div>
<?php endif; ?>
</div>
<?php endif; ?>

<div class="panel" style="margin-top:20px">
<h2>Overall</h2>
<p class="muted" style="margin:0">👍 <?= (int)$totals['up'] ?> · 👎 <?= (int)$totals['down'] ?></p>
</div>

<div style="margin-top:20px">
<?php if (!$byTarget): ?>
  <div class="panel">No feedback yet.</div>
<?php else: foreach ($byTarget as $t): ?>
  <div class="panel" style="margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
      <div>
        <strong><?= h($t['target_type']) ?>:<?= h($t['target_id']) ?></strong>
        <?php if ($t['flagged']): ?><span class="chip" style="color:#9a3412;border-color:#9a3412;margin-left:8px">Flagged</span><?php endif; ?>
      </div>
      <span class="muted" style="font-size:13px">👍 <?= (int)$t['up'] ?> · 👎 <?= (int)$t['down'] ?> · <?= round($t['ratio'] * 100) ?>% down</span>
    </div>
    <?php if ($t['comments']): ?>
    <div style="margin-top:10px">
    <?php foreach ($t['comments'] as $c): ?>
      <div class="hint" style="margin-top:6px"><?= $c['vote'] === 'down' ? '👎' : '👍' ?> "<?= nl2br(h($c['comment'])) ?>" — <?= h(date('j M Y', strtotime($c['created_at']))) ?></div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
<?php endforeach; endif; ?>
</div>

</div></section></main><?php include __DIR__ . '/partials/footer-minimal.php'; ?></body></html>
