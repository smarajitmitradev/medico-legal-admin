<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ✅ Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
            'country_code'  => 'required',
        ]);

        $otp = rand(100000, 999999);

        $user = User::where('mobile_number', $request->mobile_number)->first();

        if (!$user) {
            $user = User::create([
                'user_id' => 'usr_' . uniqid(),
                'mobile_number' => $request->mobile_number,
                'country_code'  => $request->country_code,
                'otp' => $otp,
                'otp_expires_at' => now()->addSeconds(120)
            ]);
        } else {
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addSeconds(120)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'otp_sent' => true,
                'expires_in_seconds' => 120,
                'resend_after_seconds' => 30,
                'otp' => $otp
            ]
        ]);
    }

    // ✅ Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
            'country_code'  => 'required',
            'otp'           => 'required',
            'device_id'     => 'required'
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();

        if (!$user || $user->otp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
                'error' => ['code' => 'INVALID_OTP']
            ], 400);
        }

        if (now()->gt($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired',
                'error' => ['code' => 'OTP_EXPIRED']
            ], 400);
        }

        // Generate tokens
        $accessToken = auth('api')->login($user);
        $refreshToken = Str::random(64);

        $user->update([
            'otp' => null,
            'refresh_token' => $refreshToken,
            'device_id' => $request->device_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in_seconds' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'user_id' => $user->user_id,
                    'mobile_number' => $user->mobile_number,
                    'country_code' => $user->country_code,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'is_profile_complete' => (bool) $user->is_profile_complete,
                    'is_premium' => (bool) $user->is_premium,
                    'premium_expiry_date' => $user->premium_expiry_date
                ],
                'device' => [
                    'device_id' => $request->device_id,
                    'is_trusted' => true
                ]
            ]
        ]);
    }

    // ✅ Refresh Token
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
            'device_id' => 'required'
        ]);

        $user = User::where('refresh_token', $request->refresh_token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token'
            ], 401);
        }

        $newAccessToken = auth('api')->login($user);
        $newRefreshToken = Str::random(64);

        $user->update([
            'refresh_token' => $newRefreshToken
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'data' => [
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'Bearer',
                'expires_in_seconds' => auth('api')->factory()->getTTL() * 60
            ]
        ]);
    }

    // ✅ Get Profile
    public function getProfile()
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Profile fetched',
            'data' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'country_code' => $user->country_code,
                'user_type' => $user->user_type,
                'is_profile_complete' => (bool) $user->is_profile_complete,
                'is_premium' => (bool) $user->is_premium,
                'premium_expiry_date' => $user->premium_expiry_date,
                'device_id' => $user->device_id
            ]
        ]);
    }

    // ✅ Complete Profile
    public function completeProfile(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'user_type'  => 'required'
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'full_name'  => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'user_type'  => $request->user_type,
            'is_profile_complete' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile completed successfully',
            'data' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'user_type' => $user->user_type,
                'is_profile_complete' => true,
                'is_premium' => (bool) $user->is_premium,
                'premium_expiry_date' => $user->premium_expiry_date
            ]
        ]);
    }
}
