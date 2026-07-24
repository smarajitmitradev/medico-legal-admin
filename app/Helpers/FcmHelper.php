<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class FcmHelper
{
    public static function send($token, $title, $body, $data = [])
    {
        try {

            // =====================================
            // CHECK PUSH NOTIFICATION ENABLED
            // =====================================
            $pushEnabled = Setting::where('key', 'push_notification_enabled')
                ->value('value');

            if (!$pushEnabled) {

                Log::info('FCM STOPPED: Push notification disabled from admin');

                return false;
            }

            // =====================================
            // GET FIREBASE JSON FILE NAME
            // =====================================
            $firebaseFile = Setting::where('key', 'firebase_json')
                ->value('value');

            if (!$firebaseFile) {

                Log::error('FCM ERROR: Firebase JSON setting missing');

                return false;
            }

            // =====================================
            // FIREBASE FILE PATH
            // =====================================
            $firebasePath = storage_path(
                'app/firebase/' . $firebaseFile
            );

            if (!file_exists($firebasePath)) {

                Log::error('FCM ERROR: Firebase JSON file not found', [
                    'path' => $firebasePath
                ]);

                return false;
            }

            // =====================================
            // LOAD FIREBASE JSON
            // =====================================
            $serviceAccount = json_decode(
                file_get_contents($firebasePath),
                true
            );

            if (!$serviceAccount) {

                Log::error('FCM ERROR: Invalid Firebase JSON');

                return false;
            }

            // =====================================
            // GENERATE JWT TOKEN
            // =====================================
            $jwt = self::generateJWT($serviceAccount);

            // =====================================
            // GET ACCESS TOKEN
            // =====================================
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt
            ]));

            $tokenResponse = curl_exec($ch);

            $tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $tokenCurlError = curl_error($ch);

            curl_close($ch);

            Log::info('FCM ACCESS TOKEN RESPONSE', [
                'http_code' => $tokenHttpCode,
                'response'  => $tokenResponse,
                'curl_error' => $tokenCurlError
            ]);

            $response = json_decode($tokenResponse, true);

            $accessToken = $response['access_token'] ?? null;

            if (!$accessToken) {

                Log::error('FCM ERROR: Access token generation failed', [
                    'response' => $response
                ]);

                return false;
            }

            // =====================================
            // PROJECT ID
            // =====================================
            $projectId = $serviceAccount['project_id'];

            // =====================================
            // CHECK NOTIFICATION SOUND
            // =====================================
            $notificationSound = Setting::where('key', 'notification_sound')
                ->value('value');

            // =====================================
            // FCM PAYLOAD
            // =====================================
            $payload = [
                "message" => [

                    "token" => $token,

                    "notification" => [
                        "title" => $title,
                        "body"  => $body
                    ],

                    "data" => (array) $data,

                    "android" => [
                        "priority" => "high",
                        "notification" => [
                            "sound" => $notificationSound ? "default" : null
                        ]
                    ],

                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "sound" => $notificationSound ? "default" : null
                            ]
                        ]
                    ]
                ]
            ];

            // REMOVE NULL VALUES
            $payload = json_decode(
                json_encode($payload),
                true
            );

            // =====================================
            // SEND FCM REQUEST
            // =====================================
            $ch = curl_init();

            curl_setopt(
                $ch,
                CURLOPT_URL,
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send"
            );

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $accessToken,
                "Content-Type: application/json"
            ]);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($payload)
            );

            $result = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $curlError = curl_error($ch);

            curl_close($ch);

            // =====================================
            // FINAL LOG
            // =====================================
            Log::info('FCM FINAL RESPONSE', [

                'http_code' => $httpCode,

                'curl_error' => $curlError,

                'firebase_file' => $firebaseFile,

                'project_id' => $projectId,

                'token' => $token,

                'title' => $title,

                'body' => $body,

                'payload' => $payload,

                'response' => $result
            ]);

            return json_decode($result, true);

        } catch (\Exception $e) {

            Log::error('FCM EXCEPTION ERROR', [

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile(),

                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    // =====================================
    // GENERATE JWT
    // =====================================
    private static function generateJWT($serviceAccount)
    {
        $header = json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]);

        $now = time();

        $payload = json_encode([

            'iss'   => $serviceAccount['client_email'],

            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',

            'aud'   => 'https://oauth2.googleapis.com/token',

            'iat'   => $now,

            'exp'   => $now + 3600
        ]);

        $base64UrlHeader = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($header)
        );

        $base64UrlPayload = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($payload)
        );

        $signature = '';

        openssl_sign(
            $base64UrlHeader . "." . $base64UrlPayload,
            $signature,
            $serviceAccount['private_key'],
            'SHA256'
        );

        $base64UrlSignature = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($signature)
        );

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}