<?php
declare(strict_types=1);

header('Content-Type: application/json');
require __DIR__ . '/../../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$slug = trim((string)($input['slug'] ?? ''));
$name = trim((string)($input['name'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$message = trim((string)($input['message'] ?? ''));

if ($slug === '' || $name === '' || $email === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Please fill in your name and email.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please enter a valid email address.']);
    exit;
}

$proposedInput = is_array($input['proposedChanges'] ?? null) ? $input['proposedChanges'] : [];
$companyName = trim((string)($proposedInput['company'] ?? ''));
$industry = trim((string)($proposedInput['industry'] ?? ''));
$country = trim((string)($proposedInput['country'] ?? ''));
$city = trim((string)($proposedInput['city'] ?? ''));
$location = trim((string)($proposedInput['location'] ?? ''));
$description = trim((string)($proposedInput['description'] ?? ''));
$founders = is_array($proposedInput['founders'] ?? null) ? $proposedInput['founders'] : [];
$founders = array_values(array_filter($founders, fn($f) => trim((string)($f['name'] ?? '')) !== ''));

if ($companyName === '' || $industry === '' || $country === '' || $city === '' || $location === '' || $description === '' || !$founders) {
    http_response_code(422);
    echo json_encode(['error' => 'Please fill in all required company fields.']);
    exit;
}

$db = get_db();

$stmt = $db->prepare("SELECT id, name FROM companies WHERE slug = ? AND status = 'approved'");
$stmt->execute([$slug]);
$company = $stmt->fetch();

if (!$company) {
    http_response_code(404);
    echo json_encode(['error' => 'Company not found.']);
    exit;
}

$proposedChanges = [
    'company' => $companyName,
    'website' => trim((string)($proposedInput['website'] ?? '')),
    'industry' => $industry,
    'size' => trim((string)($proposedInput['size'] ?? '')),
    'founded' => !empty($proposedInput['founded']) ? (int)$proposedInput['founded'] : null,
    'country' => $country,
    'city' => $city,
    'location' => $location,
    'description' => $description,
    'founders' => array_map(fn($f) => [
        'name' => trim((string)$f['name']),
        'email' => trim((string)($f['email'] ?? '')),
        'linkedin' => trim((string)($f['linkedin'] ?? '')),
        'showEmail' => !empty($f['showEmail']),
    ], $founders),
    'branches' => array_values(array_filter(array_map('trim', is_array($proposedInput['branches'] ?? null) ? $proposedInput['branches'] : []))),
];

$insert = $db->prepare(
    'INSERT INTO claim_requests (company_id, claimant_name, claimant_email, message, proposed_changes) VALUES (?, ?, ?, ?, ?)'
);
$insert->execute([$company['id'], $name, $email, $message, json_encode($proposedChanges, JSON_UNESCAPED_UNICODE)]);

notify_new_claim($company['name'], $name, $email, $message);

echo json_encode(['ok' => true]);

function notify_new_claim(string $companyName, string $claimantName, string $claimantEmail, string $message): void
{
    $to = 'hello@keralafounders.eu';
    $subject = "New listing claim: $companyName";

    $body = "A new claim/correction request was submitted on Kerala Founders.\n\n"
        . "Company: $companyName\n"
        . "From: $claimantName <$claimantEmail>\n\n"
        . ($message !== '' ? "Message:\n$message\n\n" : '')
        . "Review the proposed changes here: https://keralafounders.eu/admin-claims.php\n";

    $headers = "From: Kerala Founders <hello@keralafounders.eu>\r\n"
        . "Content-Type: text/plain; charset=UTF-8";

    @mail($to, $subject, $body, $headers);
}
