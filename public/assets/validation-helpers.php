<?php
// Shared validation/anti-spam helpers used by public/api/*.php endpoints.
// Lives under assets/ (not config/) so it deploys automatically with every
// push — config/auth.php etc. are real, gitignored, server-side-only files
// that deploy never touches.

/**
 * Normalizes a raw Instagram handle/URL/@handle into a bare lowercase
 * handle. Shared by submit-company.php and admin-update.php so the two
 * forms can't drift out of sync (they previously carried separate copies
 * of the same regex logic).
 *
 * Returns: the normalized handle on success, null if $raw was empty, or
 * false if $raw was non-empty but not a valid handle — callers differ on
 * whether an invalid handle should be a hard error or silently dropped,
 * so this only normalizes/validates and leaves that decision to the caller.
 */
function normalize_instagram_handle(string $raw)
{
    $raw = trim($raw);
    if ($raw === '') {
        return null;
    }
    $handle = strtolower(preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $raw));
    $handle = preg_replace('/[\/?#].*$/', '', $handle);
    $handle = ltrim($handle, '@');
    return preg_match('/^[a-z0-9._]{1,30}$/', $handle) ? $handle : false;
}

/**
 * True if $ipHash has already hit $max rows in $table within the last
 * $windowMinutes. Shared by every public endpoint with its own IP-hash
 * rate limit (submit-company.php, submit-story-signup.php,
 * guidance-feedback.php), so a future tweak to the pattern doesn't need to
 * be copied by hand into each one. $table is never user input — always a
 * literal string from the calling code.
 */
function rate_limit_exceeded(PDO $db, string $table, string $ipHash, int $windowMinutes, int $max): bool
{
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM `$table` WHERE ip_hash = ? AND created_at > NOW() - INTERVAL $windowMinutes MINUTE"
    );
    $stmt->execute([$ipHash]);
    return (int)$stmt->fetchColumn() >= $max;
}

/** True once config/turnstile.php has been created on the server with a real secret key. */
function turnstile_enabled(): bool
{
    return defined('TURNSTILE_SECRET_KEY') && TURNSTILE_SECRET_KEY !== '';
}

/**
 * Verifies a Cloudflare Turnstile response token server-side. Returns true
 * (i.e. doesn't block the submission) when Turnstile isn't configured yet,
 * so this code is safe to deploy before config/turnstile.php exists — see
 * config/turnstile.example.php. Fails closed (rejects) on a missing token or
 * a network/API problem reaching Cloudflare, same as any CAPTCHA service.
 */
function turnstile_verify(string $token, string $remoteIp): bool
{
    if (!turnstile_enabled()) {
        return true;
    }
    if ($token === '') {
        return false;
    }
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'secret' => TURNSTILE_SECRET_KEY,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response === false) {
        return false;
    }
    $data = json_decode($response, true);
    return is_array($data) && !empty($data['success']);
}
