<?php
// Shared admin sub-nav. Include after $db is available and $activeAdminPage is set
// to one of: 'dashboard', 'submissions', 'claims', 'guidance-feedback'.
$navPendingClaims = (int)$db->query("SELECT COUNT(*) FROM claim_requests WHERE status = 'pending'")->fetchColumn();
?>
<div class="toggle" style="margin-top:20px">
  <a href="admin-dashboard.php" class="<?= $activeAdminPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
  <a href="admin.php" class="<?= $activeAdminPage === 'submissions' ? 'active' : '' ?>">Submissions</a>
  <a href="admin-claims.php" class="<?= $activeAdminPage === 'claims' ? 'active' : '' ?>">Claims<?= $navPendingClaims ? ' (' . $navPendingClaims . ')' : '' ?></a>
  <a href="admin-guidance-feedback.php" class="<?= $activeAdminPage === 'guidance-feedback' ? 'active' : '' ?>">Guidance feedback</a>
</div>
