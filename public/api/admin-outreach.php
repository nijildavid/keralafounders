<?php
require __DIR__ . '/../../config/auth.php';
require_admin();
require __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Invalid or expired form']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$emailed = ($_POST['emailed'] ?? '') === '1';

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

$db = get_db();
if ($emailed) {
    $db->prepare("UPDATE companies SET outreach_status = 'sent', outreach_sent_at = NOW() WHERE id = ?")->execute([$id]);
} else {
    $db->prepare("UPDATE companies SET outreach_status = 'not_contacted', outreach_sent_at = NULL WHERE id = ?")->execute([$id]);
}

echo json_encode(['ok' => true]);
