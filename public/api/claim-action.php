<?php
require __DIR__ . '/../../config/auth.php';
require_admin();
require __DIR__ . '/../../config/db.php';

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid or expired form. Please refresh the page and try again.');
}

$id = (int)($_POST['id'] ?? 0);
$action = (string)($_POST['action'] ?? '');
$db = get_db();

if ($id > 0 && $action === 'resolve') {
    $db->prepare("UPDATE claim_requests SET status = 'resolved' WHERE id = ?")->execute([$id]);
} elseif ($id > 0 && $action === 'dismiss') {
    $db->prepare("UPDATE claim_requests SET status = 'dismissed' WHERE id = ?")->execute([$id]);
} elseif ($id > 0 && $action === 'apply') {
    $stmt = $db->prepare('SELECT company_id, proposed_changes FROM claim_requests WHERE id = ?');
    $stmt->execute([$id]);
    $claim = $stmt->fetch();
    $proposed = $claim && $claim['proposed_changes'] ? json_decode($claim['proposed_changes'], true) : null;

    if ($claim && is_array($proposed)) {
        $companyId = (int)$claim['company_id'];

        $db->beginTransaction();

        $db->prepare(
            'UPDATE companies SET name = ?, website = ?, industry = ?, business_type = ?, industry_detail = ?, size = ?, founded_year = ?, country = ?, city = ?, location = ?, description = ?, verified = 1 WHERE id = ?'
        )->execute([
            trim((string)($proposed['company'] ?? '')),
            trim((string)($proposed['website'] ?? '')) ?: null,
            trim((string)($proposed['industry'] ?? '')),
            trim((string)($proposed['businessType'] ?? '')),
            trim((string)($proposed['industryDetail'] ?? '')) ?: null,
            trim((string)($proposed['size'] ?? '')) ?: null,
            !empty($proposed['founded']) ? (int)$proposed['founded'] : null,
            trim((string)($proposed['country'] ?? '')),
            trim((string)($proposed['city'] ?? '')),
            trim((string)($proposed['location'] ?? '')),
            trim((string)($proposed['description'] ?? '')),
            $companyId,
        ]);

        $db->prepare('DELETE FROM founders WHERE company_id = ?')->execute([$companyId]);
        $founderStmt = $db->prepare('INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES (?, ?, ?, ?, ?)');
        foreach (($proposed['founders'] ?? []) as $f) {
            $fname = trim((string)($f['name'] ?? ''));
            if ($fname === '') {
                continue;
            }
            $founderStmt->execute([
                $companyId,
                $fname,
                trim((string)($f['email'] ?? '')) ?: null,
                trim((string)($f['linkedin'] ?? '')) ?: null,
                !empty($f['showEmail']) ? 1 : 0,
            ]);
        }

        $db->prepare('DELETE FROM branches WHERE company_id = ?')->execute([$companyId]);
        $branchStmt = $db->prepare('INSERT INTO branches (company_id, country) VALUES (?, ?)');
        foreach (($proposed['branches'] ?? []) as $b) {
            $b = trim((string)$b);
            if ($b !== '') {
                $branchStmt->execute([$companyId, $b]);
            }
        }

        $db->prepare("UPDATE claim_requests SET status = 'resolved' WHERE id = ?")->execute([$id]);

        $db->commit();
    }
}

header('Location: ../admin-claims.php');
