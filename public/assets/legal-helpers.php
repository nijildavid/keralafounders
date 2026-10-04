<?php

/**
 * Operator details for the Impressum page, read from config/legal.php (a
 * server-only file, see config/legal.example.php). Returns null until the
 * file exists and has a name, street and city, so callers can hide the page
 * and its footer link instead of showing something half-filled.
 */
function kf_legal_details(): ?array
{
    static $cached = false;
    if ($cached !== false) {
        return $cached;
    }
    $cached = null;
    $path = __DIR__ . '/../../config/legal.php';
    if (!is_file($path)) {
        return null;
    }
    $KF_LEGAL = [];
    include $path;
    if (!is_array($KF_LEGAL)) {
        return null;
    }
    foreach (['name', 'street', 'city'] as $required) {
        if (trim((string)($KF_LEGAL[$required] ?? '')) === '') {
            return null;
        }
    }
    $cached = $KF_LEGAL;
    return $cached;
}
