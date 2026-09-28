<?php
// Streams the entire production database to stdout as plain SQL (schema +
// data), so it can be piped straight into gzip/openssl without ever writing
// an unencrypted copy to disk. Pure PHP/PDO — deliberately doesn't shell out
// to the mysqldump binary, since shared hosting doesn't reliably expose one
// over SSH.
//
// Usage: php scripts/backup-database.php /path/to/config/db.php > backup.sql

if ($argc < 2) {
    fwrite(STDERR, "Usage: php backup-database.php <path-to-db.php>\n");
    exit(1);
}

require $argv[1];
$pdo = get_db();

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

echo "-- Kerala Founders database backup — generated " . gmdate('c') . " UTC\n";
echo "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    // Table names come from SHOW TABLES (DB introspection), never user input.
    $createRow = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    echo "DROP TABLE IF EXISTS `$table`;\n";
    echo $createRow['Create Table'] . ";\n\n";

    $stmt = $pdo->query("SELECT * FROM `$table`");
    $columns = null;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($columns === null) {
            $columns = array_keys($row);
        }
        $values = array_map(
            fn($value) => $value === null ? 'NULL' : $pdo->quote((string)$value),
            $row
        );
        echo "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
    }
    echo "\n";
}

echo "SET FOREIGN_KEY_CHECKS=1;\n";
