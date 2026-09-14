<?php
// Local-only tool. Reads the fixed enrichment CSV and emits slug-keyed UPDATE
// statements for companies where a contact email was actually found.
// Usage: php generate-contact-update.php <csv-path> > migration-import-contacts-batch1.sql

if ($argc < 2) {
    fwrite(STDERR, "Usage: php generate-contact-update.php <csv-path>\n");
    exit(1);
}

$path = $argv[1];
$f = fopen($path, 'r');
if (!$f) {
    fwrite(STDERR, "Cannot open $path\n");
    exit(1);
}

$header = fgetcsv($f);
$idx = array_flip($header);

function sqlq($v) {
    if ($v === null || $v === '') return 'NULL';
    return "'" . str_replace("'", "''", $v) . "'";
}

$rows = [];
$found = 0;
$highOfficial = 0;
while ($row = fgetcsv($f)) {
    $slug = $row[$idx['slug']];
    $email = trim($row[$idx['contact_email']]);
    if ($email === '') {
        continue;
    }
    $found++;
    $type = $row[$idx['email_type']];
    $source = $row[$idx['email_source']];
    $confidence = $row[$idx['email_confidence']];
    $sourceUrl = $row[$idx['email_source_url']];

    if ($confidence === 'High' && $source === 'Official website') {
        $highOfficial++;
    }

    $rows[] = sprintf(
        "UPDATE companies SET contact_email=%s, email_type=%s, email_source=%s, email_confidence=%s, email_source_url=%s WHERE slug=%s;",
        sqlq($email), sqlq($type), sqlq($source), sqlq($confidence), sqlq($sourceUrl), sqlq($slug)
    );
}
fclose($f);

echo "-- Kerala Founders: import contact-enrichment results (batch 1)\n";
echo "-- Generated from " . basename($path) . " -- $found companies with a found contact email.\n";
echo "-- Safe to run regardless of production's current auto-increment state (matches by slug).\n\n";
foreach ($rows as $r) {
    echo $r . "\n";
}

fwrite(STDERR, "Total with an email found: $found\n");
fwrite(STDERR, "Of those, High confidence + Official website: $highOfficial\n");
