<?php
// Validates drafts in drafts/country-guides/<slug>/ against the Country Guide
// Standard. Merges each draft into a TEMP COPY of public/content/guidance (the
// real files are never touched), then runs scripts/validate-guidance-content.php
// against that copy, plus Standard checks the live validator does not do.
//   php scripts/validate-guide-drafts.php [slug ...]
// Exit 0 = all pass, 1 = failures.

$root = dirname(__DIR__);
$draftsDir = $root . '/drafts/country-guides';
$only = array_slice($argv, 1);
$slugs = [];
foreach (glob($draftsDir . '/*/guide.json') as $p) {
    $s = basename(dirname($p));
    if (!$only || in_array($s, $only, true)) {
        $slugs[] = $s;
    }
}
if (!$slugs) {
    echo "No drafts found.\n";
    exit(0);
}

function rcopy(string $src, string $dst): void
{
    @mkdir($dst, 0777, true);
    foreach (scandir($src) as $f) {
        if ($f === '.' || $f === '..') continue;
        is_dir("$src/$f") ? rcopy("$src/$f", "$dst/$f") : copy("$src/$f", "$dst/$f");
    }
}
function jload(string $p)
{
    $d = json_decode(file_get_contents($p), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        fwrite(STDERR, "[FAIL] $p: invalid JSON — " . json_last_error_msg() . "\n");
        exit(1);
    }
    return $d;
}

