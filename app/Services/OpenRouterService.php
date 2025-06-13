<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected $apiKey;
    protected $baseUrl = 'https://openrouter.ai/api/v1';
    protected $siteUrl;
    protected $siteName;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->siteUrl = config('services.openrouter.site_url');
        $this->siteName = config('services.openrouter.site_name');

        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenRouter API key is not configured');
        }
    }

    public function sendMessage($message, $conversationHistory = [], $model = 'deepseek/deepseek-r1:free')
    {
        try {
            $payload = [
                'model' => $model,
                'messages' => array_merge($conversationHistory, [
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ]),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => $this->siteUrl,
                'X-Title' => $this->siteName,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30) // Add timeout
             ->post($this->baseUrl . '/chat/completions', $payload);

            if (!$response->successful()) {
                Log::error('OpenRouter API Error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
                throw new \RuntimeException('API request failed with status: ' . $response->status());
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                Log::error('OpenRouter API Malformed Response', ['response' => $data]);
                throw new \RuntimeException('Unexpected API response structure');
            }

            return $data['choices'][0]['message']['content'];

        } catch (\Exception $e) {
            Log::error('OpenRouter Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'Sorry, I encountered an error processing your request. Please try again later.';
        }
    }
}