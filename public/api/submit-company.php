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

$name = trim((string)($input['company'] ?? ''));
$industry = trim((string)($input['industry'] ?? ''));
$businessType = trim((string)($input['businessType'] ?? ''));
$industryDetail = trim((string)($input['industryDetail'] ?? ''));
$country = trim((string)($input['country'] ?? ''));
$city = trim((string)($input['city'] ?? ''));
$location = trim((string)($input['location'] ?? ''));
$description = trim((string)($input['description'] ?? ''));
$founders = is_array($input['founders'] ?? null) ? $input['founders'] : [];
$founders = array_values(array_filter($founders, fn($f) => trim((string)($f['name'] ?? '')) !== ''));

if ($name === '' || $industry === '' || $businessType === '' || $country === '' || $city === '' || $location === '' || $description === '' || !$founders) {
    http_response_code(422);
    echo json_encode(['error' => 'Please fill in all required fields.']);
    exit;
}

$db = get_db();

$slugBase = strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
$slugBase = $slugBase !== '' ? $slugBase : 'company';
$slug = $slugBase;
$check = $db->prepare('SELECT COUNT(*) FROM companies WHERE slug = ?');
for ($suffix = 2; ; $suffix++) {
    $check->execute([$slug]);
    if ((int)$check->fetchColumn() === 0) {
        break;
    }
    $slug = $slugBase . '-' . $suffix;
}

$db->beginTransaction();

$stmt = $db->prepare(
    'INSERT INTO companies (slug, name, website, industry, business_type, industry_detail, size, founded_year, country, city, location, description, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending")'
);
$stmt->execute([
    $slug,
    $name,
    trim((string)($input['website'] ?? '')) ?: null,
    $industry,
    $businessType,
    $industryDetail ?: null,
    trim((string)($input['size'] ?? '')) ?: null,
    !empty($input['founded']) ? (int)$input['founded'] : null,
    $country,
    $city,
    $location,
    $description,
]);
$companyId = (int)$db->lastInsertId();

$founderStmt = $db->prepare(
    'INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES (?, ?, ?, ?, ?)'
);
foreach ($founders as $f) {
    $founderStmt->execute([
        $companyId,
        trim((string)$f['name']),
        trim((string)($f['email'] ?? '')) ?: null,
        trim((string)($f['linkedin'] ?? '')) ?: null,
        !empty($f['showEmail']) ? 1 : 0,
    ]);
}

$branchStmt = $db->prepare('INSERT INTO branches (company_id, country) VALUES (?, ?)');
foreach (($input['branches'] ?? []) as $branch) {
    $branch = trim((string)$branch);
    if ($branch !== '') {
        $branchStmt->execute([$companyId, $branch]);
    }
}

$db->commit();

notify_new_submission($name, $industry, $country, $city, $location, $description, $founders);

echo json_encode(['ok' => true, 'slug' => $slug]);

function notify_new_submission(
    string $name,
    string $industry,
    string $country,
    string $city,
    string $location,
    string $description,
    array $founders
): void {
    $to = 'hello@keralafounders.eu';
    $subject = "New company submission: $name";

    $founderLines = implode("\n", array_map(
        fn($f) => '- ' . trim((string)$f['name']) . (!empty($f['email']) ? ' <' . $f['email'] . '>' : ''),
        $founders
    ));

    $body = "A new company was submitted for review on Kerala Founders.\n\n"
        . "Company: $name\n"
        . "Industry: $industry\n"
        . "Location: $location ($city, $country)\n\n"
        . "Founders:\n$founderLines\n\n"
        . "Description:\n$description\n\n"
        . "Review it here: https://keralafounders.eu/admin.php\n";

    $headers = "From: Kerala Founders <hello@keralafounders.eu>\r\n"
        . "Content-Type: text/plain; charset=UTF-8";

    @mail($to, $subject, $body, $headers);
}
