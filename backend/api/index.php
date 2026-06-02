<?php
// Catch the incoming request path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Clean up the path (e.g., /api/login -> login)
$fileName = str_replace('/api/', '', $path);

// If no file is specified, default to config
if (empty($fileName)) {
    $fileName = 'config';
}

// Append .php if it isn't there
if (substr($fileName, -4) !== '.php') {
    $fileName .= '.php';
}

// Point directly to your existing backend files
$targetFile = __DIR__ . '/../backend/api/' . $fileName;

if (file_exists($targetFile)) {
    include $targetFile;
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["error" => "Endpoint not found: " . $fileName]);
}
