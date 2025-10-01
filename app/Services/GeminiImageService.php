<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiImageService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected $imageModel = 'imagen-3.0-generate-001';
    protected $fallbackImageModel = 'gemini-1.5-flash';

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
            $imageData = $this->callGeminiApi($prompt);

            if (!$imageData) {
                Log::warning('No image data returned from Gemini API, using fallback');
                return ['error' => 'Failed to generate image from Gemini API'];
            }

            // Validate the image data
            $decoded = base64_decode($imageData, true);
            if ($decoded === false) {
                Log::error('Invalid base64 image data from Gemini');
                return ['error' => 'Invalid image data from Gemini API'];
            }

            // Check if it's valid image data
            if (!$this->isValidImageData($decoded)) {
                Log::error('Gemini returned invalid image format');
                return ['error' => 'Invalid image format from Gemini API'];
            }

            // Save the generated image
            $imagePath = $this->saveImage($imageData, $fileId);

            if (!$imagePath) {
                Log::error('Failed to save Gemini generated image to disk');
                return ['error' => 'Failed to save generated image'];
            }

            Log::info("Successfully generated and saved AI image: {$imagePath}");
            return [
                'success' => 'AI image generated successfully using Gemini',
                'image_path' => $imagePath
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Image Generation Error: ' . $e->getMessage());
            return ['error' => 'Error generating image: ' . $e->getMessage()];
        }
    }

    /**
     * Create a detailed prompt for image generation optimized for Imagen API
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

        // Create a concise but detailed prompt optimized for Imagen
        $prompt = "Movie poster for '{$title}'";

        if (!empty($description)) {
            // Extract key themes from description
            $prompt .= ", " . trim($description);
        }

        $prompt .= ". Cinematic, professional movie poster design with dramatic lighting";
        $prompt .= ", vibrant colors, high-quality digital art style";
        $prompt .= ", perfect for video thumbnail, engaging and eye-catching";
        $prompt .= ", modern entertainment industry aesthetic";

        return $prompt;
    }

    /**
     * Call Imagen API to generate image using official Google AI approach
     *
     * @param string $prompt
     * @return string|null
     */
    private function callGeminiApi($prompt)
    {
        try {
            // Use the official Imagen API endpoint structure based on Google AI documentation
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/models/{$this->imageModel}:generateImages", [
                'prompt' => $prompt,
                'config' => [
                    'numberOfImages' => 1,
                    'aspectRatio' => '16:9', // Perfect for video thumbnails
                    'safetyFilterLevel' => 'block_some',
                    'personGeneration' => 'allow_adult'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Imagen API Response Structure: ' . json_encode($data, JSON_PRETTY_PRINT));

                // Based on official documentation: response.generatedImages[0].image.imageBytes
                if (isset($data['generatedImages'][0]['image']['imageBytes'])) {
                    $imageData = $data['generatedImages'][0]['image']['imageBytes'];
                    
                    Log::info('Successfully extracted image from Imagen API (' . strlen($imageData) . ' chars)');
                    return $imageData;
                }

                Log::warning('No generatedImages found in Imagen response');
            } else {
                $errorData = $response->json();
                Log::error('Imagen API Error: ' . $response->status() . ' - ' . json_encode($errorData));
                
                // If Imagen fails, try the previous approach that was working
                return $this->callGeminiTextToImage($prompt);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Imagen API Exception: ' . $e->getMessage());
            // Fallback to the previous approach if Imagen API fails
            return $this->callGeminiTextToImage($prompt);
        }
    }

    /**
     * Fallback approach using Gemini text model (based on working logs)
     *
     * @param string $prompt
     * @return string|null
     */
    private function callGeminiTextToImage($prompt)
    {
        try {
            Log::info('Using fallback Gemini text-to-image approach');
            
            // Use the fallback model that was working in your logs
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/models/{$this->fallbackImageModel}:generateContent", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Based on your logs showing inlineData structure
                if (isset($data['candidates'][0]['content']['parts'])) {
                    $parts = $data['candidates'][0]['content']['parts'];

                    foreach ($parts as $part) {
                        if (isset($part['inlineData']['data'])) {
                            $imageData = $part['inlineData']['data'];
                            
                            Log::info('Found fallback image data (' . strlen($imageData) . ' chars)');
                            
                            if (strlen($imageData) > 100) {
                                $decoded = base64_decode($imageData, true);
                                if ($decoded !== false) {
                                    Log::info('Valid fallback image data found');
                                    return $imageData;
                                }
                            }
                        }
                    }
                }
                
                Log::warning('No valid image data in fallback response');
            } else {
                Log::error('Fallback API request failed: ' . $response->status());
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Gemini Fallback Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate if the data is valid image data
     *
     * @param string $data
     * @return bool
     */
    private function isValidImageData($data)
    {
        // Check for common image headers
        $headers = [
            "\xFF\xD8\xFF", // JPEG
            "\x89PNG\r\n\x1a\n", // PNG
            "GIF87a", // GIF87a
            "GIF89a", // GIF89a
            "RIFF" // WebP
        ];

        foreach ($headers as $header) {
            if (strpos($data, $header) === 0) {
                return true;
            }
        }

        return false;
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

            // Decode base64 image data
            $imageData = base64_decode($imageData);

            if (!$imageData) {
                return null;
            }

            // Save image to public directory
            $imagePath = "screenshots/{$fileId}.jpg";
            $fullPath = public_path($imagePath);

            if (file_put_contents($fullPath, $imageData)) {
                return $imagePath;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Image Save Error: ' . $e->getMessage());
            return null;
        }
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

