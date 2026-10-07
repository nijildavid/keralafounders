<?php
// Share kit images for verified companies: ?id=<slug>&type=badge (SVG) | square | story (PNG).
// Only approved AND verified companies qualify. No logo file is used (logo.svg is managed by hand).
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/render-helpers.php';

$slug = (string)($_GET['id'] ?? '');
$type = (string)($_GET['type'] ?? '');
$stmt = get_db()->prepare("SELECT name, city, country FROM companies WHERE slug = ? AND status = 'approved' AND verified = 1");
$stmt->execute([$slug]);
$c = $stmt->fetch();
if (!$c || !in_array($type, ['badge', 'square', 'story'], true)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Not found');
}
header('X-Robots-Tag: noindex');
$place = implode(', ', array_filter([$c['city'], $c['country']]));

if ($type === 'badge') {
    header('Content-Type: image/svg+xml; charset=utf-8');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="56" viewBox="0 0 220 56" role="img" aria-label="Featured on Kerala Founders">'
        . '<rect width="220" height="56" rx="8" fill="#0f5132"/>'
        . '<circle cx="28" cy="28" r="12" fill="#fff"/><path d="M22 28l4 4 8-9" fill="none" stroke="#0f5132" stroke-width="3"/>'
        . '<text x="50" y="24" font-family="Arial,sans-serif" font-size="12" fill="#cfe8da">Featured on</text>'
        . '<text x="50" y="42" font-family="Arial,sans-serif" font-size="16" font-weight="bold" fill="#fff">Kerala Founders</text></svg>';
    exit;
}

$font = null;
foreach (['/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
          '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf', '/usr/share/fonts/liberation/LiberationSans-Bold.ttf'] as $f) {
    if (is_file($f)) { $font = $f; break; }
}
if (!function_exists('imagettftext') || $font === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Image not available');
}

$w = 1080;
$h = $type === 'story' ? 1920 : 1080;
$im = imagecreatetruecolor($w, $h);
$green = imagecolorallocate($im, 15, 81, 50);
$white = imagecolorallocate($im, 255, 255, 255);
$soft = imagecolorallocate($im, 207, 232, 218);
imagefilledrectangle($im, 0, 0, $w, $h, $green);

// Draw text centred, wrapping to the width of the card.
$draw = function (string $text, int $size, $color, int $y) use ($im, $font, $w) {
    $lines = [];
    $line = '';
    foreach (explode(' ', $text) as $word) {
        $try = $line === '' ? $word : $line . ' ' . $word;
        $box = imagettfbbox($size, 0, $font, $try);
        if ($line !== '' && ($box[2] - $box[0]) > $w - 160) { $lines[] = $line; $line = $word; } else { $line = $try; }
    }
    $lines[] = $line;
    foreach ($lines as $l) {
        $box = imagettfbbox($size, 0, $font, $l);
        imagettftext($im, $size, 0, (int)(($w - ($box[2] - $box[0])) / 2), $y, $color, $font, $l);
        $y += (int)($size * 1.6);
    }
    return $y;
};
$top = (int)($h / 2) - 220;
$y = $draw('Featured on', 40, $soft, $top);
$y = $draw('Kerala Founders', 64, $white, $y + 10);
$y = $draw($c['name'], 72, $white, $y + 90);
if ($place !== '') { $y = $draw($place, 40, $soft, $y + 20); }
$draw('keralafounders.eu', 36, $soft, $h - 120);

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
imagepng($im);
