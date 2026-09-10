<?php
require __DIR__ . '/../../config/auth.php';
require_admin();
require __DIR__ . '/../../config/db.php';

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid or expired form. Please refresh the page and try again.');
}

$id = (int)($_POST['id'] ?? 0);
$action = (string)($_POST['action'] ?? '');

if ($id > 0 && $action === 'approve') {
    get_db()->prepare("UPDATE companies SET status = 'approved' WHERE id = ?")->execute([$id]);
} elseif ($id > 0 && $action === 'delete') {
    get_db()->prepare('DELETE FROM companies WHERE id = ?')->execute([$id]);
} elseif ($id > 0 && $action === 'toggle-verified') {
    get_db()->prepare('UPDATE companies SET verified = NOT verified WHERE id = ?')->execute([$id]);
}

$redirect = (string)($_POST['redirect'] ?? '');
if (strpos($redirect, 'admin.php') !== 0) {
    $redirect = 'admin.php';
}

header('Location: ../' . $redirect);
