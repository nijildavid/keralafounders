<?php
declare(strict_types=1);

// Reusable CSV importer for Kerala Founders. Local tool only — never deployed to the server.
// Usage: php import-csv.php <csv-path> <excluded-slugs-comma-sep> <pending-slugs-comma-sep>

require __DIR__ . '/../config/db.php';

[, $csvPath, $excludedArg, $pendingArg] = $argv + [null, null, '', ''];

if (!$csvPath || !is_file($csvPath)) {
    fwrite(STDERR, "Usage: php import-csv.php <csv-path> <excluded-slugs> <pending-slugs>\n");
    exit(1);
}

$excluded = array_filter(array_map('trim', explode(',', $excludedArg)));
$pending = array_filter(array_map('trim', explode(',', $pendingArg)));

function slugify(string $name): string
{
    $slug = strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    return $slug !== '' ? $slug : 'company';
}

function uniqueSlug(PDO $db, string $desired): string
{
    $slug = $desired;
    $check = $db->prepare('SELECT COUNT(*) FROM companies WHERE slug = ?');
    for ($suffix = 2; ; $suffix++) {
        $check->execute([$slug]);
        if ((int)$check->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $desired . '-' . $suffix;
    }
}

$rows = [];
$header = null;
$handle = fopen($csvPath, 'r');
while (($fields = fgetcsv($handle)) !== false) {
    if ($header === null) {
        $fields[0] = preg_replace('/^\xEF\xBB\xBF/', '', $fields[0]); // strip BOM from first header field
        $header = $fields;
        continue;
    }
    if (count($fields) === 1 && trim((string)$fields[0]) === '') {
        continue; // skip blank trailing lines
    }
    if (count($fields) < count($header)) {
        continue;
    }
    $rows[] = array_combine($header, array_slice($fields, 0, count($header)));
}
fclose($handle);

$db = get_db();

$imported = ['approved' => 0, 'pending' => 0];
$skipped = 0;

foreach ($rows as $row) {
    $slug = trim($row['slug'] ?? '');
    if ($slug === '' || in_array($slug, $excluded, true)) {
        $skipped++;
        continue;
    }

    $status = in_array($slug, $pending, true) ? 'pending' : 'approved';

    $name = trim($row['name'] ?? '');
    $city = trim($row['city'] ?? '');
    $country = trim($row['country'] ?? '');
    $industry = trim($row['industry'] ?? $row['business_type'] ?? '');
    $website = trim($row['website'] ?? '');
    $foundedYear = trim($row['founded_year'] ?? '');

    // Data cleanup: city/country swapped or duplicated
    if ($country === '' && $city !== '') {
        $country = $city;
        $city = '';
    }
    if ($city !== '' && strcasecmp($city, $country) === 0) {
        $city = '';
    }
    // Slash-separated cities: take the first one
    if (strpos($city, '/') !== false) {
        $city = trim(explode('/', $city)[0]);
    }

    $location = trim($city !== '' ? "$city, $country" : $country);

    $founderNames = array_filter(array_map('trim', explode(';', $row['founder_names'] ?? '')));

    // This CSV format has no description column — build a plain, safe fallback
    // rather than leaving the public "About the company" section empty.
    $description = trim($industry) . ' company';
    if ($founderNames) {
        $description .= ' founded by ' . implode(', ', $founderNames);
    }
    $description .= $location !== '' ? " in $location." : '.';

    $finalSlug = uniqueSlug($db, slugify($slug));

    $db->beginTransaction();

    $stmt = $db->prepare(
        'INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified)
         VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 0)'
    );
    $stmt->execute([
        $finalSlug,
        $name,
        $website ?: null,
        $industry,
        $foundedYear !== '' ? (int)$foundedYear : null,
        $country,
        $city,
        $location,
        $description,
        $status,
    ]);
    $companyId = (int)$db->lastInsertId();

    $founderStmt = $db->prepare(
        'INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES (?, ?, NULL, NULL, 0)'
    );
    foreach ($founderNames as $founderName) {
        if ($founderName !== '') {
            $founderStmt->execute([$companyId, $founderName]);
        }
    }

    $db->commit();
    $imported[$status]++;
}

echo "Imported: {$imported['approved']} approved, {$imported['pending']} pending. Skipped: $skipped.\n";
