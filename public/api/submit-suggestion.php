<?php
declare(strict_types=1);

// Public endpoint for the "Know a Malayali business?" suggestion form.
// Same anti-spam pattern as submit-story-signup.php: honeypot plus an
// IP-hash rate limit (never the raw IP). Stores no personal data about
// people; suggestions land in admin-suggestions.php for review.

header('Content-Type: application/json');
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/auth.php';
require __DIR__ . '/../assets/validation-helpers.php';

const RATE_LIMIT_WINDOW_MINUTES = 10;
const RATE_LIMIT_MAX_SUGGESTIONS = 5;

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid payload']);
    exit;
}

// Honeypot: a real visitor never fills this in.
if (trim((string)($input['website'] ?? '')) !== '') {
    echo json_encode(['ok' => true]);
    exit;
}

$name = trim((string)($input['businessName'] ?? ''));
$city = trim((string)($input['city'] ?? ''));
$link = trim((string)($input['link'] ?? ''));
$isOwner = !empty($input['isOwner']);

if ($name === '' || $city === '' || $link === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Please fill in the business name, city and a website or Instagram link.']);
    exit;
}
if (mb_strlen($name) > 200 || mb_strlen($city) > 120 || mb_strlen($link) > 300) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'One of the answers is too long.']);
    exit;
}

$ipHash = hash('sha256', GUIDANCE_FEEDBACK_IP_SALT . (string)($_SERVER['REMOTE_ADDR'] ?? ''));
$db = get_db();

if (rate_limit_exceeded($db, 'company_suggestions', $ipHash, RATE_LIMIT_WINDOW_MINUTES, RATE_LIMIT_MAX_SUGGESTIONS)) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many attempts from this connection — please try again later.']);
    exit;
}

$db->prepare('INSERT INTO company_suggestions (business_name, city, link, is_owner, ip_hash) VALUES (?, ?, ?, ?, ?)')
   ->execute([$name, $city, $link, $isOwner ? 1 : 0, $ipHash]);

echo json_encode(['ok' => true]);
