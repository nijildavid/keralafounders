<?php
// Share kit badge for verified companies: ?id=<slug>&type=badge (SVG).
// Only approved AND verified companies qualify.
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/render-helpers.php';

$slug = (string)($_GET['id'] ?? '');
$type = (string)($_GET['type'] ?? '');
$stmt = get_db()->prepare("SELECT name FROM companies WHERE slug = ? AND status = 'approved' AND verified = 1");
$stmt->execute([$slug]);
$c = $stmt->fetch();
if (!$c || $type !== 'badge') {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Not found');
}
header('X-Robots-Tag: noindex');
header('Content-Type: image/svg+xml; charset=utf-8');
echo '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="56" viewBox="0 0 220 56" role="img" aria-label="Featured on Kerala Founders">'
    . '<rect x="0.5" y="0.5" width="219" height="55" rx="8" fill="#f8faf7" stroke="#d1d5db"/>'
    . '<circle cx="28" cy="28" r="12" fill="#0f5132"/><path d="M22 28l4 4 8-9" fill="none" stroke="#fff" stroke-width="3"/>'
    . '<text x="50" y="24" font-family="Inter,Arial,sans-serif" font-size="12" fill="#6b7280">Featured on</text>'
    . '<text x="50" y="42" font-family="Inter,Arial,sans-serif" font-size="16" font-weight="bold" fill="#1f2937">Kerala Founders</text></svg>';
