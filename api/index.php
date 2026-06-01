<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

load_env();

session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (get_request_method() === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);

// Strip /api prefix if present
$path = preg_replace('#^/api#', '', $path);
$path = '/' . trim($path, '/');

match ($path) {
    '/cart'      => require __DIR__ . '/cart.php',
    '/checkout'  => require __DIR__ . '/checkout.php',
    '/webhook'   => require __DIR__ . '/webhook.php',
    '/inventory' => require __DIR__ . '/inventory.php',
    default      => json_error('Not found', 404),
};
