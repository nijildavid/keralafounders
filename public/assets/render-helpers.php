<?php
// Shared server-side rendering helpers, mirroring assets/app.js (KFUI) so
// PHP-rendered markup matches what the client-side JS would render.

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}

/**
 * $trail is an ordered list of ['name' => ..., 'url' => absolute URL], from
 * Home down to the current page. Renders a schema.org BreadcrumbList so
 * Google can show the breadcrumb path (Home > Countries > Germany, etc.)
 * under a search result instead of just the page title.
 */
function breadcrumb_json_ld(array $trail): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(fn($item, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'],
        ], $trail, array_keys($trail)),
    ];
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

function fetch_approved_companies_by_industry(PDO $db, string $industry): array
{
    $stmt = $db->prepare("SELECT * FROM companies WHERE status = 'approved' AND industry = ? ORDER BY name");
    $stmt->execute([$industry]);
    return companies_with_founders($db, $stmt->fetchAll());
}

function fetch_approved_companies_by_business_type(PDO $db, string $businessType): array
{
    $stmt = $db->prepare("SELECT * FROM companies WHERE status = 'approved' AND business_type = ? ORDER BY name");
    $stmt->execute([$businessType]);
    return companies_with_founders($db, $stmt->fetchAll());
}

function fetch_approved_companies_by_city(PDO $db, string $city): array
{
    $stmt = $db->prepare("SELECT * FROM companies WHERE status = 'approved' AND city = ? ORDER BY name");
    $stmt->execute([$city]);
    return companies_with_founders($db, $stmt->fetchAll());
}

/**
 * Real country => company-count pairs (no zero-fill against $KF_COUNTRIES —
 * companies.country is free text and can include countries not in that
 * whitelist), sorted by count descending, for the footer's country list.
 */
function fetch_top_countries(PDO $db, int $limit): array
{
    $stmt = $db->prepare(
        "SELECT country, COUNT(*) AS n FROM companies WHERE status = 'approved' GROUP BY country ORDER BY n DESC LIMIT ?"
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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

/**
 * Builds a pagination link URL. $extraParams carries the page's own filter
 * (e.g. ['country' => 'Germany']) so links preserve the selected category.
 * The 'page' param is only added when $p > 1, matching the site's convention
 * of a bare/param-less URL for page 1.
 */
function ssr_page_href(string $baseUrl, array $extraParams, int $p): string
{
    $params = $extraParams;
    if ($p > 1) {
        $params['page'] = $p;
    }
    if (!$params) {
        return $baseUrl;
    }
    $pairs = [];
    foreach ($params as $key => $value) {
        $pairs[] = rawurlencode((string)$key) . '=' . rawurlencode((string)$value);
    }
    return $baseUrl . '?' . implode('&', $pairs);
}

function ssr_page_numbers(int $current, int $total): array
{
    if ($total <= 7) {
        return range(1, $total);
    }
    $out = [1];
    if ($current > 3) {
        $out[] = '...';
    }
    for ($i = max(2, $current - 1); $i <= min($total - 1, $current + 1); $i++) {
        $out[] = $i;
    }
    if ($current < $total - 2) {
        $out[] = '...';
    }
    $out[] = $total;
    return $out;
}

function ssr_pagination_html(string $baseUrl, array $extraParams, int $page, int $totalPages): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $html = '<nav class="pagination" aria-label="Directory pages">';
    $prevDisabled = $page <= 1;
    $html .= '<a href="' . h(ssr_page_href($baseUrl, $extraParams, $page - 1)) . '" class="page-btn prev' . ($prevDisabled ? ' disabled' : '') . '"' . ($prevDisabled ? ' aria-disabled="true" tabindex="-1"' : '') . '>&larr; Prev</a>';
    foreach (ssr_page_numbers($page, $totalPages) as $n) {
        if ($n === '...') {
            $html .= '<span class="page-ellipsis">&hellip;</span>';
        } else {
            $active = $n === $page;
            $html .= '<a href="' . h(ssr_page_href($baseUrl, $extraParams, $n)) . '" class="page-btn' . ($active ? ' active' : '') . '"' . ($active ? ' aria-current="page"' : '') . '>' . $n . '</a>';
        }
    }
    $nextDisabled = $page >= $totalPages;
    $html .= '<a href="' . h(ssr_page_href($baseUrl, $extraParams, $page + 1)) . '" class="page-btn next' . ($nextDisabled ? ' disabled' : '') . '"' . ($nextDisabled ? ' aria-disabled="true" tabindex="-1"' : '') . '>Next &rarr;</a>';
    $html .= '</nav>';
    return $html;
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
