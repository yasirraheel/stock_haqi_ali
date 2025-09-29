<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiTextService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1';
    protected $textModel = 'gemini-2.0-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Generate movie description using Gemini AI
     *
     * @param string $movieTitle
     * @param string $genres
     * @param string $actors
     * @param string $directors
     * @return array
     */
    public function generateMovieDescription($movieTitle, $genres = '', $actors = '', $directors = '')
    {
        try {
            $prompt = $this->createDescriptionPrompt($movieTitle, $genres, $actors, $directors);

            $response = $this->callGeminiApi($prompt);

            if (!$response) {
                Log::warning('Gemini API returned null response, using fallback');
                $fallbackDescription = $this->generateFallbackDescription($movieTitle, $genres, $actors, $directors);

                return [
                    'success' => 'Description generated successfully (using fallback)',
                    'description' => $fallbackDescription
                ];
            }

            return [
                'success' => 'Description generated successfully',
                'description' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Text Generation Error: ' . $e->getMessage());

            // Fallback to basic description generation
            $fallbackDescription = $this->generateFallbackDescription($movieTitle, $genres, $actors, $directors);

            return [
                'success' => 'Description generated successfully (using fallback)',
                'description' => $fallbackDescription
            ];
        }
    }

    /**
     * Create a detailed prompt for movie description generation
     *
     * @param string $movieTitle
     * @param string $genres
     * @param string $actors
     * @param string $directors
     * @return string
     */
    private function createDescriptionPrompt($movieTitle, $genres = '', $actors = '', $directors = '')
    {
        $prompt = "Write a compelling movie description for a film titled '{$movieTitle}'.";

        if (!empty($genres)) {
            $prompt .= " Genre(s): {$genres}.";
        }

        if (!empty($actors)) {
            $prompt .= " Starring: {$actors}.";
        }

        if (!empty($directors)) {
            $prompt .= " Directed by: {$directors}.";
        }

        $prompt .= " The description should be engaging, professional, and suitable for a movie database. ";
        $prompt .= "Include plot elements, themes, and what makes this movie special. ";
        $prompt .= "Keep it between 150-300 words. ";
        $prompt .= "Write in a style that would attract viewers and make them want to watch the movie. ";
        $prompt .= "IMPORTANT: Format your response using HTML tags. Use <h3> for the movie title, <p> for paragraphs, <strong> for bold text, <em> for italic text, and <br> for line breaks. Do not use markdown formatting like **bold** or *italic*. Do not wrap your response in code blocks or markdown formatting. Return only the HTML content directly.";

        return $prompt;
    }

    /**
     * Call Gemini API to generate text
     *
     * @param string $prompt
     * @return string|null
     */
    private function callGeminiApi($prompt)
    {
        try {
            Log::info('Calling Gemini API for text generation', [
                'url' => "{$this->baseUrl}/models/{$this->textModel}:generateContent",
                'model' => $this->textModel,
                'prompt_length' => strlen($prompt)
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/models/{$this->textModel}:generateContent?key={$this->apiKey}", [
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

                Log::info('Gemini API Response received', ['response_structure' => array_keys($data)]);

                // Extract text from response
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $generatedText = $data['candidates'][0]['content']['parts'][0]['text'];
                    Log::info('Successfully generated text from Gemini API', ['text_length' => strlen($generatedText)]);

                    // Convert any remaining markdown to HTML
                    $generatedText = $this->convertMarkdownToHtml($generatedText);

                    return $generatedText;
                }

                Log::error('Gemini API Response Structure: ' . json_encode($data, JSON_PRETTY_PRINT));
            } else {
                $errorData = $response->json();
                $errorMessage = 'Gemini API HTTP Error: ' . $response->status();

                if (isset($errorData['error']['message'])) {
                    $errorMessage .= ' - ' . $errorData['error']['message'];
                }

                Log::error($errorMessage);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a fallback description when Gemini API is not available
     *
     * @param string $movieTitle
     * @param string $genres
     * @param string $actors
     * @param string $directors
     * @return string
     */
    private function generateFallbackDescription($movieTitle, $genres = '', $actors = '', $directors = '')
    {
        $description = "<h3>{$movieTitle}</h3>\n\n";

        if (!empty($genres)) {
            $description .= "<p><strong>Genre:</strong> {$genres}</p>\n";
        }

        if (!empty($directors)) {
            $description .= "<p><strong>Directed by:</strong> {$directors}</p>\n";
        }

        if (!empty($actors)) {
            $description .= "<p><strong>Starring:</strong> {$actors}</p>\n";
        }

        $description .= "\n<p>An engaging and captivating film that promises to deliver an unforgettable viewing experience. ";
        $description .= "This movie combines compelling storytelling with outstanding performances to create a memorable cinematic journey. ";
        $description .= "Perfect for audiences looking for quality entertainment and thought-provoking content.</p>\n\n";

        $description .= "<p><em>Note: This description was generated automatically. You can edit it to better reflect the actual content of the movie.</em></p>";

        return $description;
    }

    /**
     * Convert markdown formatting to HTML
     *
     * @param string $text
     * @return string
     */
    private function convertMarkdownToHtml($text)
    {
        // Remove markdown code blocks
        $text = preg_replace('/```html\s*/', '', $text);
        $text = preg_replace('/```\s*$/', '', $text);

        // Convert **bold** to <strong>bold</strong>
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);

        // Convert *italic* to <em>italic</em>
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);

        // Convert line breaks to <br> tags
        $text = nl2br($text);

        // Clean up excessive line breaks
        $text = preg_replace('/(<br\s*\/?>){3,}/', '<br><br>', $text);

        // Ensure proper paragraph structure
        $text = preg_replace('/(<br\s*\/?>){2,}/', '</p><p>', $text);

        // Wrap in paragraph tags if not already wrapped
        if (!preg_match('/^<[ph]/', trim($text))) {
            $text = '<p>' . $text . '</p>';
        }

        return $text;
    }
}
