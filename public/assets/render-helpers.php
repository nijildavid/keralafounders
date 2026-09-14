<?php
// Shared server-side rendering helpers, mirroring assets/app.js (KFUI) so
// PHP-rendered markup matches what the client-side JS would render.

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

function truncate_meta(string $s, int $len = 160): string
{
    $s = trim($s);
    return mb_strlen($s) <= $len ? $s : mb_substr($s, 0, $len - 1) . '…';
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = array_map(fn($p) => mb_substr($p, 0, 1), array_slice($parts, 0, 2));
    $result = mb_strtoupper(implode('', $letters));
    return $result !== '' ? $result : 'KF';
}

function verified_chip_html(bool $v): string
{
    return $v
        ? '<span class="chip" style="color:var(--accent2);border-color:var(--accent2)">Verified</span>'
        : '<span class="chip" style="color:#9a3412;border-color:#9a3412" title="If you own this company, email hello@keralafounders.eu to get verified.">Not yet verified</span>';
}

/** Large, high-visibility verified badge for the company detail page. */
function verified_badge_strong_html(): string
{
    return '<span class="verified-badge-strong"><span class="verified-badge-check" aria-hidden="true">&#10003;</span>Verified</span>';
}

/**
 * $c must have: id (slug), name, founders (array of names), country, industry, verified (bool)
 * Mirrors KFUI.companyCard() in assets/app.js exactly.
 */
function company_card_html(array $c): string
{
    $founders = h(implode(', ', $c['founders'] ?? []));
    return '<a class="company-card company-link" href="company.php?id=' . rawurlencode($c['id']) . '">'
        . '<div class="logo">' . h(initials($c['name'])) . '</div>'
        . '<div class="company-main"><h3>' . h($c['name']) . '</h3><div class="meta">' . $founders . '</div></div>'
        . '<div class="chips"><span class="chip">' . h($c['country']) . '</span><span class="chip">' . h($c['industry']) . '</span>'
        . (!empty($c['business_type']) ? '<span class="chip">' . h($c['business_type']) . '</span>' : '')
        . verified_chip_html((bool)$c['verified']) . '</div>'
        . '</a>';
}

/**
 * Fetch approved companies (optionally filtered by country), each with its founders array,
 * sorted by name to match the JS default sort (rows.sort((a,b)=>a.name.localeCompare(b.name))).
 */
function fetch_approved_companies(PDO $db, ?string $country = null): array
{
    if ($country !== null) {
        $stmt = $db->prepare("SELECT * FROM companies WHERE status = 'approved' AND country = ? ORDER BY name");
        $stmt->execute([$country]);
    } else {
        $stmt = $db->query("SELECT * FROM companies WHERE status = 'approved' ORDER BY name");
    }
    return companies_with_founders($db, $stmt->fetchAll());
}

/**
 * Most-recently-added approved companies, matching data.php's default order.
 * Batch imports share one created_at per batch, so id DESC breaks ties deterministically
 * (higher id = added later) — without it, MySQL's tie order is undefined and can
 * disagree between this SSR query and data.php's, or change from load to load.
 */
function fetch_recent_approved_companies(PDO $db, int $limit): array
{
    $stmt = $db->prepare("SELECT * FROM companies WHERE status = 'approved' ORDER BY created_at DESC, id DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return companies_with_founders($db, $stmt->fetchAll());
}

function companies_with_founders(PDO $db, array $rows): array
{
    $companies = [];
    foreach ($rows as $row) {
        $fs = $db->prepare('SELECT name FROM founders WHERE company_id = ? ORDER BY id');
        $fs->execute([$row['id']]);
        $companies[] = [
            'id' => $row['slug'],
            'name' => $row['name'],
            'founders' => $fs->fetchAll(PDO::FETCH_COLUMN),
            'country' => $row['country'],
            'city' => $row['city'],
            'industry' => $row['industry'],
            'business_type' => $row['business_type'],
            'industry_detail' => $row['industry_detail'],
            'size' => $row['size'],
            'verified' => (bool)$row['verified'],
        ];
    }
    return $companies;
}
