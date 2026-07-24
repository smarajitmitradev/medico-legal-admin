<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * ============================
     * SETTINGS PAGE
     * ============================
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * ============================
     * SAVE SETTINGS
     * ============================
     */
    public function update(Request $request)
    {
        // ============================
        // VALIDATION
        // ============================
        $request->validate([

            // GENERAL
            'app_name'            => 'nullable|string|max:255',
            'contact_email'       => 'nullable|email|max:255',
            'contact_phone'       => 'nullable|string|max:20',
            'timezone'            => 'nullable|string|max:100',

            // BRANDING
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'favicon'             => 'nullable|image|mimes:jpg,jpeg,png,ico|max:1024',

            // NOTIFICATION
            'fcm_key'             => 'nullable|string',
            'firebase_json'       => 'nullable|file|mimes:json|max:5120',
            'twofactor_api_key'   => 'nullable|string|max:255',
            'otp_template'        => 'nullable|string|max:255',
            'groq_api_key'        => 'nullable|string|max:255',

            // SECURITY
            'otp_expiry'          => 'nullable|numeric',
            'login_attempt_limit' => 'nullable|numeric',

            // SMTP
            'smtp_host'           => 'nullable|string|max:255',
            'smtp_port'           => 'nullable|string|max:20',
            'smtp_username'       => 'nullable|string|max:255',
            'smtp_password'       => 'nullable|string|max:255',

        ]);

        // ============================
        // NORMAL INPUTS
        // ============================
        $data = [

            // GENERAL
            'app_name'                  => $request->app_name,
            'contact_email'             => $request->contact_email,
            'contact_phone'             => $request->contact_phone,
            'timezone'                  => $request->timezone,

            // NOTIFICATION
            'fcm_key'                   => $request->fcm_key,
            'twofactor_api_key'         => $request->twofactor_api_key,
            'otp_template'              => $request->otp_template,
            'push_notification_enabled' => $request->push_notification_enabled ? 1 : 0,
            'notification_sound'        => $request->notification_sound ? 1 : 0,
            'auto_notification'         => $request->auto_notification ? 1 : 0,
            // AI SERVICE
            'groq_api_key'              => $request->groq_api_key,

            // SECURITY
            'otp_expiry'                => $request->otp_expiry,
            'login_attempt_limit'       => $request->login_attempt_limit,
            'maintenance_mode'          => $request->maintenance_mode ? 1 : 0,

            // SMTP
            'smtp_host'                 => $request->smtp_host,
            'smtp_port'                 => $request->smtp_port,
            'smtp_username'             => $request->smtp_username,
            'smtp_password'             => $request->smtp_password,
        ];

        // ============================
        // SAVE NORMAL SETTINGS
        // ============================
        foreach ($data as $key => $value) {

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // ============================
        // LOGO UPLOAD
        // ============================
        if ($request->hasFile('logo')) {

            $logo = $request->file('logo');

            $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();

            $logo->move(public_path('uploads/settings'), $logoName);

            Setting::updateOrCreate(
                ['key' => 'logo'],
                ['value' => $logoName]
            );
        }

        // ============================
        // FAVICON UPLOAD
        // ============================
        if ($request->hasFile('favicon')) {

            $favicon = $request->file('favicon');

            $faviconName = time() . '_favicon.' . $favicon->getClientOriginalExtension();

            $favicon->move(public_path('uploads/settings'), $faviconName);

            Setting::updateOrCreate(
                ['key' => 'favicon'],
                ['value' => $faviconName]
            );
        }

        // ============================
        // FIREBASE JSON UPLOAD
        // ============================
        if ($request->hasFile('firebase_json')) {

            $firebase = $request->file('firebase_json');

            $firebaseName = time() . '_firebase.json';

            $firebase->move(
                storage_path('app/firebase'),
                $firebaseName
            );

            Setting::updateOrCreate(
                ['key' => 'firebase_json'],
                ['value' => $firebaseName]
            );
        }

        return back()->with(
            'success',
            'Settings updated successfully'
        );
    }
}
