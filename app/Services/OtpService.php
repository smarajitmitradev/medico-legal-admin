<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OtpService
{
    protected $apiKey = "914650ac-3a78-11f1-9800-0200cd936042";

    public function sendOtp($mobile, $otp)
    {
        $url = "https://2factor.in/API/V1/{$this->apiKey}/SMS/{$mobile}/{$otp}/HEALTH_LOGIN_OTP";

        try {
            $response = Http::get($url);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'Status' => 'Error',
                'Message' => $e->getMessage()
            ];
        }
    }
}