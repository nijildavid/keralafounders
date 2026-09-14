<?php
declare(strict_types=1);

// Duplicate-detection aid for new research CSVs. Local tool only — never deployed.
// Run this FIRST on any new country CSV, before the manual editorial pass, so
// already-covered businesses (same company under a different name/slug, or
// re-exported research) get flagged instead of re-reviewed from scratch.
//
// Usage: php check-duplicates.php <csv-path>

require __DIR__ . '/../config/db.php';

[, $csvPath] = $argv + [null, null];

if (!$csvPath || !is_file($csvPath)) {
    fwrite(STDERR, "Usage: php check-duplicates.php <csv-path>\n");
    exit(1);
}

// Common legal suffixes across the countries seen so far — stripped before comparing,
// so "Kerala Kitchen" and "Kerala Kitchen GmbH" normalize to the same thing.
const LEGAL_SUFFIXES = [
    'gmbh', 'ug haftungsbeschraenkt', 'ug', 'ltd', 'limited', 'llc', 'inc', 'plc',
    's r o', 'sro', 'sarl', 'sa', 'sl', 'bv', 'ab', 'oy', 'aps', 'kg', 'ehf',
    'pvt ltd', 'private limited', 'co', 'corp', 'corporation', 'company', 'holdings',
];

function normalize_name(string $name): string
{
    $name = mb_strtolower(trim($name));
    $name = preg_replace('/[^a-z0-9]+/u', ' ', $name);
    $name = trim($name);
    foreach (LEGAL_SUFFIXES as $suffix) {
        $name = preg_replace('/(^|\s)' . preg_quote($suffix, '/') . '($|\s)/', ' ', $name);
    }
    return trim(preg_replace('/\s+/', ' ', $name));
}

function slugify(string $name): string
{
    $slug = strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    return $slug !== '' ? $slug : 'company';
}

function extract_domain(string $url): ?string
{
    $url = trim($url);
    if ($url === '') {
        return null;
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) {
        return null;
    }
    return preg_replace('/^www\./i', '', strtolower($host));
}

// Detect the "name" and "website" columns from whatever header the CSV happens to use —
// every batch so far has named these differently.
function detect_column(array $header, array $candidates): ?string
{
    $normalized = [];
    foreach ($header as $col) {
        $normalized[$col] = strtolower(trim($col));
    }
    foreach ($candidates as $candidate) {
        foreach ($normalized as $original => $norm) {
            if ($norm === $candidate) {
                return $original;
            }
        }
    }
    foreach ($candidates as $candidate) {
        foreach ($normalized as $original => $norm) {
            if (str_contains($norm, $candidate)) {
                return $original;
            }
        }
    }
    return null;
}

$rows = [];
$header = null;
$handle = fopen($csvPath, 'r');
while (($fields = fgetcsv($handle)) !== false) {
    if ($header === null) {
        $fields[0] = preg_replace('/^\xEF\xBB\xBF/', '', $fields[0]);
        $header = $fields;
        continue;
    }
    if (count($fields) === 1 && trim((string)$fields[0]) === '') {
        continue;
    }
    if (count($fields) < count($header)) {
        continue;
    }
    $rows[] = array_combine($header, array_slice($fields, 0, count($header)));
}
fclose($handle);

$nameCol = detect_column($header, ['business_name', 'business name', 'name', 'business / organisation name', 'business / organisation']);
$websiteCol = detect_column($header, ['website']);

if (!$nameCol) {
    fwrite(STDERR, "Could not detect a name column in the header. Columns found: " . implode(', ', $header) . "\n");
    exit(1);
}

$db = get_db();

$existing = $db->query('SELECT slug, name, city, country, website, status FROM companies')->fetchAll();
$existingBySlug = [];
$existingByNormName = [];
$existingByDomain = [];
foreach ($existing as $row) {
    $existingBySlug[$row['slug']] = $row;
    $existingByNormName[normalize_name($row['name'])][] = $row;
    if ($domain = extract_domain((string)($row['website'] ?? ''))) {
        $existingByDomain[$domain][] = $row;
    }
}

echo "Checked against {$nameCol}" . ($websiteCol ? " and {$websiteCol}" : '') . " columns, "
    . count($existing) . " existing companies in the database.\n\n";

$flaggedCount = 0;
foreach ($rows as $i => $row) {
    $name = trim((string)($row[$nameCol] ?? ''));
    if ($name === '') {
        continue;
    }
    $website = $websiteCol ? trim((string)($row[$websiteCol] ?? '')) : '';
    $normName = normalize_name($name);
    $slug = slugify($name);
    $domain = extract_domain($website);

    $matches = [];
    if (isset($existingBySlug[$slug])) {
        $matches[] = ['reason' => 'slug match', 'row' => $existingBySlug[$slug]];
    }
    foreach ($existingByNormName[$normName] ?? [] as $m) {
        if ($m['slug'] !== $slug) {
            $matches[] = ['reason' => 'name match', 'row' => $m];
        }
    }
    if ($domain && isset($existingByDomain[$domain])) {
        foreach ($existingByDomain[$domain] as $m) {
            if (!in_array($m, array_column($matches, 'row'), true)) {
                $matches[] = ['reason' => 'website match', 'row' => $m];
            }
        }
    }
    // Substring containment as a softer backup signal (e.g. "Kerala Kitchen" vs
    // "Kerala Kitchen Eindhoven" — different businesses, same brand family; still
    // worth a human glance). Requires BOTH sides to be multi-word so a single
    // generic word (e.g. "Kerala" left over after suffix-stripping "Kerala SA")
    // doesn't match half the database.
    if (!$matches && str_contains($normName, ' ')) {
        foreach ($existingByNormName as $existingNorm => $ms) {
            if ($existingNorm !== '' && $existingNorm !== $normName && str_contains($existingNorm, ' ')
                && (str_contains($existingNorm, $normName) || str_contains($normName, $existingNorm))) {
                foreach ($ms as $m) {
                    $matches[] = ['reason' => 'partial name match', 'row' => $m];
                }
            }
        }
    }

    if ($matches) {
        $flaggedCount++;
        echo "ROW " . ($i + 2) . ": \"{$name}\"" . ($website ? " ({$website})" : '') . "\n";
        foreach ($matches as $m) {
            $r = $m['row'];
            echo "  -> {$m['reason']}: \"{$r['name']}\" [{$r['slug']}] — {$r['city']}, {$r['country']} — {$r['status']}\n";
        }
        echo "\n";
    }
}

echo "Done. {$flaggedCount} of " . count($rows) . " rows flagged as possible duplicates.\n";
