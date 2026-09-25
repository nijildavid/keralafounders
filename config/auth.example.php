<?php

// Copy this file to auth.php (on the server, outside the web root) and set
// your own password hash. auth.php itself is gitignored — never commit a
// real password hash. Generate one with:
//   php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
define('ADMIN_PASSWORD_HASH', 'paste-your-generated-hash-here');

define('LOGIN_ATTEMPTS_FILE', __DIR__ . '/login-attempts.json');
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_SECONDS', 300);

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
