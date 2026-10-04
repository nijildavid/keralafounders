<?php
declare(strict_types=1);

// Public endpoint for the Contact page. Email-only (no database table), same
// as the notification step in submit-claim.php. No CSRF, by design — the
// visitor isn't logged in. Anti-spam: a honeypot field plus length caps.

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid payload']);
    exit;
}

// Honeypot: a real visitor never fills this in. Pretend success so a bot
// doesn't learn the field is being checked, but don't send anything.
if (trim((string)($input['website'] ?? '')) !== '') {
    echo json_encode(['ok' => true]);
    exit;
}

$name = trim((string)($input['name'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$topic = trim((string)($input['topic'] ?? ''));
$message = trim((string)($input['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Please fill in your name, email and message.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

if (mb_strlen($name) > 120 || mb_strlen($topic) > 60 || mb_strlen($message) > 5000) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Your message is too long — please shorten it.']);
    exit;
}

$allowedTopics = ['Question', 'Suggest a country', 'Feedback', 'Remove or correct a listing', 'Something else'];
if (!in_array($topic, $allowedTopics, true)) {
    $topic = 'Something else';
}

// Strip line breaks from anything that could reach a mail header.
$safeName = preg_replace('/[\r\n]+/', ' ', $name);

$to = 'hello@keralafounders.eu';
$subject = "Contact form: $topic";
$body = "New message from the Kerala Founders contact form.\n\n"
    . "From: $safeName <$email>\n"
    . "Topic: $topic\n\n"
    . "Message:\n$message\n";
$headers = "From: Kerala Founders <hello@keralafounders.eu>\r\n"
    . "Reply-To: $email\r\n"
    . "Content-Type: text/plain; charset=UTF-8";

if (!@mail($to, $subject, $body, $headers)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'We couldn\'t send your message just now. Please email hello@keralafounders.eu directly.']);
    exit;
}

echo json_encode(['ok' => true]);
