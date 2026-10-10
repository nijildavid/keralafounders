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
        . '<rect x="0.5" y="0.5" width="219" height="55" rx="8" fill="#f8faf7" stroke="#d1d5db"/>'
        . '<circle cx="28" cy="28" r="12" fill="#0f5132"/><path d="M22 28l4 4 8-9" fill="none" stroke="#fff" stroke-width="3"/>'
        . '<text x="50" y="24" font-family="Inter,Arial,sans-serif" font-size="12" fill="#6b7280">Featured on</text>'
        . '<text x="50" y="42" font-family="Inter,Arial,sans-serif" font-size="16" font-weight="bold" fill="#1f2937">Kerala Founders</text></svg>';
    exit;
}

// Site style: Inter (bundled so it looks the same on any server), cream background, dark ink text, green link.
$fontBold = null;
foreach ([__DIR__ . '/assets/fonts/Inter-Bold.otf', '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
          '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf', '/usr/share/fonts/liberation/LiberationSans-Bold.ttf'] as $f) {
    if (is_file($f)) { $fontBold = $f; break; }
}
$fontMed = is_file(__DIR__ . '/assets/fonts/Inter-Medium.otf') ? __DIR__ . '/assets/fonts/Inter-Medium.otf' : $fontBold;
if (!function_exists('imagettftext') || $fontBold === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Image not available');
}

$w = 1080;
$h = $type === 'story' ? 1920 : 1080;
$im = imagecreatetruecolor($w, $h);
$cream = imagecolorallocate($im, 248, 250, 247);   // --cream
$ink = imagecolorallocate($im, 31, 41, 55);        // --ink
$muted = imagecolorallocate($im, 107, 114, 128);   // --muted
$green = imagecolorallocate($im, 15, 81, 50);      // --primary
imagefilledrectangle($im, 0, 0, $w, $h, $cream);

// Draw text centred, wrapping to the width of the card. Returns the next free y.
$draw = function (string $text, int $size, $color, int $y, string $font) use ($im, $w) {
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
        $y += (int)($size * 1.4);
    }
    return $y;
};
// Stories keep text clear of Instagram's top and bottom overlays (about 250px / 340px).
$top = (int)($h / 2) - 230;
$y = $draw('Featured on', 36, $muted, $top, $fontMed);
$y = $draw('Kerala Founders', 60, $ink, $y + 44, $fontBold);
$nameSize = mb_strlen($c['name']) > 24 ? 54 : 72;   // long names get a smaller size so they never reach the link
$y = $draw($c['name'], $nameSize, $ink, $y + 90, $fontBold);
if ($place !== '') { $y = $draw($place, 38, $muted, $y + 16, $fontMed); }
$draw('keralafounders.eu', 40, $green, $h - ($type === 'story' ? 360 : 120), $fontBold);

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
imagepng($im);
