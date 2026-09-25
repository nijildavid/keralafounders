<?php
declare(strict_types=1);

// Public endpoint for the Stories page's "notify me" email capture. No CSRF,
// by design — same as submit-company.php/submit-claim.php, since a visitor
// isn't logged in. Reuses guidance-feedback.php's anti-spam pattern: a
// honeypot field and an IP-hash rate limit (never the raw IP).

header('Content-Type: application/json');
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/auth.php';

const RATE_LIMIT_WINDOW_MINUTES = 10;
const RATE_LIMIT_MAX_SIGNUPS = 5;

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

$email = trim((string)($input['email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

$ipHash = hash('sha256', GUIDANCE_FEEDBACK_IP_SALT . (string)($_SERVER['REMOTE_ADDR'] ?? ''));

$db = get_db();

$rateCheck = $db->prepare(
    'SELECT COUNT(*) FROM story_signups WHERE ip_hash = ? AND created_at > NOW() - INTERVAL ' . RATE_LIMIT_WINDOW_MINUTES . ' MINUTE'
);
$rateCheck->execute([$ipHash]);
if ((int)$rateCheck->fetchColumn() >= RATE_LIMIT_MAX_SIGNUPS) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many attempts from this connection — please try again later.']);
    exit;
}

$insert = $db->prepare('INSERT IGNORE INTO story_signups (email, ip_hash) VALUES (?, ?)');
$insert->execute([$email, $ipHash]);

echo json_encode(['ok' => true]);