$required = ['overview', 'legal-forms', 'sole-steps', 'company-steps', 'taxes', 'social', 'visa'];
$fail = 0;
foreach ($slugs as $slug) {
    $d = "$draftsDir/$slug";
    $tmp = sys_get_temp_dir() . '/kf-draft-' . $slug . '-' . getmypid();
    rcopy($root . '/public/content/guidance', $tmp . '/guidance');
    $own = [];

    $guide = jload("$d/guide.json");
    $sources = is_file("$d/sources.json") ? jload("$d/sources.json") : [];
    $faq = is_file("$d/faq.json") ? jload("$d/faq.json") : [];
    $hub = is_file("$d/hub-entry.json") ? jload("$d/hub-entry.json") : [];

    // Standard checks
    $ids = array_column($guide['sections'] ?? [], 'id');
    foreach ($required as $r) {
        if (!in_array($r, $ids, true)) $own[] = "missing required section \"$r\"";
    }
    $qf = array_keys($guide['quick_facts'] ?? []);
    sort($qf);
    if ($qf !== ['cost', 'gotcha', 'structure', 'time']) $own[] = 'quick_facts must be exactly structure, cost, time, gotcha';
    $n = count($guide['founder_checklist'] ?? []);
    if ($n < 6 || $n > 10) $own[] = "founder_checklist has $n items, expected 6 to 10";
    $mine = array_filter($faq, fn($f) => ($f['country'] ?? '') === $slug);
    if (count($mine) < 5) $own[] = 'fewer than 5 FAQ entries for ' . $slug;
    foreach ($guide['sections'] ?? [] as $s) {
        if (($s['id'] ?? '') === 'visa' && empty($s['hold'])) $own[] = 'visa section must be hold:true in a draft';
    }
    $prefix = strtoupper(substr($sources[0]['id'] ?? '', 0, 2));
    foreach ($sources as $s) {
        foreach (['id', 'title', 'publisher', 'type', 'url', 'language', 'accessed'] as $k) {
            if (empty($s[$k])) $own[] = "source " . ($s['id'] ?? '?') . " missing $k";
        }
        if (!in_array($s['type'] ?? '', ['official', 'chamber', 'government_portal', 'secondary'], true)) $own[] = "source {$s['id']} has unknown type";
    }
    $existing = array_column(jload($tmp . '/guidance/sources.json'), 'id');
    foreach ($sources as $s) {
        if (in_array($s['id'] ?? '', $existing, true)) $own[] = "source id {$s['id']} already exists in live sources.json";
    }
    if (!is_file("$d/evidence-log.md")) $own[] = 'evidence-log.md missing';

    // ---- Standard evals added 14 Oct 2026 (approved wording, CG-06) ----
    $srcById = [];
    foreach (array_merge(jload($tmp . '/guidance/sources.json'), $sources) as $x) { $srcById[$x['id']] = $x; }
    $log = is_file("$d/evidence-log.md") ? file_get_contents("$d/evidence-log.md") : '';
    if (stripos($log, 'Independent check') === false) $own[] = 'QA4: evidence-log.md has no "Independent check" section (second-agent re-verification missing)';
    $forbidden = '/\b(reviewed by|lawyer[- ]reviewed|legally reviewed|legal review|we guarantee|guaranteed (?:approval|result|speed|to))\b/i';
    $needsYear = [];
    foreach ($guide['sections'] ?? [] as $sec) {
        $isVisa = ($sec['id'] ?? '') === 'visa';
        if ($isVisa) {
            $note = (string)($sec['hold_note'] ?? '');
            $sid = $sec['hold_note_source_id'] ?? '';
            if ($note === '') $own[] = 'visa: hold_note missing';
            if (preg_match($forbidden, $note)) $own[] = 'visa: hold_note uses a forbidden review/guarantee phrase';
            if ($sid === '' || !isset($srcById[$sid])) {
                $own[] = 'visa: hold_note_source_id missing or not a known source (readers need an official link)';
            } elseif (!in_array($srcById[$sid]['type'], ['official', 'government_portal'], true) || strpos($srcById[$sid]['url'], 'https://') !== 0) {
                $own[] = 'visa: hold_note_source_id must be an official https page';
            }
        }
        if (!empty($sec['hold'])) continue;
        foreach (['official_information', 'expert_interpretation', 'founder_experience'] as $k) {
            foreach ($sec[$k] ?? [] as $i => $c) {
                $conf = $c['confidence'] ?? 'official';
                $text = (string)($c['text'] ?? '');
                if (preg_match($forbidden, $text)) $own[] = "section {$sec['id']} $k #$i uses a forbidden review/guarantee phrase";
                if ($conf === 'official') {
                    $types = array_map(fn($id) => $srcById[$id]['type'] ?? '?', $c['source_ids'] ?? []);
                    if ($types && !array_diff($types, ['secondary'])) $own[] = "section {$sec['id']} $k #$i is shown as official but cites only secondary sources";
                    if (preg_match('/(€|EUR|PLN|CZK|SEK|CHF|\d\s?%)/', $text) && !preg_match('/\b20(2\d|3\d)\b/', $text)) $needsYear[] = "{$sec['id']} $k #$i";
                }
            }
        }
    }
    foreach ($mine as $f) {
        if (($f['status'] ?? '') === 'hold') continue;
        if (preg_match($forbidden, ($f['answer'] ?? '') . ' ' . ($f['question'] ?? ''))) $own[] = 'FAQ ' . ($f['id'] ?? '?') . ' uses a forbidden review/guarantee phrase';
    }
    if ($needsYear) {
        // Standard section 4: every annual figure carries its year. Heuristic, so reported as a warning with a count, not a failure.
        fwrite(STDERR, "[WARN] $slug: " . count($needsYear) . " rendered claims show a figure without a 20xx year (first: " . implode(', ', array_slice($needsYear, 0, 4)) . ")\n");
    }

    // Merge into the temp copy
    $all = array_merge(jload($tmp . '/guidance/sources.json'), $sources);
    file_put_contents($tmp . '/guidance/sources.json', json_encode($all, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $allFaq = array_merge(jload($tmp . '/guidance/faq.json'), $faq);
    file_put_contents($tmp . '/guidance/faq.json', json_encode($allFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    @mkdir($tmp . '/guidance/' . $slug);
    copy("$d/guide.json", $tmp . '/guidance/' . $slug . '/guide.json');
    $countries = jload($tmp . '/guidance/countries.json');
    $found = false;
    foreach ($countries as &$c) {
        if ($c['slug'] === $slug) {
            $found = true;
            $c['status'] = 'ready_for_review';
            $c['last_checked'] = $hub['last_checked'] ?? $guide['last_checked'] ?? null;
            $c['next_check_due'] = $hub['next_check_due'] ?? $guide['next_check_due'] ?? null;
        }
    }
    unset($c);
    if (!$found) $own[] = "slug \"$slug\" is not in countries.json";
    file_put_contents($tmp . '/guidance/countries.json', json_encode($countries, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    // Run the live validator against the temp copy
    $code = file_get_contents($root . '/scripts/validate-guidance-content.php');
    $code = str_replace("__DIR__ . '/../public/assets/guidance-date-helpers.php'", var_export($root . '/public/assets/guidance-date-helpers.php', true), $code);
    $code = str_replace("define('CONTENT_DIR', __DIR__ . '/../public/content/guidance');", "define('CONTENT_DIR', " . var_export($tmp . '/guidance', true) . ");", $code);
    file_put_contents($tmp . '/v.php', $code);
    exec('php ' . escapeshellarg($tmp . '/v.php') . ' 2>&1', $out, $rc);
    $liveFail = array_filter($out, fn($l) => str_starts_with($l, '[FAIL]') && (str_contains($l, "$slug/") || str_contains($l, "\"$slug\"") || str_contains($l, 'faq.json') || str_contains($l, 'sources.json')));
    echo "== $slug ==\n";
    foreach ($own as $m) echo "[FAIL] standard: $m\n";
    foreach ($liveFail as $m) echo $m . "\n";
    if (!$own && !$liveFail) echo "OK\n";
    $fail += count($own) + count($liveFail);
    exec('rm -rf ' . escapeshellarg($tmp));
    unset($out);
}
exit($fail ? 1 : 0);
