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
