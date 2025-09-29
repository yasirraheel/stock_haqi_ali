<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiImageService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected $imageModel = 'gemini-2.5-flash-image-preview';

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
                return ['error' => 'Failed to generate image from Gemini API'];
            }

            // Save the generated image
            $imagePath = $this->saveImage($imageData, $fileId);

            if (!$imagePath) {
                return ['error' => 'Failed to save generated image'];
            }

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
     * Call Gemini API to generate image
     *
     * @param string $prompt
     * @return string|null
     */
    private function callGeminiApi($prompt)
    {
        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/models/{$this->imageModel}:generateContent", [
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
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
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

                // Log the response structure for debugging
                Log::info('Gemini API Response Structure: ' . json_encode($data, JSON_PRETTY_PRINT));

                // Check if we have candidates
                if (isset($data['candidates'][0]['content']['parts'])) {
                    $parts = $data['candidates'][0]['content']['parts'];

                    // Look for image data in all parts
                    foreach ($parts as $part) {
                        if (isset($part['inlineData']['data'])) {
                            Log::info('Found image data in part');
                            return $part['inlineData']['data'];
                        }
                    }

                    // Check for text parts (for debugging)
                    foreach ($parts as $part) {
                        if (isset($part['text'])) {
                            Log::info('Gemini returned text: ' . $part['text']);
                        }
                    }
                }

                // Check for finish reason
                if (isset($data['candidates'][0]['finishReason'])) {
                    Log::info('Gemini finish reason: ' . $data['candidates'][0]['finishReason']);
                }
            } else {
                $errorData = $response->json();
                $errorMessage = 'Gemini API HTTP Error: ' . $response->status();

                if (isset($errorData['error']['message'])) {
                    $errorMessage .= ' - ' . $errorData['error']['message'];
                }

                Log::error($errorMessage);

                // Check if it's a quota error
                if ($response->status() === 429) {
                    Log::warning('Gemini API quota exceeded, will use fallback method');
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
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

