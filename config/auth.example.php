<?php

// Copy this file to auth.php (on the server, outside the web root) and set
// your own password hash. auth.php itself is gitignored — never commit a
// real password hash. Generate one with:
//   php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
define('ADMIN_PASSWORD_HASH', 'paste-your-generated-hash-here');

define('LOGIN_ATTEMPTS_FILE', __DIR__ . '/login-attempts.json');
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_SECONDS', 300);

// An admin session left open with no activity for this long is force-expired
// on the next admin page load — limits how long a stolen/leaked session
// cookie or an unattended logged-in browser stays useful.
define('ADMIN_SESSION_IDLE_TIMEOUT', 1800);

// Optional second login factor (TOTP, e.g. Google Authenticator/Authy) on
// top of the password. Leave both blank to skip 2FA entirely — nothing
// below runs unless a real secret is set, so a fresh install still logs in
// with just the password until you deliberately turn this on.
// Generate a secret + one-time backup code with:
//   php scripts/generate-2fa-secret.php
define('ADMIN_TOTP_SECRET', '');
define('ADMIN_TOTP_BACKUP_CODE_HASH', '');
define('ADMIN_TOTP_BACKUP_USED_FILE', __DIR__ . '/totp-backup-used.flag');

function admin_2fa_enabled(): bool
{
    return ADMIN_TOTP_SECRET !== '';
}

const TOTP_BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

function totp_base32_decode(string $b32): string
{
    $b32 = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $b32));
    $bits = '';
    foreach (str_split($b32) as $char) {
        $val = strpos(TOTP_BASE32_ALPHABET, $char);
        if ($val === false) {
            continue;
        }
        $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
    }
    $bytes = '';
    foreach (str_split($bits, 8) as $byte) {
        if (strlen($byte) === 8) {
            $bytes .= chr(bindec($byte));
        }
    }
    return $bytes;
}

/** 6-digit code for the 30-second window containing $timestamp (RFC 6238, SHA-1 — the Google Authenticator default). */
function totp_code(string $secret, int $timestamp): string
{
    $counter = intdiv($timestamp, 30);
    $binCounter = str_pad(pack('N', $counter), 8, "\0", STR_PAD_LEFT);
    $hash = hash_hmac('sha1', $binCounter, totp_base32_decode($secret), true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $truncated = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;
    return str_pad((string)($truncated % 1000000), 6, '0', STR_PAD_LEFT);
}

/** Accepts the current code plus one 30s step either side, to tolerate small clock drift. */
function totp_verify(string $secret, string $code): bool
{
    $code = preg_replace('/\D/', '', $code);
    if (strlen($code) !== 6) {
        return false;
    }
    $now = time();
    for ($step = -1; $step <= 1; $step++) {
        if (hash_equals(totp_code($secret, $now + ($step * 30)), $code)) {
            return true;
        }
    }
    return false;
}

/** Single-use recovery code for when the authenticator app/device is unavailable. */
function totp_backup_code_verify(string $code): bool
{
    if (ADMIN_TOTP_BACKUP_CODE_HASH === '' || file_exists(ADMIN_TOTP_BACKUP_USED_FILE)) {
        return false;
    }
    if (!password_verify(trim($code), ADMIN_TOTP_BACKUP_CODE_HASH)) {
        return false;
    }
    @file_put_contents(ADMIN_TOTP_BACKUP_USED_FILE, (string)time(), LOCK_EX);
    return true;
}

// Salt mixed into the hashed IP address stored against a Guidance feedback
// vote, so the rate limiter can recognise repeat visitors without ever
// storing a raw IP. Generate one with:
//   php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
define('GUIDANCE_FEEDBACK_IP_SALT', 'paste-your-generated-salt-here');

function kf_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function require_admin(): void
{
    kf_session_start();
    if (empty($_SESSION['is_admin'])) {
        header('Location: admin-login.php');
        exit;
    }
    if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > ADMIN_SESSION_IDLE_TIMEOUT) {
        session_unset();
        session_destroy();
        header('Location: admin-login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

/** Per-session CSRF token, embedded in every admin form/fetch and checked on every state-changing request. */
function csrf_token(): string
{
    kf_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(?string $token): bool
{
    kf_session_start();
    return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Login attempt tracking, keyed by IP and stored in a file outside the web root.
 * Deliberately IP-based rather than session-based: a session-based counter is
 * trivially bypassed by an attacker who just doesn't send cookies back.
 */
function client_ip(): string
{
    return (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function login_read_attempts(): array
{
    if (!file_exists(LOGIN_ATTEMPTS_FILE)) {
        return [];
    }
    $data = json_decode((string)file_get_contents(LOGIN_ATTEMPTS_FILE), true);
    return is_array($data) ? $data : [];
}

function login_write_attempts(array $data): void
{
    $cutoff = time() - 3600;
    // prune entries with no activity in the last hour, so the file doesn't grow forever —
    // deliberately keyed off lastAttempt, not lockedUntil (which is legitimately unset/0
    // for the first few failures, before an IP has actually earned a lockout)
    $data = array_filter($data, fn($entry) => ($entry['lastAttempt'] ?? 0) > $cutoff);
    // Best-effort: if the file isn't writable, lockout just won't persist rather than
    // breaking login entirely — worth checking your host's error_log if lockout never engages.
    @file_put_contents(LOGIN_ATTEMPTS_FILE, json_encode($data), LOCK_EX);
}

function login_is_locked_out(): bool
{
    $entry = login_read_attempts()[client_ip()] ?? null;
    return $entry
        && ($entry['count'] ?? 0) >= LOGIN_MAX_ATTEMPTS
        && time() < ($entry['lockedUntil'] ?? 0);
}

function login_register_failure(): void
{
    $ip = client_ip();
    $data = login_read_attempts();
    $count = ($data[$ip]['count'] ?? 0) + 1;
    $entry = [
        'count' => $count,
        'lockedUntil' => $data[$ip]['lockedUntil'] ?? 0,
        'lastAttempt' => time(),
    ];
    if ($count >= LOGIN_MAX_ATTEMPTS) {
        $entry['lockedUntil'] = time() + LOGIN_LOCKOUT_SECONDS;
    }
    $data[$ip] = $entry;
    login_write_attempts($data);
}

function login_reset_attempts(): void
{
    $data = login_read_attempts();
    unset($data[client_ip()]);
    login_write_attempts($data);
}
