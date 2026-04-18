<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class FcmHelper
{
    public static function send($token, $title, $body, $data = [])
    {
        $serviceAccount = json_decode(
            file_get_contents(storage_path('app/firebase/lawcription-f6592-firebase-adminsdk-fbsvc-8ce9e81946.json')),
            true
        );

        $jwt = self::generateJWT($serviceAccount);

        // 🔥 Get Access Token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $accessToken = $response['access_token'] ?? null;

        if (!$accessToken) return false;

        // 🔥 Send Notification
        $projectId = $serviceAccount['project_id'];

        $payload = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body" => $body
                ],
                "data" => $data
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);
        
        // Log Debug
        // Log::info('FCM Response:', [
        //     'response' => $result
        // ]);
        
        
        Log::info('FCM DEBUG', [
            'token' => $token,
            'title' => $title,
            'body' => $body,
            'payload' => $payload,
            'response' => $result
        ]);
        curl_close($ch);
        
        return json_decode($result, true);
    }

    private static function generateJWT($serviceAccount)
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);

        $now = time();

        $payload = json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = '';
        openssl_sign(
            $base64UrlHeader . "." . $base64UrlPayload,
            $signature,
            $serviceAccount['private_key'],
            'SHA256'
        );

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}