@extends('admin.layout.master')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

<style>
    /* GOOGLE FONT */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    /* PAGE */
    .settings-page {
        min-height: 100vh;

        background:
            radial-gradient(circle at top left, rgba(99, 102, 241, 0.12), transparent 30%),
            radial-gradient(circle at bottom right, rgba(168, 85, 247, 0.10), transparent 30%),
            linear-gradient(135deg, #f8fafc, #eef2ff, #f5f3ff);

        padding-bottom: 40px;
    }

    /* MAIN CARD */
    .settings-card {
        background: rgba(255, 255, 255, 0.75);

        backdrop-filter: blur(18px);

        border-radius: 32px;

        border: 1px solid rgba(255, 255, 255, 0.4);

        overflow: hidden;

        box-shadow:
            0 20px 60px rgba(15, 23, 42, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    /* SIDEBAR */
    .settings-sidebar {
        background:
            linear-gradient(180deg, #ffffff, #f8fafc);

        min-height: 850px;

        border-right: 1px solid rgba(226, 232, 240, 0.8);
    }

    /* SETTINGS TITLE */
    .settings-title {
        font-size: 28px;
        font-weight: 800;
        color: #111827;
    }

    .settings-subtitle {
        color: #64748b;
        font-size: 14px;
    }

    /* NAV LINK */
    .settings-sidebar .nav-link {
        position: relative;

        display: flex;
        align-items: center;
        gap: 14px;

        border-radius: 18px;

        padding: 15px 18px;

        font-size: 15px;
        font-weight: 600;

        color: #475569;

        transition: all 0.35s ease;

        overflow: hidden;
    }

    .settings-sidebar .nav-link i {
        font-size: 18px;
    }

    .settings-sidebar .nav-link:hover {
        transform: translateX(4px);

        background: #eef2ff;

        color: #4f46e5;
    }

    /* ACTIVE TAB */
    .settings-sidebar .nav-link.active {
        background:
            linear-gradient(135deg, #6366f1, #8b5cf6);

        color: white;

        box-shadow:
            0 12px 24px rgba(99, 102, 241, 0.28);
    }

    /* CONTENT */
    .settings-content {
        background: transparent;
    }

    /* SECTION */
    .section-card {
        background: rgba(255, 255, 255, 0.72);

        backdrop-filter: blur(12px);

        border-radius: 28px;

        padding: 28px;

        border: 1px solid rgba(226, 232, 240, 0.7);

        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.04);
    }

    /* HEADINGS */
    .section-title {
        font-size: 32px;
        font-weight: 800;

        color: #0f172a;

        margin-bottom: 8px;
    }

    .section-subtitle {
        color: #64748b;
        font-size: 15px;

        margin-bottom: 35px;
    }

    /* LABEL */
    .form-label {
        font-size: 14px;
        font-weight: 700;

        color: #334155;

        margin-bottom: 10px;
    }

    /* INPUTS */
    .form-control,
    .form-select,
    textarea {
        border: 1px solid #e2e8f0 !important;

        border-radius: 18px !important;

        min-height: 56px;

        padding-left: 18px;
        padding-right: 18px;

        font-size: 15px;

        background: rgba(255, 255, 255, 0.85) !important;

        transition: all 0.3s ease;

        box-shadow: none !important;
    }

    /* TEXTAREA */
    textarea.form-control {
        min-height: 140px;
        padding-top: 16px;
    }

    /* INPUT FOCUS */
    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #6366f1 !important;

        background: #fff !important;

        transform: translateY(-1px);

        box-shadow:
            0 0 0 5px rgba(99, 102, 241, 0.10) !important;
    }

    /* LOGO BOX */
    .logo-box {
        width: 120px;
        height: 120px;

        border-radius: 28px;

        background:
            linear-gradient(135deg, #f8fafc, #eef2ff);

        border: 2px dashed #cbd5e1;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;

        transition: 0.3s;
    }

    .logo-box:hover {
        transform: scale(1.03);

        border-color: #6366f1;
    }

    .logo-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* SWITCH */
    .form-check-input {
        width: 58px;
        height: 30px;

        cursor: pointer;

        border-radius: 50px !important;

        box-shadow: none !important;
    }

    .form-check-input:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }

    /* SWITCH CARD */
    .switch-card {
        background:
            linear-gradient(135deg, #ffffff, #f8fafc);

        border: 1px solid #e2e8f0;

        border-radius: 22px;

        padding: 20px 24px;

        transition: 0.3s;
    }

    .switch-card:hover {
        transform: translateY(-2px);

        box-shadow:
            0 10px 25px rgba(15, 23, 42, 0.06);
    }

    /* SAVE BUTTON */
    .save-btn {
        border: none;

        background:
            linear-gradient(135deg, #6366f1, #8b5cf6);

        color: white;

        border-radius: 20px;

        padding: 16px 36px;

        font-size: 15px;
        font-weight: 700;

        transition: all 0.35s ease;

        box-shadow:
            0 16px 35px rgba(99, 102, 241, 0.30);
    }

    .save-btn:hover {
        transform: translateY(-3px) scale(1.02);

        color: white;

        box-shadow:
            0 22px 45px rgba(99, 102, 241, 0.38);
    }

    /* SMALL BADGE */
    .custom-badge {
        background:
            linear-gradient(135deg, #dcfce7, #bbf7d0);

        color: #166534;

        padding: 8px 14px;

        border-radius: 999px;

        font-size: 12px;
        font-weight: 700;
    }

    /* TAB ANIMATION */
    .tab-pane {
        animation: fadeSlide 0.4s ease;
    }

    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* RESPONSIVE */
    @media(max-width:991px) {

        .settings-sidebar {
            min-height: auto;
        }

        .section-title {
            font-size: 24px;
        }

    }


    .secure-input-group {
        position: relative;
    }

    .secure-input-group input,
    .secure-input-group textarea {
        padding-right: 80px;
    }

    .secure-actions {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        display: flex;
        gap: 10px;
        z-index: 5;
    }

    .secure-btn {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        border: none;
        background: #f1f5f9;
        color: #334155;
        transition: 0.3s;
    }

    .secure-btn:hover {
        background: #2563eb;
        color: #fff;
    }

    .secure-btn.active {
        background: #2563eb;
        color: #fff;
    }
</style>

<div class="container-fluid settings-page py-4">

    <div class="settings-card overflow-hidden">

        <div class="row g-0">

            <!-- SIDEBAR -->
            <div class="col-lg-3">

                <div class="settings-sidebar p-4">

                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-gear me-2"></i>
                        Settings
                    </h4>

                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">

                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#general">

                            <i class="fa-solid fa-sliders"></i>
                            General
                        </button>

                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#branding">

                            <i class="fa-solid fa-image"></i>
                            Branding
                        </button>

                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#notification">

                            <i class="fa-solid fa-bell"></i>
                            Notifications
                        </button>

                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security">

                            <i class="fa-solid fa-shield-halved"></i>
                            Security
                        </button>

                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#smtp">

                            <i class="fa-solid fa-envelope"></i>
                            SMTP
                        </button>

                    </div>

                </div>

            </div>

            <!-- CONTENT -->
            <div class="col-lg-9">

                <div class="p-5">

                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="tab-content">

                            <!-- GENERAL -->
                            <div class="tab-pane fade show active" id="general">

                                <h2 class="section-title">General Settings</h2>

                                <div class="row g-4">

                                    <div class="col-md-6">
                                        <label class="form-label">App Name</label>

                                        <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Email</label>

                                        <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Phone</label>

                                        <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Timezone</label>

                                        <select name="timezone" class="form-select">

                                            <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>
                                                Asia/Kolkata
                                            </option>

                                        </select>
                                    </div>

                                </div>

                            </div>

                            <!-- BRANDING -->
                            <div class="tab-pane fade" id="branding">

                                <h2 class="section-title">Branding Settings</h2>

                                <div class="row g-4">

                                    <div class="col-md-6">

                                        <label class="form-label">App Logo</label>

                                        <div class="d-flex align-items-center gap-4">

                                            <div class="logo-box">

                                                @if(!empty($settings['logo']))
                                                <img src="{{ asset('uploads/settings/'.$settings['logo']) }}">
                                                @endif

                                            </div>

                                            <input type="file" name="logo" class="form-control">

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">Favicon</label>

                                        <input type="file" name="favicon" class="form-control">

                                    </div>

                                </div>

                            </div>

                            <!-- NOTIFICATION -->
                            <!-- NOTIFICATION -->
                            <div class="tab-pane fade" id="notification">

                                <div class="section-card">

                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">

                                        <div>

                                            <h2 class="section-title mb-1">
                                                Notification Settings
                                            </h2>

                                            <p class="section-subtitle mb-0">
                                                Configure Firebase push notification service and controls.
                                            </p>

                                        </div>

                                        <span class="custom-badge">
                                            LIVE CONFIG
                                        </span>

                                    </div>

                                    <!-- FCM KEY -->
                                    <!-- FCM KEY -->
                                    <div class="mb-4">

                                        <label class="form-label">
                                            Firebase FCM Server Key
                                        </label>

                                        <div class="secure-input-group">

                                            <textarea name="fcm_key" id="fcm_key" class="form-control secure-field" rows="6" readonly data-real="{{ $settings['fcm_key'] ?? '' }}">***********************</textarea>

                                            <div class="secure-actions">

                                                <!-- SHOW -->
                                                <button type="button" class="secure-btn" onclick="toggleView('fcm_key', this)">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>

                                                <!-- EDIT -->
                                                <button type="button" class="secure-btn" onclick="toggleEdit('fcm_key', this)">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>

                                            </div>

                                        </div>

                                        <small class="text-muted mt-2 d-block">
                                            Used for sending push notifications to mobile devices.
                                        </small>

                                    </div>

                                    <div class="row g-4">

                                        <!-- FIREBASE JSON -->
                                        <div class="col-md-12">

                                            <label class="form-label fw-semibold">
                                                Firebase Service JSON File
                                            </label>

                                            <input type="file" name="firebase_json" class="form-control">

                                            @if(!empty($settings['firebase_json']))
                                            <small class="text-success d-block mt-2">
                                                Current File:
                                                {{ $settings['firebase_json'] }}
                                            </small>
                                            @endif

                                            <small class="text-muted d-block mt-2">
                                                Upload Firebase Admin SDK JSON file downloaded from Firebase Console.
                                            </small>

                                        </div>

                                        <!-- 2FACTOR API -->
                                        <div class="col-md-12">

                                            <label class="form-label fw-semibold">
                                                2Factor API Key (SMS Gateway API)
                                            </label>

                                            <div class="secure-input-group">

                                                <input type="password" name="twofactor_api_key" id="twofactor_api_key" class="form-control secure-field" readonly value="{{ $settings['twofactor_api_key'] ?? '' }}">

                                                <div class="secure-actions">

                                                    <button type="button" class="secure-btn" onclick="togglePassword('twofactor_api_key', this)">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>

                                                    <button type="button" class="secure-btn" onclick="toggleEdit('twofactor_api_key', this)">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>

                                                </div>

                                            </div>

                                            <small class="text-muted d-block mt-2">
                                                Used for sending OTP SMS to mobile numbers.
                                            </small>

                                        </div>

                                        <!-- OTP TEMPLATE -->
                                        <div class="col-md-12">

                                            <label class="form-label fw-semibold">
                                                OTP SMS Template Name
                                            </label>

                                            <div class="secure-input-group">

                                                <input type="password" name="otp_template" id="otp_template" class="form-control secure-field" readonly value="{{ $settings['otp_template'] ?? 'HEALTH_LOGIN_OTP' }}">

                                                <div class="secure-actions">

                                                    <button type="button" class="secure-btn" onclick="togglePassword('otp_template', this)">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>

                                                    <button type="button" class="secure-btn" onclick="toggleEdit('otp_template', this)">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>

                                                </div>

                                            </div>

                                            <small class="text-muted d-block mt-2">
                                                Template name configured inside your 2Factor account.
                                            </small>

                                        </div>

                                    </div>

                                    <!-- ENABLE PUSH -->
                                    <div class="switch-card d-flex justify-content-between align-items-center">

                                        <div>

                                            <h6 class="fw-bold mb-1">
                                                Enable Push Notifications
                                            </h6>

                                            <small class="text-muted">
                                                Allow system to send real-time notifications to app users.
                                            </small>

                                        </div>

                                        <div class="form-check form-switch m-0">

                                            <input class="form-check-input" type="checkbox" name="push_notification_enabled" value="1" {{ ($settings['push_notification_enabled'] ?? 0) ? 'checked' : '' }}>

                                        </div>

                                    </div>

                                    <!-- SOUND -->
                                    <div class="switch-card d-flex justify-content-between align-items-center mt-4">

                                        <div>

                                            <h6 class="fw-bold mb-1">
                                                Notification Sound
                                            </h6>

                                            <small class="text-muted">
                                                Play notification sound on user devices.
                                            </small>

                                        </div>

                                        <div class="form-check form-switch m-0">

                                            <input class="form-check-input" type="checkbox" name="notification_sound" value="1" {{ ($settings['notification_sound'] ?? 1) ? 'checked' : '' }}>

                                        </div>

                                    </div>

                                    <!-- AUTO SEND -->
                                    <div class="switch-card d-flex justify-content-between align-items-center mt-4">

                                        <div>

                                            <h6 class="fw-bold mb-1">
                                                Auto Send Notifications
                                            </h6>

                                            <small class="text-muted">
                                                Automatically send notifications for new content updates.
                                            </small>

                                        </div>

                                        <div class="form-check form-switch m-0">

                                            <input class="form-check-input" type="checkbox" name="auto_notification" value="1" {{ ($settings['auto_notification'] ?? 1) ? 'checked' : '' }}>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- SECURITY -->
                            <div class="tab-pane fade" id="security">

                                <h2 class="section-title">Security Settings</h2>

                                <div class="row g-4">

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            OTP Expiry (Minutes)
                                        </label>

                                        <input type="number" name="otp_expiry" class="form-control" value="{{ $settings['otp_expiry'] ?? 5 }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Login Attempt Limit
                                        </label>

                                        <input type="number" name="login_attempt_limit" class="form-control" value="{{ $settings['login_attempt_limit'] ?? 5 }}">

                                    </div>

                                </div>

                                <div class="mt-4">

                                    <div class="form-check form-switch">

                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' }}>

                                        <label class="form-check-label ms-2">
                                            Maintenance Mode
                                        </label>

                                    </div>

                                </div>

                            </div>

                            <!-- SMTP -->
                            <div class="tab-pane fade" id="smtp">

                                <h2 class="section-title">SMTP Settings</h2>

                                <div class="row g-4">

                                    <div class="col-md-6">

                                        <label class="form-label">SMTP Host</label>

                                        <input type="text" name="smtp_host" class="form-control" value="{{ $settings['smtp_host'] ?? '' }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">SMTP Port</label>

                                        <input type="text" name="smtp_port" class="form-control" value="{{ $settings['smtp_port'] ?? '' }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">SMTP Username</label>

                                        <input type="text" name="smtp_username" class="form-control" value="{{ $settings['smtp_username'] ?? '' }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">SMTP Password</label>

                                        <input type="password" name="smtp_password" class="form-control" value="{{ $settings['smtp_password'] ?? '' }}">

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- SAVE -->
                        <div class="mt-5">

                            <button type="submit" class="save-btn">

                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Save Settings

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

    // SHOW/HIDE PASSWORD INPUT
    function togglePassword(id, btn)
    {
        let input = document.getElementById(id);

        if (input.type === "password") {

            input.type = "text";

            btn.classList.add('active');

            btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';

        } else {

            input.type = "password";

            btn.classList.remove('active');

            btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
        }
    }

    // SHOW/HIDE TEXTAREA VALUE
    function toggleView(id, btn)
    {
        let textarea = document.getElementById(id);

        let realValue = textarea.getAttribute('data-real');

        if (textarea.value === '***********************') {

            textarea.value = realValue;

            btn.classList.add('active');

            btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';

        } else {

            textarea.value = '***********************';

            btn.classList.remove('active');

            btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
        }
    }

    // ENABLE EDIT
    function toggleEdit(id, btn)
    {
        let input = document.getElementById(id);

        if (input.hasAttribute('readonly')) {

            input.removeAttribute('readonly');

            input.focus();

            btn.classList.add('active');

        } else {

            input.setAttribute('readonly', true);

            btn.classList.remove('active');
        }
    }

</script>

@endsection