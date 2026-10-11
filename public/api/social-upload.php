<?php
declare(strict_types=1);

// Private upload door for the weekly Instagram/Buffer automation. Takes one
// PNG, JPEG or MP4 video plus a filename, stores it in public/social/ and
// answers with the permanent public URL Buffer needs. Authenticated with a long bearer
// token (config/social-upload-auth.php, outside the web root) — nothing
// about this endpoint is linked from the site.
//
//   curl -sS -X POST https://keralafounders.eu/api/social-upload.php \
//     -H "Authorization: Bearer <TOKEN>" \
//     -F "name=2026-10-05_01_country-spotlight_germany.png" \
//     -F "file=@/path/to/image.png"
//
//   Videos work the same way (MP4 only, up to 50 MB):
//     -F "name=2026-10-12_reel_germany.mp4" -F "file=@/path/to/reel.mp4"
//
// Files are never overwritten (a scheduled post may already point at one) and
// only real PNG/JPEG/MP4 bytes are accepted — checked by content, not extension.
// Buffer links to the file instead of copying it, so nothing is ever deleted.
//
// Server needs upload_max_filesize >= 50M and post_max_size >= 60M for videos
// (cPanel > MultiPHP INI Editor); below that the endpoint answers 413.

require __DIR__ . '/../../config/social-upload-auth.php';

const SOCIAL_DIR = __DIR__ . '/../social';
const SOCIAL_PUBLIC_BASE = 'https://keralafounders.eu/social/';
const SOCIAL_LOG_FILE = __DIR__ . '/../../config/social-upload.log';
const SOCIAL_RATE_FILE = __DIR__ . '/../../config/social-upload-rate.json';
const SOCIAL_MAX_BYTES = 8 * 1024 * 1024;
const SOCIAL_MAX_VIDEO_BYTES = 50 * 1024 * 1024;
const SOCIAL_MIN_SIDE = 200;
const SOCIAL_MAX_SIDE = 8000;
const SOCIAL_UPLOADS_PER_HOUR = 40;
const SOCIAL_BAD_AUTH_PER_HOUR = 10;

header('Content-Type: application/json');
header('Cache-Control: no-store');

