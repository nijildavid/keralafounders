<?php
require __DIR__ . '/../../config/auth.php';
require_admin();
require __DIR__ . '/../../config/db.php';

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid or expired form. Please refresh the page and try again.');
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    get_db()->prepare('DELETE FROM story_signups WHERE id = ?')->execute([$id]);
}

header('Location: ../admin-story-signups.php');
