<?php
// Upload this file to your GoDaddy public_html temporarily
echo "PHP Path: " . PHP_BINARY . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Home Directory: " . $_SERVER['HOME'] ?? 'Not available' . "\n";

// Check if artisan file exists
$artisan_path = dirname(__DIR__) . '/artisan';
echo "Artisan exists: " . (file_exists($artisan_path) ? 'YES' : 'NO') . "\n";
echo "Artisan path: " . $artisan_path . "\n";
?>