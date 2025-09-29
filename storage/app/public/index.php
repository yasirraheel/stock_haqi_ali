<?php

// Storage link alternative for servers without symlink support
// This file redirects requests to the actual storage directory

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove /storage from the beginning of the path
$filePath = str_replace('/storage', '', $path);

// Construct the actual file path
$actualPath = __DIR__ . '/../storage/app/public' . $filePath;

// Check if file exists
if (file_exists($actualPath)) {
    // Get file info
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $actualPath);
    finfo_close($finfo);
    
    // Set appropriate headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($actualPath));
    
    // Output the file
    readfile($actualPath);
} else {
    // File not found
    http_response_code(404);
    echo 'File not found';
}
