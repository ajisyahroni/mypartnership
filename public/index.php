<?php

if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' &&
    isset($_SERVER['CONTENT_LENGTH']) &&
    $_SERVER['CONTENT_LENGTH'] > convertToBytes(ini_get('post_max_size'))
) {
    header('Content-Type: application/json; charset=utf-8', true, 413);

    echo json_encode(
        'Sistem terjadi kesalahan, maksimal upload '
            . ini_get('post_max_size')
            . 'B dan segera hubungi admin.'
    );
    exit;
}

function convertToBytes($value)
{
    $unit  = strtoupper(substr($value, -1));
    $bytes = (int) $value;

    return match ($unit) {
        'G' => $bytes * 1024 * 1024 * 1024,
        'M' => $bytes * 1024 * 1024,
        'K' => $bytes * 1024,
        default => $bytes,
    };
}


use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__ . '/../bootstrap/app.php')
    ->handleRequest(Request::capture());
