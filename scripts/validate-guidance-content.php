<?php
// CLI validator for the Guidance content files. Run manually:
//   php scripts/validate-guidance-content.php
// Exits 0 if everything checks out, 1 (with a summary) otherwise. Uses the
// same date-math helper the live pages use, so this can never silently
// drift from what actually renders.

require_once __DIR__ . '/../public/assets/guidance-date-helpers.php';

define('CONTENT_DIR', __DIR__ . '/../public/content/guidance');

$failures = [];
$warnings = [];

function fail(array &$failures, string $file, string $message): void
{
    $failures[] = "[FAIL] $file: $message";
}

function warn(array &$warnings, string $file, string $message): void
{
    $warnings[] = "[WARN] $file: $message";
}

/** Reads+decodes a JSON file, recording a failure and returning null if it can't. */
function load(array &$failures, string $relativePath)
{
    $path = CONTENT_DIR . '/' . $relativePath;
    if (!is_file($path)) {
        fail($failures, $relativePath, 'file does not exist');
        return null;
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        fail($failures, $relativePath, 'invalid JSON — ' . json_last_error_msg());
        return null;
    }
    return $data;
}

$countries = load($failures, 'countries.json');
$sources = load($failures, 'sources.json');
$faq = load($failures, 'faq.json');

if ($failures) {
    // Can't safely cross-check anything if the base files don't even parse.
    print_report($failures, $warnings);
    exit(1);
}

$sourceIds = array_column($sources, 'id');
$sourceIdSet = array_flip($sourceIds);

$referencedSourceIds = []; // id => true, for every source_id seen anywhere

function check_source_ids(array &$failures, array &$referencedSourceIds, array $sourceIdSet, string $file, array $ids, string $context): void
{
    foreach ($ids as $id) {
        $referencedSourceIds[$id] = true;
        if (!isset($sourceIdSet[$id])) {
            fail($failures, $file, "$context references source id \"$id\" which does not exist in sources.json");
        }
    }
}

/**
 * A claim needs a non-empty source_ids array unless it's confidence 'hold'
 * (never rendered) or 'editorial' (synthesis/recommendation text, not a
 * sourced fact — matches how the build doc's own "Expert interpretation"
 * paragraphs are written, with no inline citations).
 */
function check_claim(array &$failures, array &$referencedSourceIds, array $sourceIdSet, string $file, array $claim, string $context): void
{
    $confidence = $claim['confidence'] ?? 'official';
    $ids = $claim['source_ids'] ?? [];
    check_source_ids($failures, $referencedSourceIds, $sourceIdSet, $file, $ids, $context);
    if (!in_array($confidence, ['hold', 'editorial'], true) && !$ids) {
        fail($failures, $file, "$context has confidence \"$confidence\" but no source_ids — every rendered claim needs a visible source");
    }
}

// --- countries.json ---
$slugsNeedingGuide = [];
foreach ($countries as $c) {
    $slug = $c['slug'] ?? '(missing slug)';
    $file = 'countries.json';
    if (empty($c['name'])) {
        fail($failures, $file, "$slug: missing name");
    }
    $status = $c['status'] ?? '';
    if (!in_array($status, ['live', 'ready_for_review', 'coming_soon', 'planned'], true)) {
        fail($failures, $file, "$slug: unknown status \"$status\"");
    }
    if (in_array($status, ['live', 'ready_for_review'], true)) {
        $slugsNeedingGuide[] = $slug;
        if (empty($c['last_checked']) || empty($c['next_check_due'])) {
            fail($failures, $file, "$slug: status \"$status\" requires last_checked and next_check_due to be set");
        } else {
            $expected = guidance_add_months_safe($c['last_checked'], 6);
            if ($c['next_check_due'] !== $expected) {
                fail($failures, $file, "$slug: next_check_due is \"{$c['next_check_due']}\", expected \"$expected\" (last_checked + 6 months)");
            }
        }
    }
}

