<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

if (($_GET['export'] ?? '') === 'csv') {
    $rows = $db->query('SELECT email, created_at FROM story_signups ORDER BY created_at DESC')->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="story-signups.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['email', 'signed_up_at']);
    foreach ($rows as $row) {
        fputcsv($out, [$row['email'], $row['created_at']]);
    }
    fclose($out);
    exit;
}

$rows = $db->query('SELECT id, email, created_at FROM story_signups ORDER BY created_at DESC')->fetchAll();
$activeAdminPage = 'story-signups';
$csrfToken = csrf_token();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Story signups — Admin — Kerala Founders</title><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap">
<div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>Story signups.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div>
<p class="muted">Emails collected from the "Notify me" box on the Stories page.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>

<div class="panel" style="margin-top:20px">
<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
  <p class="muted" style="margin:0"><?= count($rows) ?> signup<?= count($rows) === 1 ? '' : 's' ?></p>
  <a class="pill light" href="admin-story-signups.php?export=csv">Download CSV</a>
</div>
</div>

<div style="margin-top:20px">
<?php if (!$rows): ?>
  <div class="panel">No signups yet.</div>
<?php else: ?>
  <div class="panel" style="padding:0;overflow:hidden">
  <table style="width:100%;border-collapse:collapse">
    <thead><tr style="text-align:left;border-bottom:1px solid var(--line)"><th style="padding:12px 16px">Email</th><th style="padding:12px 16px">Signed up</th><th style="padding:12px 16px"></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
      <tr style="border-bottom:1px solid var(--line)">
        <td style="padding:10px 16px"><?= h($row['email']) ?></td>
        <td style="padding:10px 16px;color:var(--muted)"><?= h(date('j M Y, g:ia', strtotime($row['created_at']))) ?></td>
        <td style="padding:10px 16px;text-align:right">
          <form method="post" action="api/admin-story-signup-delete.php" onsubmit="return confirm('Delete this signup?')">
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
            <button class="pill light" type="submit" style="padding:6px 12px;font-size:12px">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
<?php endif; ?>
</div>

</div></section></main><?php include __DIR__ . '/partials/footer-minimal.php'; ?></body></html>
