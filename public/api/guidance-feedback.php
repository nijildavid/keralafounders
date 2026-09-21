<?php
declare(strict_types=1);

// Public endpoint for the Guidance "Was this useful?" widget. No CSRF, by
// design — same as submit-company.php/submit-claim.php, since a visitor
// isn't logged in. Unlike those endpoints, this one needs its own spam
// control, since nothing like it exists elsewhere on the site to copy:
// a honeypot field, a comment length cap, and an IP-hash rate limit backed
// only by this table's own timestamps (never the raw IP).

header('Content-Type: application/json');
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/auth.php';

const RATE_LIMIT_WINDOW_MINUTES = 10;
const RATE_LIMIT_MAX_VOTES = 30;
const COMMENT_MAX_LENGTH = 1000;

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid payload']);
    exit;
}

// Honeypot: a real visitor never fills this in. Pretend success so a bot
// doesn't learn the field is being checked, but don't write a row.
if (trim((string)($input['website'] ?? '')) !== '') {
    echo json_encode(['ok' => true]);
    exit;
}

$targetType = (string)($input['targetType'] ?? '');
$targetId = trim((string)($input['targetId'] ?? ''));
$vote = (string)($input['vote'] ?? '');
$comment = trim((string)($input['comment'] ?? ''));

if (!in_array($targetType, ['guide', 'section', 'faq'], true) || $targetId === '' || !in_array($vote, ['up', 'down'], true)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid vote.']);
    exit;
}

if (mb_strlen($comment) > COMMENT_MAX_LENGTH) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Comment is too long.']);
    exit;
}

$ipHash = hash('sha256', GUIDANCE_FEEDBACK_IP_SALT . (string)($_SERVER['REMOTE_ADDR'] ?? ''));

$db = get_db();

$rateCheck = $db->prepare(
    'SELECT COUNT(*) FROM guidance_feedback WHERE ip_hash = ? AND created_at > NOW() - INTERVAL ' . RATE_LIMIT_WINDOW_MINUTES . ' MINUTE'
);
$rateCheck->execute([$ipHash]);
if ((int)$rateCheck->fetchColumn() >= RATE_LIMIT_MAX_VOTES) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many votes from this connection — please try again later.']);
    exit;
}

$insert = $db->prepare(
    'INSERT INTO guidance_feedback (target_type, target_id, vote, comment, ip_hash) VALUES (?, ?, ?, ?, ?)'
);
$insert->execute([$targetType, $targetId, $vote, $comment !== '' ? $comment : null, $ipHash]);

echo json_encode(['ok' => true]);
