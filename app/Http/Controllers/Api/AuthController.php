<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Helpers\FcmHelper;

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
            'device_id'     => 'required',
            'fcm_token'     => 'nullable' // ✅ NEW
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

        // ✅ DEVICE CHECK (TAKEOVER FLOW)
        if ($user->device_id && $user->device_id !== $request->device_id) {

            $takeoverToken = Str::random(40);

            $user->update([
                'takeover_token' => $takeoverToken,
                'takeover_expires_at' => now()->addMinutes(5),

                // ✅ store requested device temp token (optional)
                'temp_fcm_token' => $request->fcm_token ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified. Confirmation required for new device.',
                'data' => [
                    'action_required' => 'confirm_device_takeover',
                    'takeover_token' => $takeoverToken,
                    'device' => [
                        'device_id' => $request->device_id,
                        'is_trusted' => false
                    ],
                    'user' => [
                        'user_id' => $user->user_id,
                        'mobile_number' => $user->mobile_number,
                        'country_code' => $user->country_code,
                        'is_profile_complete' => (bool) $user->is_profile_complete,
                        'is_premium' => (bool) $user->is_premium
                    ]
                ]
            ]);
        }

        // ✅ NORMAL LOGIN
        $accessToken = auth('api')->login($user);
        $refreshToken = Str::random(64);

        $user->update([
            'otp' => null,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_at' => now()->addDays(7),

            // ✅ Device tracking
            'device_id' => $request->device_id,
            'fcm_token' => $request->fcm_token ?? null,

            // ✅ clear takeover
            'takeover_token' => null,
            'takeover_expires_at' => null
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

        // ❌ Invalid token
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token',
                'error' => ['code' => 'INVALID_REFRESH_TOKEN']
            ], 401);
        }

        // ❌ Device mismatch
        if ($user->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Device mismatch. Please login again.',
                'error' => ['code' => 'DEVICE_MISMATCH']
            ], 401);
        }

        // ❌ Token expired
        if (!$user->refresh_token_expires_at || now()->gt($user->refresh_token_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token expired. Please login again.',
                'error' => ['code' => 'REFRESH_TOKEN_EXPIRED']
            ], 401);
        }

        // ✅ Generate new tokens
        $newAccessToken = auth('api')->login($user);
        $newRefreshToken = Str::random(64);

        $user->update([
            'refresh_token' => $newRefreshToken,
            'refresh_token_expires_at' => now()->addDays(7) // 🔥 reset expiry
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

    public function confirmDeviceTakeover(Request $request)
    {
        $request->validate([
            'takeover_token' => 'required',
            'device_id' => 'required',
            'fcm_token' => 'nullable' // ✅ NEW
        ]);

        $user = User::where('takeover_token', $request->takeover_token)->first();

        if (!$user || !$user->takeover_expires_at || now()->gt($user->takeover_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Takeover session expired. Please login again.',
                'error' => [
                    'code' => 'TAKEOVER_TOKEN_EXPIRED'
                ]
            ], 401);
        }

        // ✅ STORE OLD DEVICE INFO (VERY IMPORTANT)
        $oldDeviceId = $user->device_id;
        $oldFcmToken = $user->fcm_token;

        // Count old sessions
        $revokedSessions = $oldDeviceId ? 1 : 0;

        // Generate tokens
        $accessToken = auth('api')->login($user);
        $refreshToken = Str::random(64);

        // ✅ UPDATE USER WITH NEW DEVICE
        $user->update([
            'device_id' => $request->device_id,
            'fcm_token' => $request->fcm_token ?? null,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_at' => now()->addDays(7),
            'takeover_token' => null,
            'takeover_expires_at' => null
        ]);

        // ✅ SEND FORCE LOGOUT TO OLD DEVICE
        if ($oldFcmToken && $oldDeviceId !== $request->device_id) {

            FcmHelper::send(
                $oldFcmToken,
                'Logged Out',
                'Your account was logged in from another device.',
                [
                    'action' => 'force_logout',
                    'reason' => 'device_takeover'
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Device takeover confirmed. Previous device logged out.',
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
                ],
                'revoked_sessions_count' => $revokedSessions
            ]
        ]);
    }

    public function registerFcmToken(Request $request)
    {
        $request->validate([
            'token'      => 'required|string',
            'platform'   => 'required|string|in:android,ios,web',
            'app_id'     => 'required|string',
            'device_id'  => 'required|string',
            'user_id'    => 'nullable|string'
        ]);

        // ✅ If user_id is provided → validate device
        if ($request->user_id) {

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error' => ['code' => 'USER_NOT_FOUND']
                ], 404);
            }

            // ❌ Device mismatch check
            if ($user->device_id && $user->device_id !== $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device mismatch',
                    'error' => ['code' => 'DEVICE_MISMATCH']
                ], 401);
            }

            // ✅ Update user FCM + device info
            $user->update([
                'fcm_token' => $request->token,
                'device_id' => $request->device_id,
                'app_id' => $request->app_id,
                'platform' => $request->platform
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully',
                'data' => [
                    'user_id'   => $user->id,
                    'device_id' => $user->device_id,
                    'fcm_token' => $user->fcm_token
                ]
            ]);
        }

        // ✅ If no user_id → just acknowledge (guest/device level)
        return response()->json([
            'success' => true,
            'message' => 'FCM token registered (guest)',
            'data' => [
                'device_id' => $request->device_id,
                'fcm_token' => $request->token
            ]
        ]);
    }
}
