<?php
declare(strict_types=1);

// Business type is no longer collected from the public form (Phase 1 of the
// add-company rebuild) — every new submission gets this fallback and an
// admin corrects it during review from admin-edit.php's dropdown, which is
// unchanged. See migration-add-founder-context-fields.sql for the schema.
const DEFAULT_BUSINESS_TYPE = 'SME / Local Business';
const RATE_LIMIT_WINDOW_MINUTES = 10;
const RATE_LIMIT_MAX_SUBMISSIONS = 5;

header('Content-Type: application/json');
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/auth.php';
require __DIR__ . '/../../config/reference.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

// Honeypot: a real visitor never fills this in. Pretend success so a bot
// doesn't learn the field is being checked, but don't write a row.
if (trim((string)($input['hpField'] ?? '')) !== '') {
    echo json_encode(['ok' => true]);
    exit;
}

$name = trim((string)($input['company'] ?? ''));
$industry = trim((string)($input['industry'] ?? ''));
$industryDetail = trim((string)($input['industryDetail'] ?? ''));
$country = trim((string)($input['country'] ?? ''));
$city = trim((string)($input['city'] ?? ''));
$location = trim((string)($input['location'] ?? ''));
$description = trim((string)($input['description'] ?? ''));
$keralaConnection = trim((string)($input['keralaConnection'] ?? ''));
$keralaDistrict = trim((string)($input['keralaDistrict'] ?? ''));
if (!in_array($keralaDistrict, $KF_KERALA_DISTRICTS, true)) {
    $keralaDistrict = '';
}
$contactOkPodcastStories = !empty($input['contactOkPodcastStories']);
$founders = is_array($input['founders'] ?? null) ? $input['founders'] : [];
$founders = array_values(array_filter($founders, fn($f) => trim((string)($f['name'] ?? '')) !== ''));

if ($name === '' || $industry === '' || $country === '' || $city === '' || $description === '' || !$founders) {
    http_response_code(422);
    echo json_encode(['error' => 'Please fill in all required fields.']);
    exit;
}

if (mb_strlen($description) < 40) {
    http_response_code(422);
    echo json_encode(['error' => 'Add a little more to the description — at least 40 characters.']);
    exit;
}

$instagram = null;
$instagramRaw = trim((string)($input['instagram'] ?? ''));
if ($instagramRaw !== '') {
    $handle = strtolower(preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $instagramRaw));
    $handle = preg_replace('/[\/?#].*$/', '', $handle);
    $handle = ltrim($handle, '@');
    if (!preg_match('/^[a-z0-9._]{1,30}$/', $handle)) {
        http_response_code(422);
        echo json_encode(['error' => 'That Instagram handle does not look right — use letters, numbers, dots, or underscores.']);
        exit;
    }
    $instagram = $handle;
}

$db = get_db();

$ipHash = hash('sha256', GUIDANCE_FEEDBACK_IP_SALT . (string)($_SERVER['REMOTE_ADDR'] ?? ''));
$rateCheck = $db->prepare(
    'SELECT COUNT(*) FROM companies WHERE ip_hash = ? AND created_at > NOW() - INTERVAL ' . RATE_LIMIT_WINDOW_MINUTES . ' MINUTE'
);
$rateCheck->execute([$ipHash]);
if ((int)$rateCheck->fetchColumn() >= RATE_LIMIT_MAX_SUBMISSIONS) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many submissions from this connection — please try again later.']);
    exit;
}

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
    'INSERT INTO companies (slug, name, website, instagram, contact_ok_podcast_stories, contact_permission_at, industry, business_type, industry_detail, kerala_connection, kerala_district, size, founded_year, country, city, location, description, status, ip_hash)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", ?)'
);
$stmt->execute([
    $slug,
    $name,
    trim((string)($input['website'] ?? '')) ?: null,
    $instagram,
    $contactOkPodcastStories ? 1 : 0,
    $contactOkPodcastStories ? date('Y-m-d H:i:s') : null,
    $industry,
    DEFAULT_BUSINESS_TYPE,
    $industryDetail ?: null,
    $keralaConnection ?: null,
    $keralaDistrict ?: null,
    trim((string)($input['size'] ?? '')) ?: null,
    !empty($input['founded']) ? (int)$input['founded'] : null,
    $country,
    $city,
    $location ?: null,
    $description,
    $ipHash,
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

notify_new_submission($name, $industry, $country, $city, $location, $description, $founders, $keralaConnection, $keralaDistrict, $instagram);

echo json_encode(['ok' => true, 'slug' => $slug]);

function notify_new_submission(
    string $name,
    string $industry,
    string $country,
    string $city,
    ?string $location,
    string $description,
    array $founders,
    string $keralaConnection,
    string $keralaDistrict,
    ?string $instagram
): void {
    $to = 'hello@keralafounders.eu';
    $subject = "New company submission: $name";

    $founderLines = implode("\n", array_map(
        fn($f) => '- ' . trim((string)$f['name']) . (!empty($f['email']) ? ' <' . $f['email'] . '>' : ''),
        $founders
    ));

    $keralaLine = $keralaConnection !== '' ? $keralaConnection . ($keralaDistrict !== '' ? " ($keralaDistrict)" : '') : 'Not answered';
    $instagramLine = $instagram !== null ? "@$instagram" : 'Not given';

    $body = "A new company was submitted for review on Kerala Founders.\n\n"
        . "Company: $name\n"
        . "Industry: $industry\n"
        . "Location: " . ($location ?: 'Not given') . " ($city, $country)\n"
        . "Kerala connection: $keralaLine\n"
        . "Instagram: $instagramLine\n\n"
        . "Founders:\n$founderLines\n\n"
        . "Description:\n$description\n\n"
        . "Review it here: https://keralafounders.eu/admin.php\n";

    $headers = "From: Kerala Founders <hello@keralafounders.eu>\r\n"
        . "Content-Type: text/plain; charset=UTF-8";

    @mail($to, $subject, $body, $headers);
}
