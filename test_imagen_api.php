<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\GeminiImageService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Gemini Image Generation...\n";

try {
    $geminiService = new GeminiImageService();
    
    // Test with a simple video title
    $videoTitle = "UK City Skyline 1900s";
    $videoDescription = "A historical view of the UK city skyline from the 1900s era";
    $fileId = "test_" . time();
    
    echo "Generating image for: $videoTitle\n";
    echo "File ID: $fileId\n";
    
    $result = $geminiService->generateImage($videoTitle, $videoDescription, $fileId);
    
    if (isset($result['error'])) {
        echo "Error: " . $result['error'] . "\n";
        
        // Try fallback method
        echo "Trying fallback method...\n";
        $fallbackPath = $geminiService->generateFallbackImage($videoTitle, $fileId);
        
        if ($fallbackPath) {
            echo "Fallback image generated successfully: $fallbackPath\n";
        } else {
            echo "Fallback image generation also failed\n";
        }
    } else {
        echo "Success: " . $result['success'] . "\n";
        echo "Image path: " . $result['image_path'] . "\n";
        
        // Check if file actually exists
        $fullPath = public_path($result['image_path']);
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            echo "File exists and is $fileSize bytes\n";
        } else {
            echo "Warning: File does not exist at $fullPath\n";
        }
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "Test completed.\n";