// --- per-country guide.json ---
foreach ($slugsNeedingGuide as $slug) {
    $guideFile = "$slug/guide.json";
    $guide = load($failures, $guideFile);
    if ($guide === null) {
        continue;
    }

    $countryEntry = null;
    foreach ($countries as $c) {
        if (($c['slug'] ?? null) === $slug) {
            $countryEntry = $c;
            break;
        }
    }

    if (empty($guide['last_checked']) || empty($guide['next_check_due'])) {
        fail($failures, $guideFile, 'missing last_checked or next_check_due');
    } else {
        $expected = guidance_add_months_safe($guide['last_checked'], 6);
        if ($guide['next_check_due'] !== $expected) {
            fail($failures, $guideFile, "next_check_due is \"{$guide['next_check_due']}\", expected \"$expected\" (last_checked + 6 months)");
        }
        if ($countryEntry) {
            if ($guide['last_checked'] !== $countryEntry['last_checked']) {
                fail($failures, $guideFile, "last_checked (\"{$guide['last_checked']}\") does not match countries.json (\"{$countryEntry['last_checked']}\") for \"$slug\"");
            }
            if ($guide['next_check_due'] !== $countryEntry['next_check_due']) {
                fail($failures, $guideFile, "next_check_due (\"{$guide['next_check_due']}\") does not match countries.json (\"{$countryEntry['next_check_due']}\") for \"$slug\"");
            }
        }
    }

    foreach ($guide['sections'] ?? [] as $section) {
        $sid = $section['id'] ?? '(missing id)';
        $context = "section \"$sid\"";

        if (empty($section['hold']) && ($section['review_status'] ?? '') === 'pending') {
            fail($failures, $guideFile, "$context: hold is false but review_status is \"pending\" — either flag it hold:true or mark it reviewed");
        }

        foreach (['official_information', 'expert_interpretation', 'founder_experience'] as $partKey) {
            foreach ($section[$partKey] ?? [] as $i => $claim) {
                check_claim($failures, $referencedSourceIds, $sourceIdSet, $guideFile, $claim, "$context $partKey #$i");
            }
        }
        // Checklist items are action steps, not sourced facts, so they're exempt
        // from the "must have a source" rule — but any source id they DO carry
        // still has to actually exist.
        foreach ($section['checklist'] ?? [] as $i => $item) {
            check_source_ids($failures, $referencedSourceIds, $sourceIdSet, $guideFile, $item['source_ids'] ?? [], "$context checklist #$i");
        }
        if (!empty($section['hold_note_source_id'])) {
            check_source_ids($failures, $referencedSourceIds, $sourceIdSet, $guideFile, [$section['hold_note_source_id']], "$context hold_note_source_id");
        }
    }
}

// Warn (not fail) about a guide.json that exists but isn't referenced by countries.json —
// exactly what would catch a leftover fake test country after the acceptance-checklist
// "add a fake second country" step.
foreach (glob(CONTENT_DIR . '/*/guide.json') as $path) {
    $slug = basename(dirname($path));
    $listed = false;
    foreach ($countries as $c) {
        if (($c['slug'] ?? null) === $slug) {
            $listed = true;
            break;
        }
    }
    if (!$listed) {
        warn($warnings, "$slug/guide.json", 'exists but is not listed in countries.json — orphaned content?');
    }
}

// --- faq.json ---
foreach ($faq as $i => $entry) {
    $id = $entry['id'] ?? "#$i";
    $file = 'faq.json';
    $status = $entry['status'] ?? '';
    $ids = $entry['source_ids'] ?? [];
    check_source_ids($failures, $referencedSourceIds, $sourceIdSet, $file, $ids, "FAQ \"$id\"");
    if ($status !== 'hold' && !$ids) {
        fail($failures, $file, "FAQ \"$id\" has status \"$status\" but no source_ids");
    }
    if (empty($entry['last_checked'])) {
        fail($failures, $file, "FAQ \"$id\" missing last_checked");
    }
}

print_report($failures, $warnings);
exit($failures ? 1 : 0);

function print_report(array $failures, array $warnings): void
{
    foreach ($warnings as $w) {
        echo $w . PHP_EOL;
    }
    foreach ($failures as $f) {
        echo $f . PHP_EOL;
    }
    if (!$failures) {
        echo 'OK — Guidance content passed all checks' . (count($warnings) ? ' (' . count($warnings) . ' warning(s))' : '') . '.' . PHP_EOL;
    } else {
        echo count($failures) . ' failure(s), ' . count($warnings) . ' warning(s).' . PHP_EOL;
    }
}
