<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class GroqAiService
{
    /*
    |--------------------------------------------------------------------------
    | MODEL
    |--------------------------------------------------------------------------
    */
    private $model = 'llama-3.3-70b-versatile';



    /*
    |--------------------------------------------------------------------------
    | GET API KEY FROM DATABASE
    |--------------------------------------------------------------------------
    */
    private function getApiKey()
    {
        return Setting::where('key', 'groq_api_key')->value('value');
    }



    /*
    |--------------------------------------------------------------------------
    | CHAT FUNCTION
    |--------------------------------------------------------------------------
    */
    public function chat($message)
    {
        $apiKey = $this->getApiKey();

        if (!$apiKey) {
            return 'Groq API key not configured';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $this->model,

            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful AI assistant for admin dashboard.'
                ],
                [
                    'role'    => 'user',
                    'content' => $message
                ]
            ],

            'temperature' => 0.7,
            'max_tokens'  => 1024,
        ]);



        if ($response->successful()) {
            return $response['choices'][0]['message']['content'] ?? 'No response';
        }

        return $response->body() ?: 'Something went wrong';
    }
}