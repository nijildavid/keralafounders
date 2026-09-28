<?php
header('Content-Type: application/javascript; charset=utf-8');
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/reference.php';
require __DIR__ . '/render-helpers.php';

$db = get_db();

$companyRows = $db->query("SELECT * FROM companies WHERE status = 'approved' ORDER BY created_at DESC, id DESC")->fetchAll();
$foundersByCompany = fetch_founders_by_company_id($db, array_column($companyRows, 'id'));

$companies = [];
foreach ($companyRows as $row) {
    $companies[] = [
        'id' => $row['slug'],
        'name' => $row['name'],
        'founders' => $foundersByCompany[$row['id']] ?? [],
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
    'keralaDistricts' => $KF_KERALA_DISTRICTS,
    'keralaConnections' => $KF_KERALA_CONNECTIONS,
    'countryFlags' => $KF_COUNTRY_FLAGS,
    'companies' => $companies,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';';
