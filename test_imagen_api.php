<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\GeminiImageService;
use Illuminate\Support\Facades\Log;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Testing Imagen API Integration...\n";

try {
    $geminiService = new GeminiImageService();
    
    // Test with a sample movie
    $testTitle = "Action Movie: The Ultimate Heist";
    $testDescription = "A thrilling action movie about a team of professional thieves planning the ultimate heist in a futuristic city";
    $testFileId = 'test_' . time();
    
    echo "Testing AI image generation for: {$testTitle}\n";
    echo "File ID: {$testFileId}\n";
    echo "Starting generation...\n\n";
    
    $result = $geminiService->generateImage($testTitle, $testDescription, $testFileId);
    
    if (isset($result['success'])) {
        echo "✅ SUCCESS: " . $result['success'] . "\n";
        echo "📁 Image Path: " . $result['image_path'] . "\n";
        
        $fullPath = __DIR__ . '/public/' . $result['image_path'];
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            echo "📊 File Size: " . number_format($fileSize) . " bytes\n";
            
            // Get image info
            $imageInfo = getimagesize($fullPath);
            if ($imageInfo) {
                echo "🎨 Dimensions: {$imageInfo[0]}x{$imageInfo[1]}\n";
                echo "🔧 Format: " . image_type_to_mime_type($imageInfo[2]) . "\n";
            }
        } else {
            echo "❌ ERROR: Generated image file not found at {$fullPath}\n";
        }
    } else {
        echo "❌ ERROR: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\nTest completed. Check the logs for detailed information.\n";