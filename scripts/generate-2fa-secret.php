<?php
// One-time setup helper for admin login 2FA. Run manually on your own
// machine (not needed on the server):
//   php scripts/generate-2fa-secret.php
//
// Paste the two values it prints into your live config/auth.php (the
// ADMIN_TOTP_SECRET and ADMIN_TOTP_BACKUP_CODE_HASH constants), then add the
// secret to an authenticator app (Google Authenticator, Authy, etc.) via its
// "enter a setup key manually" option — no QR code needed. Write the backup
// code down somewhere safe; it's shown once here and can't be recovered
// later, and it only works once before it's spent.

const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

function base32_encode(string $bytes): string
{
    $bits = '';
    foreach (str_split($bytes) as $byte) {
        $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
    }
    $bits = str_pad($bits, (int)(ceil(strlen($bits) / 5) * 5), '0', STR_PAD_RIGHT);
    $encoded = '';
    foreach (str_split($bits, 5) as $chunk) {
        $encoded .= BASE32_ALPHABET[bindec($chunk)];
    }
    return $encoded;
}

$secret = base32_encode(random_bytes(20));
$backupCode = strtoupper(bin2hex(random_bytes(5))); // 10 hex chars, easy to read/write down
$backupCodeHash = password_hash($backupCode, PASSWORD_DEFAULT);

echo "Add these two lines to your live config/auth.php (replacing the blank placeholders):\n\n";
echo "define('ADMIN_TOTP_SECRET', '{$secret}');\n";
echo "define('ADMIN_TOTP_BACKUP_CODE_HASH', '{$backupCodeHash}');\n\n";
echo "In your authenticator app, add a new account manually with:\n";
echo "  Account name: Kerala Founders admin\n";
echo "  Key: {$secret}\n";
echo "  Type: Time based\n\n";
echo "One-time backup code (use only if you lose access to the authenticator app — write it down now, it won't be shown again):\n";
echo "  {$backupCode}\n";
