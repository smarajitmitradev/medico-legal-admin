<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // =========================
    // 🔐 LOGIN FORM
    // =========================
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // =========================
    // 🔐 LOGIN + SEND OTP
    // =========================
    public function login(Request $request, OtpService $otpService)
    {
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {

            // 🚫 Check if locked
            if ($admin->otp_locked_until && now()->lt($admin->otp_locked_until)) {
                return back()->with('error', 'Account locked. Try again later.');
            }

            // ✅ Generate OTP
            $otp = rand(100000, 999999);

            // ✅ Store HASHED OTP + expiry
            $admin->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => Carbon::now()->addMinutes(5),
                'otp_locked_until' => null
            ]);

            // ✅ Send OTP
            $response = $otpService->sendOtp($admin->phone, $otp);

            if ($response['Status'] == 'Success') {
                Session::put('otp_admin_id', $admin->id);
                Session::forget(['otp_attempts', 'otp_last_sent']);
                Session::put('otp_last_sent', now());

                return redirect()->route('admin.otp.form');
            }

            return back()->with('error', 'Failed to send OTP');
        }

        return back()->with('error', 'Invalid credentials');
    }

    // =========================
    // 🚪 LOGOUT
    // =========================
    public function logout()
    {
        Session::flush();
        return redirect()->route('admin.login');
    }

    // =========================
    // 🔢 OTP FORM
    // =========================
    public function showOtpForm()
    {
        if (!Session::has('otp_admin_id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.otp');
    }

    // =========================
    // 🔐 VERIFY OTP
    // =========================
    public function verifyOtp(Request $request)
    {
        $adminId = Session::get('otp_admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login');
        }

        $admin = Admin::find($adminId);

        // 🚫 Check lock
        if ($admin->otp_locked_until && now()->lt($admin->otp_locked_until)) {
            return back()->with('error', 'Too many attempts. Try again later.');
        }

        // ✅ Attempt limit
        $attempts = Session::get('otp_attempts', 0);

        if ($attempts >= 5) {
            $admin->update([
                'otp_locked_until' => now()->addMinutes(10)
            ]);

            Session::forget('otp_attempts');

            return back()->with('error', 'Too many attempts. Account locked for 10 minutes.');
        }

        // ✅ Verify OTP
        if (
            $admin->otp &&
            Hash::check($request->otp, $admin->otp) &&
            now()->lte($admin->otp_expires_at)
        ) {
            // 🎉 SUCCESS
            Session::forget(['otp_admin_id', 'otp_attempts']);
            Session::put('admin_id', $admin->id);

            $admin->update([
                'otp' => null,
                'otp_expires_at' => null,
                'otp_locked_until' => null
            ]);

            return redirect()->route('admin.dashboard');
        }

        // ❌ Failed attempt
        Session::put('otp_attempts', $attempts + 1);

        return back()->with('error', 'Invalid or expired OTP');
    }

    // =========================
    // 🔁 RESEND OTP
    // =========================
    public function resendOtp(OtpService $otpService)
    {
        $adminId = Session::get('otp_admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login');
        }

        $admin = Admin::find($adminId);

        // 🚫 Block if locked
        if ($admin->otp_locked_until && now()->lt($admin->otp_locked_until)) {
            return back()->with('error', 'Account locked. Cannot resend OTP.');
        }

        // ⏱ Cooldown (30 sec)
        if (
            Session::has('otp_last_sent') &&
            now()->diffInSeconds(Session::get('otp_last_sent')) < 30
        ) {
            return back()->with('error', 'Please wait before requesting another OTP');
        }

        // ✅ Generate OTP
        $otp = rand(100000, 999999);

        // ✅ Store HASHED OTP
        $admin->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5)
        ]);

        // ✅ Send OTP
        $response = $otpService->sendOtp($admin->phone, $otp);

        if ($response['Status'] == 'Success') {
            Session::put('otp_last_sent', now());
            return back()->with('success', 'OTP resent successfully');
        }

        return back()->with('error', 'Failed to resend OTP');
    }
}
