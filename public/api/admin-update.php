<?php
declare(strict_types=1);

require __DIR__ . '/../../config/auth.php';
require_admin();
require __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

if (!csrf_verify($input['csrfToken'] ?? null)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid or expired form. Please refresh the page and try again.']);
    exit;
}

$id = (int)($input['id'] ?? 0);
$status = (string)($input['status'] ?? 'pending');
$verified = ((int)($input['verified'] ?? 0)) === 1 ? 1 : 0;
$name = trim((string)($input['company'] ?? ''));
$industry = trim((string)($input['industry'] ?? ''));
$businessType = trim((string)($input['businessType'] ?? ''));
$industryDetail = trim((string)($input['industryDetail'] ?? ''));
$country = trim((string)($input['country'] ?? ''));
$city = trim((string)($input['city'] ?? ''));
$location = trim((string)($input['location'] ?? ''));
$description = trim((string)($input['description'] ?? ''));
$keralaConnection = trim((string)($input['keralaConnection'] ?? ''));
$keralaDistrict = trim((string)($input['keralaDistrict'] ?? ''));
$contactOkPodcastStories = !empty($input['contactOkPodcastStories']);
$founders = is_array($input['founders'] ?? null) ? $input['founders'] : [];
$founders = array_values(array_filter($founders, fn($f) => trim((string)($f['name'] ?? '')) !== ''));

// Kept in sync with submit-company.php's Instagram normalization.
$instagram = null;
$instagramRaw = trim((string)($input['instagram'] ?? ''));
if ($instagramRaw !== '') {
    $handle = strtolower(preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $instagramRaw));
    $handle = preg_replace('/[\/?#].*$/', '', $handle);
    $handle = ltrim($handle, '@');
    if (preg_match('/^[a-z0-9._]{1,30}$/', $handle)) {
        $instagram = $handle;
    }
}

if ($id <= 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Company not found']);
    exit;
}
if (!in_array($status, ['pending', 'approved'], true)) {
    $status = 'pending';
}
if ($name === '' || $industry === '' || $businessType === '' || $country === '' || $city === '' || $description === '' || !$founders) {
    http_response_code(422);
    echo json_encode(['error' => 'Please fill in all required fields.']);
    exit;
}

$db = get_db();

$check = $db->prepare('SELECT id FROM companies WHERE id = ?');
$check->execute([$id]);
if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Company not found']);
    exit;
}

$db->beginTransaction();

$stmt = $db->prepare(
    'UPDATE companies SET name = ?, website = ?, instagram = ?, industry = ?, business_type = ?, industry_detail = ?, kerala_connection = ?, kerala_district = ?, contact_ok_podcast_stories = ?, size = ?, founded_year = ?, country = ?, city = ?, location = ?, description = ?, status = ?, verified = ? WHERE id = ?'
);
$stmt->execute([
    $name,
    trim((string)($input['website'] ?? '')) ?: null,
    $instagram,
    $industry,
    $businessType,
    $industryDetail ?: null,
    $keralaConnection ?: null,
    $keralaDistrict ?: null,
    $contactOkPodcastStories ? 1 : 0,
    trim((string)($input['size'] ?? '')) ?: null,
    !empty($input['founded']) ? (int)$input['founded'] : null,
    $country,
    $city,
    $location ?: null,
    $description,
    $status,
    $verified,
    $id,
]);

$db->prepare('DELETE FROM founders WHERE company_id = ?')->execute([$id]);
$founderStmt = $db->prepare(
    'INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES (?, ?, ?, ?, ?)'
);
foreach ($founders as $f) {
    $founderStmt->execute([
        $id,
        trim((string)$f['name']),
        trim((string)($f['email'] ?? '')) ?: null,
        trim((string)($f['linkedin'] ?? '')) ?: null,
        !empty($f['showEmail']) ? 1 : 0,
    ]);
}

$db->prepare('DELETE FROM branches WHERE company_id = ?')->execute([$id]);
$branchStmt = $db->prepare('INSERT INTO branches (company_id, country) VALUES (?, ?)');
foreach (($input['branches'] ?? []) as $branch) {
    $branch = trim((string)$branch);
    if ($branch !== '') {
        $branchStmt->execute([$id, $branch]);
    }
}

$db->commit();

echo json_encode(['ok' => true]);
