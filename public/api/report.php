<?php
declare(strict_types=1);

// Read-only JSON reporting API for pulling data outside the admin UI (e.g.
// building outreach lists). Authenticated with a long bearer token instead
// of the admin session, so it can be called from a script rather than a
// logged-in browser.
//
// Every report below is a fixed, hardcoded SELECT defined in this file —
// there is no way to pass arbitrary SQL or table/column names through this
// endpoint, and it never executes INSERT/UPDATE/DELETE. Adding a new report
// means adding a new entry to $reports here, not opening up free-form query
// access.

require __DIR__ . '/../../config/report-auth.php';
require __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

function report_deny(int $status, string $error): void
{
    http_response_code($status);
    echo json_encode(['ok' => false, 'error' => $error]);
    exit;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m) || !hash_equals(REPORT_API_TOKEN, $m[1])) {
    report_deny(401, 'Unauthorized');
}

$db = get_db();

$reports = [
    'email_counts' => function (PDO $db): array {
        return [
            'company_emails' => (int)$db->query(
                "SELECT COUNT(*) FROM companies WHERE contact_email IS NOT NULL AND TRIM(contact_email) <> ''"
            )->fetchColumn(),
            'distinct_company_emails' => (int)$db->query(
                "SELECT COUNT(DISTINCT contact_email) FROM companies WHERE contact_email IS NOT NULL AND TRIM(contact_email) <> ''"
            )->fetchColumn(),
            'founder_emails' => (int)$db->query(
                "SELECT COUNT(*) FROM founders WHERE email IS NOT NULL AND TRIM(email) <> ''"
            )->fetchColumn(),
            'distinct_founder_emails' => (int)$db->query(
                "SELECT COUNT(DISTINCT email) FROM founders WHERE email IS NOT NULL AND TRIM(email) <> ''"
            )->fetchColumn(),
        ];
    },

    // One row per company to email about claiming their listing: the named
    // founder's own email where one exists, otherwise the company's general
    // contact email. Skips companies with an already-resolved claim request.
    'outreach_list' => function (PDO $db): array {
        $sql = "
            SELECT company_name, founder_name, recipient_email, claim_link FROM (
                SELECT
                    c.name AS company_name,
                    f.name AS founder_name,
                    f.email AS recipient_email,
                    CONCAT('https://keralafounders.eu/claim.php?id=', c.slug) AS claim_link,
                    c.id AS company_id
                FROM founders f
                JOIN companies c ON c.id = f.company_id
                WHERE f.email IS NOT NULL AND TRIM(f.email) <> ''
                  AND c.status = 'approved'

                UNION ALL

                SELECT
                    c.name AS company_name,
                    NULL AS founder_name,
                    c.contact_email AS recipient_email,
                    CONCAT('https://keralafounders.eu/claim.php?id=', c.slug) AS claim_link,
                    c.id AS company_id
                FROM companies c
                WHERE c.contact_email IS NOT NULL AND TRIM(c.contact_email) <> ''
                  AND c.status = 'approved'
                  AND NOT EXISTS (
                      SELECT 1 FROM founders f2
                      WHERE f2.company_id = c.id AND f2.email IS NOT NULL AND TRIM(f2.email) <> ''
                  )
            ) AS outreach_candidates
            WHERE NOT EXISTS (
                SELECT 1 FROM claim_requests cr
                WHERE cr.company_id = outreach_candidates.company_id AND cr.status = 'resolved'
            )
            ORDER BY company_name
        ";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    },

    // Companies with no contact_email and no founder email at all — the
    // enrichment worklist. Rows with a website already on file sort first,
    // since those just need someone to check the site's contact/Impressum
    // page; the rest need discovery (LinkedIn, business registry, directory,
    // community source) before an email can even be looked for.
    'missing_email' => function (PDO $db): array {
        $sql = "
            SELECT
                c.name AS company_name,
                c.slug AS slug,
                c.website AS website,
                c.city AS city,
                c.country AS country,
                c.industry AS industry,
                c.status AS status
            FROM companies c
            WHERE (c.contact_email IS NULL OR TRIM(c.contact_email) = '')
              AND NOT EXISTS (
                  SELECT 1 FROM founders f
                  WHERE f.company_id = c.id AND f.email IS NOT NULL AND TRIM(f.email) <> ''
              )
            ORDER BY (c.website IS NULL OR TRIM(c.website) = ''), c.name
        ";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    },
];

$reportName = (string)($_GET['report'] ?? '');
if (!isset($reports[$reportName])) {
    report_deny(404, 'Unknown report. Available: ' . implode(', ', array_keys($reports)));
}

echo json_encode(['ok' => true, 'report' => $reportName, 'data' => $reports[$reportName]($db)]);
