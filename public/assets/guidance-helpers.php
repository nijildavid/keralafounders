<?php
// Render + data-loading helpers for the Guidance section, following the
// same small-pure-function convention as render-helpers.php: functions
// return HTML strings, all user/content text goes through h().
require_once __DIR__ . '/guidance-date-helpers.php';

define('GUIDANCE_CONTENT_DIR', __DIR__ . '/../content/guidance');

function guidance_load_json(string $relativePath): array
{
    $path = GUIDANCE_CONTENT_DIR . '/' . $relativePath;
    if (!is_file($path)) {
        return [];
    }
    $data = json_decode((string)file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function guidance_load_countries(): array
{
    $countries = guidance_load_json('countries.json');
    usort($countries, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
    return $countries;
}

/** Keyed by source id, e.g. ['S1' => [...], 'S2' => [...]]. */
function guidance_load_sources(): array
{
    $sources = guidance_load_json('sources.json');
    $byId = [];
    foreach ($sources as $s) {
        $byId[$s['id']] = $s;
    }
    return $byId;
}

function guidance_load_guide(string $countrySlug): ?array
{
    $path = GUIDANCE_CONTENT_DIR . '/' . $countrySlug . '/guide.json';
    if (!is_file($path)) {
        return null;
    }
    $data = json_decode((string)file_get_contents($path), true);
    return is_array($data) ? $data : null;
}

function guidance_load_faq(): array
{
    return guidance_load_json('faq.json');
}

/** How a country's status should be *displayed on the hub* — 'ready_for_review' reads as 'coming_soon' to visitors. */
function guidance_hub_display_status(string $status): string
{
    return $status === 'ready_for_review' ? 'coming_soon' : $status;
}

/** Whether a country guide can render its full content when visited directly by URL. */
function guidance_should_render_full(string $status, bool $hasGuideJson): bool
{
    return $hasGuideJson && in_array($status, ['live', 'ready_for_review'], true);
}

function guidance_status_chip_html(string $displayStatus): string
{
    $labels = ['live' => 'Live', 'coming_soon' => 'Coming soon', 'planned' => 'Planned'];
    $colors = ['live' => '#166534', 'coming_soon' => '#9a3412', 'planned' => '#6b7280'];
    $label = $labels[$displayStatus] ?? ucfirst($displayStatus);
    $color = $colors[$displayStatus] ?? '#6b7280';
    return '<span class="chip" style="color:' . h($color) . ';border-color:' . h($color) . '">' . h($label) . '</span>';
}

function guidance_country_card_html(array $country, array $countryFlags): string
{
    $isLive = ($country['status'] ?? '') === 'live';
    $displayStatus = guidance_hub_display_status($country['status'] ?? 'planned');
    $flag = $countryFlags[$country['name']] ?? '';
    $inner = '<strong>' . h(($flag !== '' ? $flag . ' ' : '') . $country['name']) . '</strong>'
        . guidance_status_chip_html($displayStatus);
    if ($isLive && !empty($country['last_checked'])) {
        $inner .= '<small>Last checked ' . h(guidance_format_date_long($country['last_checked'])) . '</small>';
    } elseif ($displayStatus === 'coming_soon' && !empty($country['target_month'])) {
        $inner .= '<small>Target: ' . h(guidance_format_month($country['target_month'])) . '</small>';
    }
    if ($isLive) {
        return '<a class="country-card guidance-country-card" href="guidance-country.php?country=' . rawurlencode($country['slug']) . '">' . $inner . '</a>';
    }
    return '<div class="country-card guidance-country-card guidance-country-card-disabled">' . $inner . '</div>';
}

/** '2026-11' -> 'November 2026' */
function guidance_format_month(string $yearMonth): string
{
    $d = DateTimeImmutable::createFromFormat('Y-m-d', $yearMonth . '-01');
    return $d ? $d->format('F Y') : $yearMonth;
}

function guidance_date_stamp_html(string $lastChecked, string $nextCheckDue): string
{
    return '<p class="guidance-date-stamp muted">Last checked: ' . h(guidance_format_date_long($lastChecked))
        . ' | Next check due: ' . h(guidance_format_date_long($nextCheckDue)) . '</p>';
}

function guidance_outdated_banner_html(string $lastChecked, string $nextCheckDue): string
{
    if (!guidance_is_outdated($nextCheckDue)) {
        return '';
    }
    return '<div class="error">This guide was last checked on ' . h(guidance_format_date_long($lastChecked))
        . ' and is overdue for its 6-month re-check. Rules may have changed. Please check the sources below before you act.</div>';
}

/** Single source of truth for the disclaimer wording (doc section 6.1 — draft, pending lawyer review). */
function guidance_disclaimer_html(string $variant, string $lastChecked, string $nextCheckDue): string
{
    $last = h(guidance_format_date_long($lastChecked));
    $next = h(guidance_format_date_long($nextCheckDue));
    if ($variant === 'short') {
        return '<p class="notice guidance-disclaimer">General information from the sources shown, not advice. Last checked '
            . $last . '. Re-checked every 6 months. Check the source and consult a professional before you act.</p>';
    }
    return '<div class="notice guidance-disclaimer">This guide is general information, not legal, tax or immigration advice. '
        . 'It is compiled from the official and public sources listed on this page. Rules, fees and thresholds change, '
        . 'and we do not update a page the moment a source changes. We re-check the information in every guide every 6 months. '
        . 'Last checked: ' . $last . '. Next check due: ' . $next . '. Before you act, check each source yourself and speak to '
        . 'a qualified lawyer, tax advisor or the responsible authority about your own situation. Using this information is at your own risk.</div>';
}

/** Skips the 'overview' section (rendered as plain intro, not a TOC/content section). */
function guidance_toc_html(array $sections): string
{
    $items = '';
    foreach ($sections as $s) {
        if ($s['id'] === 'overview') {
            continue;
        }
        $items .= '<li><a href="#gs-' . h($s['id']) . '">' . h($s['title']) . '</a></li>';
    }
    if ($items === '') {
        return '';
    }
    return '<details open class="guidance-toc"><summary>Contents</summary><ul>' . $items . '</ul></details>';
}

function guidance_source_ref_html(array $sourceIds): string
{
    if (!$sourceIds) {
        return '';
    }
    $links = array_map(fn($id) => '<a href="#source-' . h($id) . '">[' . h($id) . ']</a>', $sourceIds);
    return '<sup class="guidance-source-refs">' . implode('', $links) . '</sup>';
}

function guidance_part_html(string $partKey, array $claims, array $usedSourceIds): array
{
    $labels = [
        'official_information' => 'Official information',
        'expert_interpretation' => 'Expert interpretation',
        'founder_experience' => 'Founder experience',
        'checklist' => 'Checklist',
    ];
    $items = '';
    foreach ($claims as $c) {
        $items .= '<li>' . h($c['text']) . guidance_source_ref_html($c['source_ids'] ?? [])
            . (($c['confidence'] ?? '') === 'secondary'
                ? ' <span class="chip guidance-secondary-tag" style="color:#9a3412;border-color:#9a3412">Secondary source</span>'
                : '') . '</li>';
        foreach ($c['source_ids'] ?? [] as $sid) {
            $usedSourceIds[$sid] = true;
        }
    }
    $partClass = $partKey === 'checklist' ? 'guidance-checklist' : '';
    $html = '<div class="guidance-section-part ' . h($partClass) . '"><div class="eyebrow">' . h($labels[$partKey]) . '</div><ul>' . $items . '</ul></div>';
    return [$html, $usedSourceIds];
}

function guidance_table_html(array $table): string
{
    $head = '';
    foreach ($table['headers'] ?? [] as $col) {
        $head .= '<th>' . h($col) . '</th>';
    }
    $body = '';
    foreach ($table['rows'] ?? [] as $row) {
        $body .= '<tr>';
        foreach ($row as $cell) {
            $body .= '<td>' . h($cell) . '</td>';
        }
        $body .= '</tr>';
    }
    return '<div class="guidance-table-wrap"><table class="guidance-table"><thead><tr>' . $head . '</tr></thead><tbody>' . $body . '</tbody></table></div>';
}

function guidance_hold_placeholder_html(array $section, array $sourcesById): string
{
    $note = h($section['hold_note'] ?? 'This section is coming soon, currently under review.');
    $sourceId = $section['hold_note_source_id'] ?? null;
    $link = '';
    if ($sourceId && isset($sourcesById[$sourceId])) {
        $link = ' <a href="' . h($sourcesById[$sourceId]['url']) . '" target="_blank" rel="noopener">Official information page</a>';
    }
    return '<section id="gs-' . h($section['id']) . '" class="guidance-section"><h2>' . h($section['title']) . '</h2>'
        . '<div class="coming-soon-panel"><p class="muted" style="margin:0">' . $note . $link . '</p></div></section>';
}

/**
 * Renders one section. Returns [html, usedSourceIds] — usedSourceIds (array
 * keyed by source id) is merged into the caller's running set so the sources
 * box only lists sources actually cited by what rendered.
 */
function guidance_section_html(array $section, array $sourcesById, array $usedSourceIds): array
{
    if (!empty($section['hold'])) {
        return [guidance_hold_placeholder_html($section, $sourcesById), $usedSourceIds];
    }

    $parts = '';
    foreach (['official_information', 'expert_interpretation', 'founder_experience', 'checklist'] as $partKey) {
        $claims = array_values(array_filter(
            $section[$partKey] ?? [],
            fn($c) => ($c['confidence'] ?? 'official') !== 'hold'
        ));
        if (!$claims) {
            continue;
        }
        [$partHtml, $usedSourceIds] = guidance_part_html($partKey, $claims, $usedSourceIds);
        $parts .= $partHtml;
    }

    $tableHtml = !empty($section['comparison_table']) ? guidance_table_html($section['comparison_table']) : '';

    if ($parts === '' && $tableHtml === '') {
        return ['', $usedSourceIds];
    }

    $note = !empty($section['note']) ? '<p class="hint">' . h($section['note']) . '</p>' : '';

    $html = '<section id="gs-' . h($section['id']) . '" class="guidance-section"><h2>' . h($section['title']) . '</h2>'
        . $note . $tableHtml . $parts . '</section>';
    return [$html, $usedSourceIds];
}

function guidance_sources_box_html(array $usedSourceIds, array $sourcesById): string
{
    $typeLabels = [
        'official' => 'Official',
        'chamber' => 'Chamber of commerce',
        'government_portal' => 'Government portal',
        'secondary' => 'Secondary (advisor or company blog)',
    ];
    $ids = array_keys($usedSourceIds);
    usort($ids, function ($a, $b) {
        return (int)substr($a, 1) <=> (int)substr($b, 1);
    });
    if (!$ids) {
        return '';
    }
    $items = '';
    $n = 0;
    foreach ($ids as $id) {
        if (!isset($sourcesById[$id])) {
            continue;
        }
        $s = $sourcesById[$id];
        $n++;
        $items .= '<li id="source-' . h($id) . '">' . $n . '. <a href="' . h($s['url']) . '" target="_blank" rel="noopener">' . h($s['title']) . '</a>'
            . ' — ' . h($s['publisher'])
            . ' <span class="chip">' . h($typeLabels[$s['type']] ?? $s['type']) . '</span>'
            . ' <span class="muted" style="font-size:12px">(' . h(strtoupper($s['language'])) . ', accessed ' . h(guidance_format_date_long($s['accessed'])) . ')</span></li>';
    }
    return '<div class="panel guidance-sources-box"><h2>Sources</h2><ol class="guidance-sources-list">' . $items . '</ol></div>';
}

function guidance_faq_short_disclaimer_html(string $lastChecked): string
{
    return '<p class="hint">General information, not advice. Last checked ' . h(guidance_format_date_long($lastChecked)) . '.</p>';
}

function guidance_faq_entry_html(array $entry, array $sourcesById): string
{
    $refs = guidance_source_ref_html($entry['source_ids'] ?? []);
    $attribution = ($entry['status'] ?? '') === 'render_with_attribution'
        ? ' <span class="chip guidance-secondary-tag" style="color:#9a3412;border-color:#9a3412">Secondary source</span>'
        : '';
    $sourcesHtml = '';
    foreach ($entry['source_ids'] ?? [] as $id) {
        if (isset($sourcesById[$id])) {
            $sourcesHtml .= '<li><a href="' . h($sourcesById[$id]['url']) . '" target="_blank" rel="noopener">' . h($sourcesById[$id]['title']) . '</a></li>';
        }
    }
    return '<details class="guidance-faq-entry" id="faq-' . h($entry['id']) . '">'
        . '<summary>' . h($entry['question']) . '</summary>'
        . '<p>' . h($entry['answer']) . $refs . $attribution . '</p>'
        . ($sourcesHtml ? '<ul class="guidance-sources-list">' . $sourcesHtml . '</ul>' : '')
        . guidance_faq_short_disclaimer_html($entry['last_checked'])
        . guidance_feedback_widget_html('faq', $entry['id'])
        . '</details>';
}

/** Compact form for the hub's FAQ preview and a guide's "related" FAQ list. */
function guidance_faq_preview_item_html(array $entry): string
{
    return '<li><a href="guidance-faq.php?country=' . rawurlencode($entry['country']) . '#faq-' . rawurlencode($entry['id']) . '">' . h($entry['question']) . '</a></li>';
}

function guidance_feedback_widget_html(string $targetType, string $targetId): string
{
    return '<div class="guidance-feedback" data-target-type="' . h($targetType) . '" data-target-id="' . h($targetId) . '">'
        . '<span class="guidance-feedback-question">Was this information useful?</span>'
        . '<button type="button" class="guidance-vote-btn" data-vote="up" aria-label="Yes, this was useful">👍 Yes</button>'
        . '<button type="button" class="guidance-vote-btn" data-vote="down" aria-label="No, this was not useful">👎 No</button>'
        . '<span class="guidance-feedback-result" aria-live="polite"></span>'
        . '<div class="guidance-feedback-comment" hidden>'
        . '<label for="fb-comment-' . h($targetType) . '-' . h($targetId) . '">What was wrong or missing? (optional)</label>'
        . '<textarea id="fb-comment-' . h($targetType) . '-' . h($targetId) . '" maxlength="1000" rows="3"></textarea>'
        . '<div class="guidance-feedback-char-count"></div>'
        . '<input type="text" class="guidance-feedback-website" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">'
        . '<button type="button" class="pill light guidance-feedback-send">Send</button>'
        . '<button type="button" class="tertiary-button guidance-feedback-skip">Skip</button>'
        . '</div>'
        . '<a class="hint guidance-feedback-report" href="mailto:hello@keralafounders.eu?subject=Error%20report">Report an error (please include the source)</a>'
        . '</div>';
}
