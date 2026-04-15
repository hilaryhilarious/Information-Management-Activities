<?php
$request_uri = $_SERVER['REQUEST_URI'];
$file_path = __DIR__ . $request_uri;

// Serve static files directly
if (is_file($file_path) && preg_match('/\\.(html|css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $file_path)) {
    return false;
}

// API routes
if (strpos($request_uri, '/api/') === 0) {
    $api_file = __DIR__ . '/../api/' . basename(parse_url($request_uri, PHP_URL_PATH));
    if (file_exists($api_file)) {
        include $api_file;
        exit;
    }
}

// Default to index.html for root
if ($request_uri === '/' || $request_uri === '') {
    include __DIR__ . '/index.html';
    exit;
}

// 404
http_response_code(404);
echo '404 Not Found';
