<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

// Only turn a stored link into a clickable one when it is a plain http(s) URL.
function suggestion_href(string $link): ?string
{
    $candidate = preg_match('#^https?://#i', $link) ? $link : 'https://' . $link;
    return filter_var($candidate, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $candidate) ? $candidate : null;
}

$rows = $db->query('SELECT id, business_name, city, link, is_owner, handled, created_at FROM company_suggestions ORDER BY handled ASC, created_at DESC')->fetchAll();
$open = count(array_filter($rows, function ($r) { return !(int)$r['handled']; }));
$activeAdminPage = 'suggestions';
$csrfToken = csrf_token();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Suggestions — Admin — Kerala Founders</title><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap">
<div style="display:flex;justify-content:space-between;align-items:baseline"><div><div class="eyebrow">Admin</div><h1>Business suggestions.</h1></div><a class="arrow" href="admin-logout.php">Log out</a></div>
<p class="muted">Businesses people nominated through the "Know a Malayali business?" form. Check each one, then add it from the normal Add your company route or reach out to the owner.</p>
<?php include __DIR__ . '/admin-nav.php'; ?>

<div class="panel" style="margin-top:20px"><p class="muted" style="margin:0"><?= $open ?> to look at, <?= count($rows) - $open ?> handled</p></div>

<div style="margin-top:20px">
<?php if (!$rows): ?>
  <div class="panel">No suggestions yet.</div>
<?php else: ?>
  <div class="panel" style="padding:0;overflow:hidden">
  <table style="width:100%;border-collapse:collapse">
    <thead><tr style="text-align:left;border-bottom:1px solid var(--line)"><th style="padding:12px 16px">Business</th><th style="padding:12px 16px">City</th><th style="padding:12px 16px">Link</th><th style="padding:12px 16px">Owner?</th><th style="padding:12px 16px">Sent</th><th style="padding:12px 16px"></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $row): $href = suggestion_href($row['link']); ?>
      <tr style="border-bottom:1px solid var(--line)<?= (int)$row['handled'] ? ';opacity:.55' : '' ?>">
        <td style="padding:10px 16px"><?= h($row['business_name']) ?></td>
        <td style="padding:10px 16px"><?= h($row['city']) ?></td>
        <td style="padding:10px 16px"><?php if ($href): ?><a href="<?= h($href) ?>" target="_blank" rel="noopener noreferrer"><?= h($row['link']) ?></a><?php else: ?><?= h($row['link']) ?><?php endif; ?></td>
        <td style="padding:10px 16px"><?= (int)$row['is_owner'] ? 'Yes' : 'No' ?></td>
        <td style="padding:10px 16px;color:var(--muted)"><?= h(date('j M Y, g:ia', strtotime($row['created_at']))) ?></td>
        <td style="padding:10px 16px;text-align:right;white-space:nowrap">
          <form method="post" action="api/admin-suggestion-action.php" style="display:inline">
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
            <?php if (!(int)$row['handled']): ?><button class="pill light" name="action" value="handled" type="submit" style="padding:6px 12px;font-size:12px">Mark handled</button><?php endif; ?>
            <button class="pill light" name="action" value="delete" type="submit" style="padding:6px 12px;font-size:12px" onclick="return confirm('Delete this suggestion?')">Delete</button>
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
