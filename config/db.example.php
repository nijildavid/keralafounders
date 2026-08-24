<?php
// Copy this file to db.php and fill in real credentials.
// db.php itself is gitignored — never commit real database credentials.

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
