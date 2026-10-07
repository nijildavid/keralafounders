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

if ($id > 0 && $action === 'approve') {
    $db = get_db();
    // Only a pending listing that came through the Add-your-company form (it stores ip_hash;
    // imported listings do not) gets the "you are live" email. Re-clicking Approve never re-sends.
    $beforeStmt = $db->prepare('SELECT slug, name, status, ip_hash FROM companies WHERE id = ?');
    $beforeStmt->execute([$id]);
    $before = $beforeStmt->fetch(PDO::FETCH_ASSOC);
    $upd = $db->prepare("UPDATE companies SET status = 'approved' WHERE id = ? AND status <> 'approved'");
    $upd->execute([$id]);
    if ($before && $before['status'] === 'pending' && $upd->rowCount() === 1
        && $before['ip_hash'] !== null && $before['ip_hash'] !== '') {
        $fStmt = $db->prepare("SELECT email FROM founders WHERE company_id = ? AND email IS NOT NULL AND email <> ''");
        $fStmt->execute([$id]);
        notify_listing_live((string)$before['name'], (string)$before['slug'], $fStmt->fetchAll(PDO::FETCH_COLUMN));
    }
} elseif ($id > 0 && $action === 'delete') {
    // Typed-name confirmation is enforced here, not just in the browser.
    $nameStmt = get_db()->prepare('SELECT name FROM companies WHERE id = ?');
    $nameStmt->execute([$id]);
    $companyName = $nameStmt->fetchColumn();
    if ($companyName !== false && trim((string)($_POST['confirm_name'] ?? '')) === $companyName) {
        get_db()->prepare('DELETE FROM companies WHERE id = ?')->execute([$id]);
    }
} elseif ($id > 0 && $action === 'toggle-verified') {
    get_db()->prepare('UPDATE companies SET verified = NOT verified WHERE id = ?')->execute([$id]);
}

$redirect = (string)($_POST['redirect'] ?? '');
if (strpos($redirect, 'admin.php') !== 0) {
    $redirect = 'admin.php';
}

header('Location: ../' . $redirect);

function notify_listing_live(string $name, string $slug, array $emails): void
{
    $url = 'https://keralafounders.eu/company.php?id=' . rawurlencode($slug);
    $subject = "You are live on Kerala Founders: $name";
    $body = "Hello,\n\n"
        . "Thank you for adding $name to Kerala Founders. Your listing has been reviewed and is now live:\n\n"
        . "$url\n\n"
        . "Please have a look. If anything needs correcting, open the page and use \"Claim this listing\" or \"Suggest an edit\".\n\n"
        . "It would help us a lot if you shared the link with your own network (WhatsApp works well) and followed us on Instagram: https://www.instagram.com/keralafounders.eu/\n\n"
        . "You are receiving this one email because you submitted this company on keralafounders.eu. "
        . "If you did not, or want the listing removed, just reply to this email.\n\n"
        . "Kerala Founders\nhttps://keralafounders.eu\n";
    $headers = "From: Kerala Founders <hello@keralafounders.eu>\r\n"
        . "Reply-To: hello@keralafounders.eu\r\n"
        . "Content-Type: text/plain; charset=UTF-8";
    foreach (array_unique($emails) as $to) {
        $to = trim((string)$to);
        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            @mail($to, $subject, $body, $headers);
        }
    }
}
