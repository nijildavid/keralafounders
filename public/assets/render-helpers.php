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

/**
 * Trust tier for a company row (needs verified, contact_email, email_source_url,
 * instagram, instagram_confidence). Mirrored by the `tier` field sent to app.js.
 *  - owner:       the owner claimed the listing and an admin applied it (owner_confirmed = 1)
 *  - verified:    checked by Kerala Founders (verified = 1 without an owner claim)
 *  - confirmed:   we found a working channel on the company's own pages
 *                 (email with a saved source URL, or a high-confidence Instagram)
 *  - unconfirmed: listed, no sourced contact point yet
 */
function company_contact_tier(array $row): string
{
    if (!empty($row['verified'])) {
        return !empty($row['owner_confirmed']) ? 'owner' : 'verified';
    }
    $hasEmail = trim((string)($row['contact_email'] ?? '')) !== '' && trim((string)($row['email_source_url'] ?? '')) !== '';
    $hasInstagram = trim((string)($row['instagram'] ?? '')) !== '' && ($row['instagram_confidence'] ?? '') === 'high';
    return ($hasEmail || $hasInstagram) ? 'confirmed' : 'unconfirmed';
}

/**
 * One verification badge for every page (cards and detail). Kasavu-gold seal for
 * the two verified tiers, quiet neutral chip for the rest. Mirrors KFUI.verifiedChip()
 * in assets/app.js exactly. $tier is 'owner' | 'verified' | 'confirmed' | 'unconfirmed';
 * a bool is still accepted (true = verified). $size is '' or 'lg' (detail page).
 */
function verified_chip_html($tier, string $size = ''): string
{
    if (is_bool($tier)) {
        $tier = $tier ? 'verified' : 'unconfirmed';
    }
    $seal = '<svg class="vbadge-seal" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path class="vbadge-seal-rim" d="M12.00 1.00 L13.83 2.78 L16.21 1.84 L17.22 4.18 L19.78 4.22 L19.82 6.78 L22.16 7.79 L21.22 10.17 L23.00 12.00 L21.22 13.83 L22.16 16.21 L19.82 17.22 L19.78 19.78 L17.22 19.82 L16.21 22.16 L13.83 21.22 L12.00 23.00 L10.17 21.22 L7.79 22.16 L6.78 19.82 L4.22 19.78 L4.18 17.22 L1.84 16.21 L2.78 13.83 L1.00 12.00 L2.78 10.17 L1.84 7.79 L4.18 6.78 L4.22 4.22 L6.78 4.18 L7.79 1.84 L10.17 2.78Z"/><circle class="vbadge-seal-ring" cx="12" cy="12" r="7.2"/><path class="vbadge-seal-check" d="M8.4 12.3l2.5 2.5 4.7-5.1"/></svg>';
    $cls = $size === 'lg' ? ' vbadge--lg' : '';
    if ($tier === 'owner') {
        return '<span class="vbadge vbadge--owner' . $cls . '" title="The owner of this business claimed and confirmed this listing.">' . $seal . 'Owner confirmed</span>';
    }
    if ($tier === 'verified') {
        return '<span class="vbadge vbadge--verified' . $cls . '" title="Checked by Kerala Founders. The owner has not confirmed the listing yet.">' . $seal . 'Verified</span>';
    }
    if ($tier === 'confirmed') {
        return '<span class="vbadge vbadge--plain' . $cls . '" title="We found a working contact on this company\'s own website or profile. The owner has not verified the listing yet.">Contact confirmed</span>';
    }
    return '<span class="vbadge vbadge--none' . $cls . '" title="If you own this company, email hello@keralafounders.eu to get verified.">Not yet verified</span>';
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
        . verified_chip_html($c['tier'] ?? (!empty($c['verified']) ? 'verified' : 'unconfirmed')) . '</div>'
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
 * Same as fetch_approved_companies(), but does the pagination in SQL
 * (COUNT + LIMIT/OFFSET) instead of fetching every approved row and
 * array_slice()-ing down to one page — founders.php/countries.php only
 * ever display $pageSize rows, so there's no reason to pull (and run
 * founders queries for) every other approved company on every pageview.
 *
 * Returns ['companies' => ..., 'total' => int, 'page' => int (clamped into
 * range), 'totalPages' => int].
 */
function fetch_approved_companies_page(PDO $db, int $page, int $pageSize, ?string $country = null): array
{
    $where = "status = 'approved'";
    $params = [];
    if ($country !== null) {
        $where .= ' AND country = ?';
        $params[] = $country;
    }

    $countStmt = $db->prepare("SELECT COUNT(*) FROM companies WHERE $where");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $totalPages = max(1, (int)ceil($total / $pageSize));
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    if ($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $pageSize;

    $stmt = $db->prepare("SELECT * FROM companies WHERE $where ORDER BY name LIMIT ? OFFSET ?");
    $i = 1;
    foreach ($params as $param) {
        $stmt->bindValue($i++, $param);
    }
    $stmt->bindValue($i++, $pageSize, PDO::PARAM_INT);
    $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'companies' => companies_with_founders($db, $stmt->fetchAll()),
        'total' => $total,
        'page' => $page,
        'totalPages' => $totalPages,
    ];
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
    $foundersByCompany = fetch_founders_by_company_id($db, array_column($rows, 'id'));

    $companies = [];
    foreach ($rows as $row) {
        $companies[] = [
            'id' => $row['slug'],
            'name' => $row['name'],
            'founders' => $foundersByCompany[$row['id']] ?? [],
            'country' => $row['country'],
            'city' => $row['city'],
            'industry' => $row['industry'],
            'business_type' => $row['business_type'],
            'industry_detail' => $row['industry_detail'],
            'size' => $row['size'],
            'verified' => (bool)$row['verified'],
            'tier' => company_contact_tier($row),
        ];
    }
    return $companies;
}

/**
 * One query for every company's founder names, keyed by company_id, instead
 * of one query per company — avoids the N+1 pattern as the directory grows.
 */
function fetch_founders_by_company_id(PDO $db, array $companyIds): array
{
    if (!$companyIds) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($companyIds), '?'));
    $stmt = $db->prepare("SELECT company_id, name FROM founders WHERE company_id IN ($placeholders) ORDER BY id");
    $stmt->execute($companyIds);
    $byCompany = [];
    foreach ($stmt->fetchAll() as $f) {
        $byCompany[$f['company_id']][] = $f['name'];
    }
    return $byCompany;
}
