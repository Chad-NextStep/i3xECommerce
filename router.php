<?php

/**
 * router.php — Development router for `php -S localhost:8000`
 *
 * Routes /api/* to the API handler; serves static files from public/.
 * Usage: php -S localhost:8000 router.php
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Route API requests
if (str_starts_with($path, '/api/') || $path === '/api') {
    // Set up REQUEST_URI for the API router
    $_SERVER['REQUEST_URI'] = $path . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '');
    require __DIR__ . '/api/index.php';
    return true;
}

// Try to serve a static file from public/
$public = __DIR__ . '/public';

// Direct file match
$file = $public . $path;
if (is_file($file)) {
    // Let the built-in server handle known file types
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mime_types = [
        'html' => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
    ];
    if (isset($mime_types[$ext])) {
        header('Content-Type: ' . $mime_types[$ext]);
    }
    readfile($file);
    return true;
}

// Try /path/index.html (clean URLs)
$index = rtrim($file, '/') . '/index.html';
if (is_file($index)) {
    header('Content-Type: text/html');
    readfile($index);
    return true;
}

// 404
http_response_code(404);
echo '<!DOCTYPE html><html><body><h1>404 Not Found</h1></body></html>';
return true;
