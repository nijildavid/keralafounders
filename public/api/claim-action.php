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
            'UPDATE companies SET name = ?, website = ?, industry = ?, business_type = ?, industry_detail = ?, size = ?, founded_year = ?, country = ?, city = ?, location = ?, description = ?, verified = 1, owner_confirmed = 1 WHERE id = ?'
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

        // The claim form never receives a hidden email or a LinkedIn URL (see
        // claim.php), so a blank value there means "keep what is on file", not
        // "delete it". Load the current founders before they are replaced and
        // match each proposed founder by name, or by position when the number
        // of founders is unchanged and the name was edited.
        $currentStmt = $db->prepare('SELECT name, email, linkedin, show_email FROM founders WHERE company_id = ? ORDER BY id');
        $currentStmt->execute([$companyId]);
        $currentFounders = $currentStmt->fetchAll();
        $currentByName = [];
        foreach ($currentFounders as $cf) {
            $currentByName[mb_strtolower(trim((string)$cf['name']))] = $cf;
        }
        $proposedFounders = array_values(array_filter(
            is_array($proposed['founders'] ?? null) ? $proposed['founders'] : [],
            fn($f) => trim((string)($f['name'] ?? '')) !== ''
        ));
        $samePositions = count($proposedFounders) === count($currentFounders);

        $db->prepare('DELETE FROM founders WHERE company_id = ?')->execute([$companyId]);
        $founderStmt = $db->prepare('INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES (?, ?, ?, ?, ?)');
        foreach ($proposedFounders as $idx => $f) {
            $fname = trim((string)($f['name'] ?? ''));
            $email = trim((string)($f['email'] ?? ''));
            $linkedin = trim((string)($f['linkedin'] ?? ''));
            $showEmail = !empty($f['showEmail']) ? 1 : 0;

            $existing = $currentByName[mb_strtolower($fname)] ?? ($samePositions ? $currentFounders[$idx] : null);
            if ($existing) {
                // A blank LinkedIn keeps the one on file (no public page shows it).
                if ($linkedin === '' && trim((string)$existing['linkedin']) !== '') {
                    $linkedin = trim((string)$existing['linkedin']);
                }
                // A blank email keeps the one on file only when it was hidden,
                // which is the case the form could not show. A visible email
                // that was cleared on purpose is still removed. A kept hidden
                // email stays hidden, even if the claimant ticked "show".
                if ($email === '' && empty($existing['show_email']) && trim((string)$existing['email']) !== '') {
                    $email = trim((string)$existing['email']);
                    $showEmail = 0;
                }
            }

            $founderStmt->execute([
                $companyId,
                $fname,
                $email ?: null,
                $linkedin ?: null,
                $showEmail,
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
