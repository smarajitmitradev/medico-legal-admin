<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function sendOtp($mobile, $otp)
    {
        try {

            // =====================================
            // GET API KEY FROM SETTINGS
            // =====================================
            $apiKey = Setting::where('key', 'twofactor_api_key')
                ->value('value');

            if (!$apiKey) {

                Log::error('OTP ERROR: 2Factor API key missing');

                return [
                    'Status' => 'Error',
                    'Message' => '2Factor API key missing'
                ];
            }

            // =====================================
            // OTP TEMPLATE
            // =====================================
            // $template = "HEALTH_LOGIN_OTP";
            $template = Setting::where('key', 'otp_template')
                ->value('value');
            $template = $template ?: 'HEALTH_LOGIN_OTP';

            // =====================================
            // API URL
            // =====================================
            $url = "https://2factor.in/API/V1/{$apiKey}/SMS/{$mobile}/{$otp}/{$template}";

            Log::info('OTP REQUEST STARTED', [

                'mobile' => $mobile,

                'otp' => $otp,

                'template' => $template,

                'url' => $url
            ]);

            // =====================================
            // SEND REQUEST
            // =====================================
            $response = Http::timeout(30)->get($url);

            $responseBody = $response->json();

            // =====================================
            // SUCCESS LOG
            // =====================================
            Log::info('OTP RESPONSE', [

                'mobile' => $mobile,

                'status_code' => $response->status(),

                'response' => $responseBody
            ]);

            return $responseBody;
        } catch (\Exception $e) {

            // =====================================
            // ERROR LOG
            // =====================================
            Log::error('OTP EXCEPTION ERROR', [

                'mobile' => $mobile,

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile(),

                'trace' => $e->getTraceAsString()
            ]);

            return [

                'Status' => 'Error',

                'Message' => $e->getMessage()
            ];
        }
    }
}
