<?php

namespace App\Services;
 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AiChatLog; // Assuming you have this model
use App\Models\Course;    // Example model
use Illuminate\Support\Facades\Auth;

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
            // 1. First check if we can answer from database
            $dbResponse = $this->tryDatabaseResponse($message);
            if ($dbResponse) {
                return $dbResponse;
            }

            // 2. If not, use OpenRouter API
            $payload = [
                'model' => $model,
                'messages' => $this->buildMessageContext($message, $conversationHistory),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => $this->siteUrl,
                'X-Title' => $this->siteName,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)
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
            $aiResponse = $data['choices'][0]['message']['content'];

            // 3. Log the interaction
            $this->logInteraction($message, $aiResponse);

            return $aiResponse;

        } catch (\Exception $e) {
            Log::error('OpenRouter Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'Sorry, I encountered an error processing your request. Please try again later.';
        }
    }

    protected function tryDatabaseResponse(string $message): ?string
    {
        // Example: Check for course-related queries
        if (preg_match('/course|lesson|class/i', $message)) {
            $courses = Course::query()
                ->when(Auth::check(), function ($query) {
                    $query->whereHas('enrollments', function ($q) {
                        $q->where('student_id', Auth::id());
                    });
                })
                ->with('lessons')
                ->get();

            if ($courses->isNotEmpty()) {
                $response = "Here are your courses:\n";
                foreach ($courses as $course) {
                    $response .= "- {$course->title} ({$course->lessons->count()} lessons)\n";
                }
                return $response;
            }
        }

        // Add more database checks as needed
        // Example: Check assignments, grades, etc.

        return null;
    }

    protected function buildMessageContext(string $message, array $history): array
    {
        $context = array_merge($history, [
            ['role' => 'user', 'content' => $message]
        ]);

        // Optionally add system message with database context
        if (Auth::check()) {
            $user = Auth::user();
            array_unshift($context, [
                'role' => 'system',
                'content' => "You're assisting {$user->name}, a {$user->role} in our LMS system."
            ]);
        }

        return $context;
    }

    protected function logInteraction(string $userMessage, string $aiResponse): void
    {
        if (Auth::check()) {
            AiChatLog::create([
                'user_id' => Auth::id(),
                'message' => $userMessage,
                'is_ai' => false
            ]);

            AiChatLog::create([
                'user_id' => Auth::id(),
                'message' => $aiResponse,
                'is_ai' => true
            ]);
        }
    }
}