function social_log(string $filename, int $bytes, string $result): void
{
    $line = gmdate('c') . "\t" . ($_SERVER['REMOTE_ADDR'] ?? '-') . "\t"
        . preg_replace('/[^\x20-\x7E]/', '?', $filename) . "\t" . $bytes . "\t" . $result . "\n";
    @file_put_contents(SOCIAL_LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

function social_respond(int $status, array $body, string $filename = '', int $bytes = 0): void
{
    social_log($filename, $bytes, $status . ' ' . ($body['error'] ?? 'ok'));
    http_response_code($status);
    echo json_encode($body);
    exit;
}

function social_fail(int $status, string $error, string $filename = '', int $bytes = 0): void
{
    social_respond($status, ['error' => $error], $filename, $bytes);
}

/**
 * Hit counter per bucket ('ip:<addr>' for bad logins, 'uploads' for everything
 * authenticated), kept in a small JSON file outside the web root. Returns
 * false once the bucket is over $limit for the last hour.
 */
function social_rate_allow(string $bucket, int $limit, bool $record): bool
{
    $fh = @fopen(SOCIAL_RATE_FILE, 'c+');
    if (!$fh) {
        return true; // best-effort, same as the login limiter
    }
    flock($fh, LOCK_EX);
    $data = json_decode((string)stream_get_contents($fh), true);
    $data = is_array($data) ? $data : [];
    $cutoff = time() - 3600;
    foreach ($data as $key => $stamps) {
        $data[$key] = array_values(array_filter((array)$stamps, function ($t) use ($cutoff) {
            return $t > $cutoff;
        }));
        if (!$data[$key]) {
            unset($data[$key]);
        }
    }
    $allowed = count($data[$bucket] ?? []) < $limit;
    if ($record) {
        $data[$bucket][] = time();
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($data));
    }
    flock($fh, LOCK_UN);
    fclose($fh);
    return $allowed;
}

/** Lowercase, spaces to hyphens, drop everything but a-z 0-9 _ - and one final .png/.jpg/.jpeg/.mp4. */
function social_clean_name(string $raw): ?string
{
    $raw = strtolower(trim(str_replace('\\', '/', $raw)));
    $raw = basename($raw); // no path parts, ever
    $raw = preg_replace('/\s+/', '-', $raw);
    $dot = strrpos($raw, '.');
    if ($dot === false) {
        return null;
    }
    $ext = substr($raw, $dot + 1);
    $base = preg_replace('/[^a-z0-9_-]/', '', substr($raw, 0, $dot));
    if (!in_array($ext, ['png', 'jpg', 'jpeg', 'mp4'], true) || $base === '' || strlen($base) > 120) {
        return null;
    }
    return $base . '.' . $ext;
}

/** Drops EXIF/XMP/comment segments from a JPEG without re-encoding (no quality loss). */
function social_strip_jpeg_metadata(string $data): string
{
    $len = strlen($data);
    $out = "\xFF\xD8";
    $pos = 2;
    while ($pos + 4 <= $len && $data[$pos] === "\xFF") {
        $marker = ord($data[$pos + 1]);
        if ($marker === 0xDA) { // start of scan: image data follows, copy the rest as-is
            break;
        }
        $segLen = (ord($data[$pos + 2]) << 8) | ord($data[$pos + 3]);
        if ($segLen < 2 || $pos + 2 + $segLen > $len) {
            return $data; // malformed: keep the original rather than corrupt it
        }
        $isMetadata = $marker === 0xE1 || $marker === 0xED || $marker === 0xFE;
        if (!$isMetadata) {
            $out .= substr($data, $pos, 2 + $segLen);
        }
        $pos += 2 + $segLen;
    }
    return $out . substr($data, $pos);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    social_fail(405, 'POST only');
}

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
if (!$https && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    social_fail(403, 'HTTPS only');
}

$ipBucket = 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (!social_rate_allow($ipBucket, SOCIAL_BAD_AUTH_PER_HOUR, false)) {
    social_fail(429, 'Too many failed attempts, try again later');
}
if (!preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m) || !hash_equals(SOCIAL_UPLOAD_TOKEN, trim($m[1]))) {
    social_rate_allow($ipBucket, SOCIAL_BAD_AUTH_PER_HOUR, true);
    social_fail(401, 'Unauthorized');
}

if (!social_rate_allow('uploads', SOCIAL_UPLOADS_PER_HOUR, true)) {
    social_fail(429, 'Upload limit reached (' . SOCIAL_UPLOADS_PER_HOUR . ' per hour), try again later');
}

// A request bigger than PHP's post_max_size arrives with $_POST/$_FILES empty.
$postMax = (int)ini_get('post_max_size');
$unit = strtoupper(substr(trim((string)ini_get('post_max_size')), -1));
$postMaxBytes = $postMax * ($unit === 'G' ? 1073741824 : ($unit === 'M' ? 1048576 : ($unit === 'K' ? 1024 : 1)));
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0 && $postMaxBytes > 0 && (int)$_SERVER['CONTENT_LENGTH'] > $postMaxBytes) {
    social_fail(413, 'File too large for this server (max ' . ini_get('post_max_size') . '; the host\'s PHP upload limit needs raising for videos)');
}

$rawName = (string)($_POST['name'] ?? '');
if ($rawName === '' && isset($_FILES['file']['name'])) {
    $rawName = (string)$_FILES['file']['name'];
}
$name = social_clean_name($rawName);
if ($name === null) {
    social_fail(400, 'Bad filename: use letters, digits, hyphens or underscores, ending in .png, .jpg, .jpeg or .mp4', $rawName);
}

$ext = substr($name, strrpos($name, '.') + 1);
$isVideo = $ext === 'mp4';
$maxBytes = $isVideo ? SOCIAL_MAX_VIDEO_BYTES : SOCIAL_MAX_BYTES;
$maxLabel = $isVideo ? '50 MB' : '8 MB';

