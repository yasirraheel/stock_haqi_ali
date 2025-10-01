<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiImageService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected $imageModel = 'imagen-3.0-generate-002';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Generate an image using Gemini AI based on video title and description
     *
     * @param string $videoTitle
     * @param string $videoDescription
     * @param string $fileId
     * @return array
     */
    public function generateImage($videoTitle, $videoDescription, $fileId)
    {
        try {
            // Create a detailed prompt based on video title and description
            $prompt = $this->createImagePrompt($videoTitle, $videoDescription);

            // Generate image using Gemini
            Log::info('Starting Gemini image generation', ['title' => $videoTitle, 'file_id' => $fileId]);
            $imageData = $this->callGeminiApi($prompt);

            if (!$imageData) {
                Log::warning('No image data returned from Gemini API');
                return ['error' => 'Failed to generate image from Gemini API'];
            }

            Log::info('Received image data from Gemini, attempting to save');

            // Save the generated image
            $imagePath = $this->saveImage($imageData, $fileId);

            if (!$imagePath) {
                Log::error('Failed to save generated image to disk');
                return ['error' => 'Failed to save generated image'];
            }

            Log::info('Successfully generated and saved image', ['path' => $imagePath]);

            return [
                'success' => 'Image generated successfully',
                'image_path' => $imagePath
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Image Generation Error: ' . $e->getMessage());
            return ['error' => 'Error generating image: ' . $e->getMessage()];
        }
    }

    /**
     * Create a detailed prompt for image generation
     *
     * @param string $videoTitle
     * @param string $videoDescription
     * @return string
     */
    private function createImagePrompt($videoTitle, $videoDescription)
    {
        // Extract key elements from title and description
        $title = trim($videoTitle);
        $description = trim($videoDescription);

        // Create a comprehensive prompt for movie poster/thumbnail generation
        $prompt = "Create a high-quality movie poster/thumbnail image for a video titled '{$title}'. ";

        if (!empty($description)) {
            $prompt .= "Description: {$description}. ";
        }

        $prompt .= "The image should be cinematic, professional, and visually appealing. ";
        $prompt .= "Include relevant visual elements that represent the content. ";
        $prompt .= "Use a 16:9 aspect ratio suitable for video thumbnails. ";
        $prompt .= "Make it engaging and eye-catching for viewers. ";
        $prompt .= "Style: modern, high-quality, professional movie poster design.";

        return $prompt;
    }

    /**
     * Call Imagen API to generate image
     *
     * @param string $prompt
     * @return string|null
     */
    private function callGeminiApi($prompt)
    {
        try {
            // Use the correct Imagen API endpoint
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-002:predict", [
                'instances' => [
                    [
                        'prompt' => $prompt
                    ]
                ],
                'parameters' => [
                    'sampleCount' => 1,
                    'aspectRatio' => '16:9'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Imagen API Response received');

                // Check if we have predictions with generated images
                if (isset($data['predictions'][0]['bytesBase64Encoded'])) {
                    $imageData = $data['predictions'][0]['bytesBase64Encoded'];
                    Log::info('Found image data from Imagen API, length: ' . strlen($imageData));
                    return $imageData;
                }

                // Alternative response structure check
                if (isset($data['predictions'][0]['generatedImages'][0]['bytesBase64Encoded'])) {
                    $imageData = $data['predictions'][0]['generatedImages'][0]['bytesBase64Encoded'];
                    Log::info('Found image data in generatedImages, length: ' . strlen($imageData));
                    return $imageData;
                }

                Log::warning('No image data found in Imagen API response');
                Log::info('Response structure: ' . json_encode($data, JSON_PRETTY_PRINT));

            } else {
                $errorData = $response->json();
                $errorMessage = 'Imagen API HTTP Error: ' . $response->status();

                if (isset($errorData['error']['message'])) {
                    $errorMessage .= ' - ' . $errorData['error']['message'];
                }

                Log::error($errorMessage);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Imagen API Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Save the generated image to storage
     *
     * @param string $imageData
     * @param string $fileId
     * @return string|null
     */
    private function saveImage($imageData, $fileId)
    {
        try {
            // Ensure the public screenshots directory exists
            $publicScreenshotsDir = public_path('screenshots');
            if (!file_exists($publicScreenshotsDir)) {
                mkdir($publicScreenshotsDir, 0777, true);
            }

            // Log the size of the base64 data we received
            Log::info('Received base64 image data length: ' . strlen($imageData));

            // Log first and last 100 characters for debugging
            Log::info('Base64 data start: ' . substr($imageData, 0, 100));
            Log::info('Base64 data end: ' . substr($imageData, -100));

            // Clean the base64 data (remove any whitespace or newlines)
            $cleanImageData = preg_replace('/\s+/', '', $imageData);
            Log::info('Cleaned base64 data length: ' . strlen($cleanImageData));

            // Validate base64 data
            if (!preg_match('/^[a-zA-Z0-9+\/]*={0,2}$/', $cleanImageData)) {
                Log::error('Invalid base64 data format detected');
                return null;
            }

            // Decode base64 image data
            $decodedImageData = base64_decode($cleanImageData, true);

            if ($decodedImageData === false) {
                Log::error('Failed to decode base64 image data - invalid base64 format');
                return null;
            }

            if (empty($decodedImageData)) {
                Log::error('Decoded image data is empty');
                return null;
            }

            Log::info('Decoded image data size: ' . strlen($decodedImageData) . ' bytes');

            // Validate that we have actual image data by checking magic bytes
            $imageType = $this->getImageTypeFromData($decodedImageData);
            if (!$imageType) {
                Log::error('Decoded data does not appear to be a valid image');
                return null;
            }

            Log::info('Detected image type: ' . $imageType);

            // Save image to public directory with appropriate extension
            $extension = $imageType === 'png' ? 'png' : 'jpg';
            $imagePath = "screenshots/{$fileId}.{$extension}";
            $fullPath = public_path($imagePath);

            // Write the file
            $bytesWritten = file_put_contents($fullPath, $decodedImageData);

            if ($bytesWritten === false) {
                Log::error('Failed to write image data to file: ' . $fullPath);
                return null;
            }

            Log::info("Successfully saved image to: {$fullPath} ({$bytesWritten} bytes written)");

            // Verify the file was created and has content
            if (!file_exists($fullPath) || filesize($fullPath) === 0) {
                Log::error('Image file was not created or is empty');
                return null;
            }

            return $imagePath;

        } catch (\Exception $e) {
            Log::error('Image Save Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Detect image type from binary data
     *
     * @param string $data
     * @return string|null
     */
    private function getImageTypeFromData($data)
    {
        if (strlen($data) < 4) {
            return null;
        }

        $magicBytes = substr($data, 0, 8);

        // Check for common image formats
        if (substr($magicBytes, 0, 2) === "\xFF\xD8") {
            return 'jpeg';
        }

        // PNG signature: 89 50 4E 47 0D 0A 1A 0A
        if (substr($magicBytes, 0, 8) === "\x89PNG\r\n\x1A\n") {
            return 'png';
        }

        // Alternative PNG check (just the first 4 bytes)
        if (substr($magicBytes, 0, 4) === "\x89PNG") {
            return 'png';
        }

        if (substr($magicBytes, 0, 6) === "GIF87a" || substr($magicBytes, 0, 6) === "GIF89a") {
            return 'gif';
        }

        if (substr($magicBytes, 0, 2) === "BM") {
            return 'bmp';
        }

        // If we can't detect the format but have substantial data, assume it's a valid image
        if (strlen($data) > 1000) {
            Log::info('Could not detect image format, but data size suggests valid image. Assuming PNG.');
            return 'png';
        }

        return null;
    }

    /**
     * Test method to debug base64 data issues
     *
     * @param string $base64Data
     * @return array
     */
    public function testBase64Data($base64Data)
    {
        $result = [
            'original_length' => strlen($base64Data),
            'is_valid_base64' => false,
            'decoded_length' => 0,
            'is_valid_image' => false,
            'image_type' => null,
            'errors' => []
        ];

        try {
            // Clean the data
            $cleanData = preg_replace('/\s+/', '', $base64Data);
            $result['cleaned_length'] = strlen($cleanData);

            // Validate base64
            if (preg_match('/^[a-zA-Z0-9+\/]*={0,2}$/', $cleanData)) {
                $result['is_valid_base64'] = true;

                // Decode
                $decoded = base64_decode($cleanData, true);
                if ($decoded !== false) {
                    $result['decoded_length'] = strlen($decoded);
                    $result['is_valid_image'] = true;
                    $result['image_type'] = $this->getImageTypeFromData($decoded);
                } else {
                    $result['errors'][] = 'Failed to decode base64 data';
                }
            } else {
                $result['errors'][] = 'Invalid base64 format';
            }

        } catch (\Exception $e) {
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Generate a fallback image using a simple text-based approach
     * This can be used if Gemini API fails
     *
     * @param string $videoTitle
     * @param string $fileId
     * @return string|null
     */
    public function generateFallbackImage($videoTitle, $fileId)
    {
        try {
            // Create a more attractive fallback image using GD
            $width = 1280;
            $height = 720;

            $image = imagecreatetruecolor($width, $height);

            // Create a more sophisticated gradient background
            for ($i = 0; $i < $height; $i++) {
                $ratio = $i / $height;
                // Dark blue to purple gradient
                $r = 15 + ($ratio * 25);
                $g = 15 + ($ratio * 15);
                $b = 35 + ($ratio * 45);
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $i, $width, $i, $color);
            }

            // Add some visual elements
            $accentColor = imagecolorallocate($image, 100, 150, 255);
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $shadowColor = imagecolorallocate($image, 0, 0, 0);

            // Add a subtle border
            imagerectangle($image, 5, 5, $width-6, $height-6, $accentColor);

            // Add title text with shadow effect
            $fontSize = 5; // Use built-in font 5 for better readability
            $title = wordwrap($videoTitle, 30, "\n", true);
            $lines = explode("\n", $title);

            $lineHeight = imagefontheight($fontSize) + 10;
            $totalHeight = count($lines) * $lineHeight;
            $startY = ($height - $totalHeight) / 2;

            foreach ($lines as $index => $line) {
                $textWidth = strlen($line) * imagefontwidth($fontSize);
                $x = ($width - $textWidth) / 2;
                $y = $startY + ($index * $lineHeight);

                // Draw shadow
                imagestring($image, $fontSize, $x + 2, $y + 2, $line, $shadowColor);
                // Draw main text
                imagestring($image, $fontSize, $x, $y, $line, $textColor);
            }

            // Add "Video Thumbnail" subtitle
            $subtitle = "Video Thumbnail";
            $subtitleWidth = strlen($subtitle) * imagefontwidth(3);
            $subtitleX = ($width - $subtitleWidth) / 2;
            $subtitleY = $startY + $totalHeight + 30;

            // Draw subtitle shadow
            imagestring($image, 3, $subtitleX + 1, $subtitleY + 1, $subtitle, $shadowColor);
            // Draw subtitle
            imagestring($image, 3, $subtitleX, $subtitleY, $subtitle, $accentColor);

            // Add a decorative line
            $lineY = $subtitleY + 40;
            imageline($image, $width/4, $lineY, 3*$width/4, $lineY, $accentColor);

            // Ensure the public screenshots directory exists
            $publicScreenshotsDir = public_path('screenshots');
            if (!file_exists($publicScreenshotsDir)) {
                mkdir($publicScreenshotsDir, 0777, true);
            }

            // Save image
            $imagePath = "screenshots/{$fileId}.jpg";
            $fullPath = public_path($imagePath);

            if (imagejpeg($image, $fullPath, 90)) {
                imagedestroy($image);
                return $imagePath;
            }

            imagedestroy($image);
            return null;

        } catch (\Exception $e) {
            Log::error('Fallback Image Generation Error: ' . $e->getMessage());
            return null;
        }
    }
}

