<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GroqAiService;

class AiChatController extends Controller
{
    protected $groqService;

    public function __construct(GroqAiService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        try {

            $reply = $this->groqService->chat($request->message);

            return response()->json([
                'status' => true,
                'reply' => $reply
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'reply' => 'AI service unavailable'
            ]);
        }
    }
}