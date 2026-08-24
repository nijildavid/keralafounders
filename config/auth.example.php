<?php
// Copy this file to auth.php and set your own password hash.
// auth.php itself is gitignored — never commit a real password hash.
//
// Generate a hash with:
//   php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"

define('ADMIN_PASSWORD_HASH', 'paste-your-generated-hash-here');

function require_admin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['is_admin'])) {
        header('Location: admin-login.php');
        exit;
    }
}
