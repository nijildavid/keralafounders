<?php
// Copy this file to db.php (on the server, outside the web root — never inside
// public_html or the domain's document root) and fill in your real credentials.
// db.php itself is gitignored — never commit real database credentials.

// This is the first file every page on the site loads, so it's the one place
// to guarantee PHP errors never render into the response a visitor sees —
// an uncaught error/exception can otherwise leak file paths, query
// fragments, or other internals. Errors are still logged server-side.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=your_db_name;charset=utf8mb4',
            'your_db_user',
            'your_db_password',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    return $pdo;
}
