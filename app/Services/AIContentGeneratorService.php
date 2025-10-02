<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIContentGeneratorService
{
    private string $apiUrl;

    private string $apiKey;

    private string $model;

    public function __construct()
    {
        $this->apiUrl = config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->apiKey = config('services.openai.api_key') ?? 'test-key';
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Check if current user can access AI features
     */
    public function canAccessAI(): bool
    {
        // Check if AI is restricted to admin only
        $adminOnly = config('app.ai_admin_only', true);

        if (! $adminOnly) {
            return true; // AI is available for all users
        }

        // Check if user is authenticated and has admin role
        $user = auth()->user();

        if (! $user) {
            return false; // User not authenticated
        }

        // Check if user has admin role using Spatie permissions
        return $user->isAdmin();
    }

    /**
     * Generate coding tutorial content using AI
     */
    public function generateContent(array $parameters): array
    {
        // Check permission first
        if (! $this->canAccessAI()) {
            Log::warning('AI Access Denied', [
                'user_id' => auth()->id(),
                'user_roles' => auth()->user()?->getRoleNames(),
                'is_admin' => auth()->user()?->isAdmin(),
                'ai_admin_only' => config('app.ai_admin_only'),
            ]);

            return [
                'success' => false,
                'error' => 'Akses ditolak. Fitur AI hanya tersedia untuk admin.',
            ];
        }

        $prompt = $this->buildPrompt($parameters);

        // Log request details
        Log::info('OpenAI API Request Started', [
            'api_url' => $this->apiUrl,
            'model' => $this->model,
            'api_key_set' => ! empty($this->apiKey) && $this->apiKey !== 'test-key',
            'prompt_length' => strlen($prompt),
            'parameters' => $parameters,
            'user_id' => auth()->id(),
            'user_roles' => auth()->user()?->getRoleNames(),
            'is_admin' => auth()->user()?->isAdmin(),
        ]);

        $requestPayload = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->getSystemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => 4000,
            'temperature' => 0.7,
            'top_p' => 0.9,
            'frequency_penalty' => 0.1,
            'presence_penalty' => 0.1,
        ];

        Log::info('OpenAI Request Payload', ['payload' => $requestPayload]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, $requestPayload);

            Log::info('OpenAI API Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'response_size' => strlen($response->body()),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $content = $responseData['choices'][0]['message']['content'] ?? '';

                Log::info('OpenAI API Success', [
                    'content_length' => strlen($content),
                    'usage' => $responseData['usage'] ?? null,
                ]);

                return [
                    'success' => true,
                    'content' => $content,
                    'usage' => $responseData['usage'] ?? null,
                ];
            }

            $errorBody = $response->body();
            $errorMessage = 'API Error: ' . $response->status() . ' - ' . $errorBody;

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'response_body' => $errorBody,
                'headers' => $response->headers(),
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        } catch (\Exception $e) {
            $errorMessage = 'Network Error: ' . $e->getMessage();

            Log::error('OpenAI API Exception', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'api_url' => $this->apiUrl,
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        }
    }

    /**
     * Build the main prompt for content generation
     */
    private function buildPrompt(array $parameters): string
    {
        $prompt = "Buatkan artikel tutorial coding dengan detail berikut:\n\n";

        if (! empty($parameters['topic'])) {
            $prompt .= "**Topik**: {$parameters['topic']}\n";
        }

        if (! empty($parameters['category'])) {
            $prompt .= "**Kategori**: {$parameters['category']}\n";
        }

        if (! empty($parameters['difficulty'])) {
            $prompt .= "**Tingkat Kesulitan**: {$parameters['difficulty']}\n";
        }

        if (! empty($parameters['target_audience'])) {
            $prompt .= "**Target Pembaca**: {$parameters['target_audience']}\n";
        }

        if (! empty($parameters['programming_language'])) {
            $prompt .= "**Bahasa Pemrograman**: {$parameters['programming_language']}\n";
        }

        if (! empty($parameters['framework_tools'])) {
            $prompt .= "**Framework/Tools**: {$parameters['framework_tools']}\n";
        }

        if (! empty($parameters['article_length'])) {
            $prompt .= "**Panjang Artikel**: {$parameters['article_length']}\n";
        }

        if (! empty($parameters['key_points'])) {
            $prompt .= "**Poin-poin Kunci yang Harus Dicakup**:\n{$parameters['key_points']}\n";
        }

        if (! empty($parameters['additional_requirements'])) {
            $prompt .= "**Persyaratan Tambahan**:\n{$parameters['additional_requirements']}\n";
        }

        return $prompt;
    }

    /**
     * Get system prompt for AI behavior
     */
    private function getSystemPrompt(): string
    {
        return 'Kamu adalah seorang penulis tutorial coding yang berpengalaman dan ahli dalam menjelaskan konsep pemrograman dengan cara yang mudah dimengerti.

                Karakteristik tulisanmu:
                - Bahasa yang ramah, tidak terlalu formal, tapi tetap profesional
                - Narasi yang menarik dan engaging
                - Penjelasan step-by-step yang sistematis dan terstruktur
                - Contoh kode yang jelas dan mudah dipahami
                - Tips dan best practices yang berguna
                - Troubleshooting untuk masalah umum

                Format artikel yang harus kamu buat:
                1. **Pengenalan** - Jelaskan topik dan manfaatnya dengan menarik
                2. **Prerequisites** - Apa yang perlu disiapkan sebelum memulai
                3. **Langkah-langkah Tutorial** - Tutorial step-by-step yang detail
                4. **Penjelasan Kode** - Jelaskan setiap bagian kode penting
                5. **Tips & Best Practices** - Saran untuk optimasi dan keamanan
                6. **Troubleshooting** - Masalah umum dan solusinya
                7. **Kesimpulan** - Ringkasan dan langkah selanjutnya

                Gunakan format HTML untuk struktur artikel dengan heading (h2, h3), paragraph, code blocks dengan tag <pre><code>, list, dan formatting lainnya sesuai kebutuhan.

                Pastikan artikel mudah diikuti oleh pembaca sesuai dengan target audience yang ditentukan.';
    }

    /**
     * Generate title suggestions based on topic
     */
    public function generateTitleSuggestions(string $topic, string $category = ''): array
    {
        // Check permission first
        if (! $this->canAccessAI()) {
            Log::warning('AI Title Generation Access Denied', [
                'user_id' => auth()->id(),
                'user_roles' => auth()->user()?->getRoleNames(),
                'is_admin' => auth()->user()?->isAdmin(),
                'ai_admin_only' => config('app.ai_admin_only'),
            ]);

            return [
                'success' => false,
                'error' => 'Akses ditolak. Fitur AI hanya tersedia untuk admin.',
            ];
        }

        $prompt = "Berikan 5 ide judul artikel tutorial coding yang menarik untuk topik: {$topic}";
        if ($category) {
            $prompt .= " dalam kategori {$category}";
        }
        $prompt .= '. Judul harus SEO-friendly, menarik, dan tidak lebih dari 8 kata.';

        Log::info('OpenAI Title Generation Request', [
            'topic' => $topic,
            'category' => $category,
            'prompt' => $prompt,
            'api_key_set' => ! empty($this->apiKey) && $this->apiKey !== 'test-key',
            'user_id' => auth()->id(),
            'user_roles' => auth()->user()?->getRoleNames(),
            'is_admin' => auth()->user()?->isAdmin(),
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 300,
                'temperature' => 0.8,
            ]);

            Log::info('OpenAI Title Generation Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');

                Log::info('OpenAI Title Generation Success', [
                    'suggestions_length' => strlen($content),
                ]);

                return [
                    'success' => true,
                    'suggestions' => $content,
                ];
            }

            $errorMessage = 'Failed to generate title suggestions: ' . $response->status() . ' - ' . $response->body();

            Log::error('OpenAI Title Generation Error', [
                'status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Title Generation Exception', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
