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

if ($id > 0) {
    $db = get_db();
    if ($action === 'handled') {
        $db->prepare('UPDATE company_suggestions SET handled = 1 WHERE id = ?')->execute([$id]);
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM company_suggestions WHERE id = ?')->execute([$id]);
    }
}

header('Location: ../admin-suggestions.php');
