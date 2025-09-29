<?php

/**
 * Alternative to php artisan storage:link for servers without symlink support
 * This script copies storage/app/public contents to public/storage
 */

echo "Creating storage link alternative...\n";

$sourceDir = __DIR__ . '/storage/app/public';
$targetDir = __DIR__ . '/public/storage';

// Create target directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "Created directory: $targetDir\n";
}

// Function to copy directory recursively
function copyDirectory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDirectory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// Copy files from storage/app/public to public/storage
if (is_dir($sourceDir)) {
    copyDirectory($sourceDir, $targetDir);
    echo "Successfully copied storage files to public/storage\n";
    echo "Storage link alternative created successfully!\n";
} else {
    echo "Error: Source directory $sourceDir does not exist\n";
}

echo "Done!\n";
