<?php
require __DIR__ . '/../../config/auth.php';
require_admin();
require __DIR__ . '/../../config/db.php';

$id = (int)($_POST['id'] ?? 0);
$action = (string)($_POST['action'] ?? '');

if ($id > 0 && $action === 'approve') {
    get_db()->prepare("UPDATE companies SET status = 'approved' WHERE id = ?")->execute([$id]);
} elseif ($id > 0 && $action === 'delete') {
    get_db()->prepare('DELETE FROM companies WHERE id = ?')->execute([$id]);
}

header('Location: ../admin.php');
