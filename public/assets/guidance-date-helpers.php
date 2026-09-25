<?php
// Date math shared by the Guidance pages and scripts/validate-guidance-content.php,
// so the "outdated" check and the 6-month re-check rule can never drift between
// what renders and what the validator checks.

/**
 * Adds $months to an ISO date (YYYY-MM-DD), clamping the day to the target
 * month's length. PHP's own string-based '+N months' arithmetic overflows
 * past month-end (e.g. 2026-08-31 + 6 months lands on 2027-03-03, not
 * 2027-02-28) — this clamps instead, matching how "6 months from a date"
 * is normally meant.
 */
function guidance_add_months_safe(string $isoDate, int $months): string
{
    [$y, $m, $d] = array_map('intval', explode('-', $isoDate));
    $m += $months;
    $y += intdiv($m - 1, 12);
    $m = (($m - 1) % 12) + 1;
    $lastDay = (int)date('t', mktime(0, 0, 0, $m, 1, $y));
    return sprintf('%04d-%02d-%02d', $y, $m, min($d, $lastDay));
}

function guidance_is_outdated(string $nextCheckDueIso, ?DateTimeImmutable $now = null): bool
{
    $now = $now ?? new DateTimeImmutable('today');
    return $now > new DateTimeImmutable($nextCheckDueIso);
}

/** '2026-09-21' -> '21 September 2026' */
function guidance_format_date_long(string $isoDate): string
{
    $d = DateTimeImmutable::createFromFormat('Y-m-d', $isoDate);
    return $d ? $d->format('j F Y') : $isoDate;
}