if (!isset($_FILES['file'])) {
    social_fail(400, 'No file received (send it as multipart field "file")', $name);
}
$upErr = $_FILES['file']['error'];
if ($upErr === UPLOAD_ERR_INI_SIZE || $upErr === UPLOAD_ERR_FORM_SIZE) {
    social_fail(413, 'File too large (max ' . $maxLabel . ', or the host\'s PHP upload_max_filesize is lower than that)', $name);
}
if ($upErr !== UPLOAD_ERR_OK || !is_uploaded_file($_FILES['file']['tmp_name'])) {
    social_fail(400, 'Upload failed, please try again', $name);
}
$tmp = $_FILES['file']['tmp_name'];
$bytes = (int)filesize($tmp);
if ($bytes > $maxBytes) {
    social_fail(413, 'File too large (max ' . $maxLabel . ')', $name, $bytes);
}
if ($bytes === 0) {
    social_fail(400, 'File is empty', $name);
}

$mime = (string)(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
$width = $height = null;
$data = null;

if ($isVideo) {
    // Content check: a real MP4 starts with an "ftyp" box (bytes 4-8) and libmagic
    // must call it video/mp4. Renamed images, scripts or other containers fail here.
    $head = (string)file_get_contents($tmp, false, null, 0, 12);
    if ($mime !== 'video/mp4' || strlen($head) < 12 || substr($head, 4, 4) !== 'ftyp') {
        social_fail(415, 'Only real MP4 videos are accepted (file content is not MP4)', $name, $bytes);
    }
} else {
    // Content check: real PNG or JPEG bytes only. SVG, scripts and anything else fail here.
    $info = @getimagesize($tmp);
    if (!in_array($mime, ['image/png', 'image/jpeg'], true) || $info === false
        || !in_array($info[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG], true)) {
        social_fail(415, 'Only real PNG or JPEG images are accepted', $name, $bytes);
    }
    $isJpeg = $mime === 'image/jpeg';
    if ($isJpeg !== ($ext === 'jpg' || $ext === 'jpeg')) {
        social_fail(415, 'File extension does not match the image content', $name, $bytes);
    }
    [$width, $height] = $info;
    if ($width < SOCIAL_MIN_SIDE || $height < SOCIAL_MIN_SIDE || $width > SOCIAL_MAX_SIDE || $height > SOCIAL_MAX_SIDE) {
        social_fail(400, 'Image dimensions out of range (' . SOCIAL_MIN_SIDE . '–' . SOCIAL_MAX_SIDE . ' px per side)', $name, $bytes);
    }
    $data = (string)file_get_contents($tmp);
    if ($isJpeg) {
        $data = social_strip_jpeg_metadata($data);
    }
}

if (!is_dir(SOCIAL_DIR) && !@mkdir(SOCIAL_DIR, 0755, true) && !is_dir(SOCIAL_DIR)) {
    social_fail(500, 'Server could not create the image folder', $name, $bytes);
}

// 'x' mode fails if the file already exists, so two uploads can never overwrite each other.
$dest = SOCIAL_DIR . '/' . $name;
$out = @fopen($dest, 'xb');
if ($out === false) {
    if (file_exists($dest)) {
        social_fail(409, 'A file with this name already exists; upload under a new name', $name, $bytes);
    }
    social_fail(500, 'Server could not save the file', $name, $bytes);
}
if ($isVideo) {
    // Stream from the temp file so a 50 MB video never has to fit in PHP's memory.
    $in = fopen($tmp, 'rb');
    $written = $in ? stream_copy_to_stream($in, $out) : false;
    if ($in) {
        fclose($in);
    }
    $expected = $bytes;
} else {
    $written = fwrite($out, $data);
    $expected = strlen($data);
}
fclose($out);
if ($written !== $expected) {
    @unlink($dest);
    social_fail(500, 'Server could not save the file', $name, $bytes);
}
@chmod($dest, 0644);

$response = ['url' => SOCIAL_PUBLIC_BASE . $name];
if ($isVideo) {
    $response['type'] = 'video/mp4';
} else {
    $response['width'] = $width;
    $response['height'] = $height;
}
$response['bytes'] = $expected;
social_respond(201, $response, $name, $expected);
