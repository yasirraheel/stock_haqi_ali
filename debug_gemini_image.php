<?php

require_once 'vendor/autoload.php';

use App\Services\GeminiImageService;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the Gemini image service
$service = new GeminiImageService();

// Test with a simple prompt
echo "Testing Gemini Image Generation...\n";
echo "=====================================\n\n";

$result = $service->generateImage(
    'Test Movie Title',
    'This is a test movie description for debugging purposes',
    'test_' . time()
);

echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";

// If there's an error, let's test the base64 data directly
if (isset($result['error'])) {
    echo "\nError occurred. Let's test with a sample base64 data...\n";
    
    // Sample base64 data (1x1 pixel PNG)
    $sampleBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    
    $testResult = $service->testBase64Data($sampleBase64);
    echo "Sample base64 test: " . json_encode($testResult, JSON_PRETTY_PRINT) . "\n";
}

echo "\nDone!\n";
