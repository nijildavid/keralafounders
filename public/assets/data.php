<?php
header('Content-Type: application/javascript; charset=utf-8');
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/reference.php';

$db = get_db();

$companies = [];
foreach ($db->query("SELECT * FROM companies WHERE status = 'approved' ORDER BY created_at DESC, id DESC") as $row) {
    $founders = $db->prepare('SELECT name FROM founders WHERE company_id = ? ORDER BY id');
    $founders->execute([$row['id']]);

    $companies[] = [
        'id' => $row['slug'],
        'name' => $row['name'],
        'founders' => $founders->fetchAll(PDO::FETCH_COLUMN),
        'country' => $row['country'],
        'city' => $row['city'],
        'industry' => $row['industry'],
        'business_type' => $row['business_type'],
        'industry_detail' => $row['industry_detail'],
        'size' => $row['size'],
        'description' => $row['description'],
        'website' => $row['website'],
        'verified' => (bool)$row['verified'],
    ];
}

echo 'window.KF = ' . json_encode([
    'countries' => $KF_COUNTRIES,
    'industries' => $KF_INDUSTRIES,
    'businessTypes' => $KF_BUSINESS_TYPES,
    'sizes' => $KF_SIZES,
    'companies' => $companies,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';';
