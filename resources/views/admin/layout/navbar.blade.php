<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left -->
    <ul class="navbar-nav">

        <li class="nav-item">

            <a class="nav-link" data-widget="pushmenu" href="#" role="button">

                <i class="fas fa-bars"></i>

            </a>

        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index3.html" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>

    </ul>

    <!-- Right -->
    <!-- RIGHT NAVBAR -->
    <ul class="navbar-nav ms-auto">

        <!-- SEARCH -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>

            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">

                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">

                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>

                        <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                            <i class="fas fa-times"></i>
                        </button>

                    </div>
                </form>
            </div>
        </li>

        <!-- MESSAGES -->
        <li class="nav-item dropdown">

            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>

                <span class="badge bg-danger navbar-badge">
                    3
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

                <a href="#" class="dropdown-item">

                    <div class="d-flex">

                        <img src="{{ asset('img/user1-128x128.jpg') }}" class="img-size-50 rounded-circle me-3" alt="User">

                        <div>

                            <h6 class="dropdown-item-title mb-1">
                                Brad Diesel
                                <span class="float-end text-danger">
                                    <i class="fas fa-star"></i>
                                </span>
                            </h6>

                            <p class="text-sm mb-0">
                                Call me whenever you can...
                            </p>

                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                4 Hours Ago
                            </small>

                        </div>

                    </div>

                </a>

                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item dropdown-footer">
                    See All Messages
                </a>

            </div>

        </li>

        <!-- NOTIFICATIONS -->
        <li class="nav-item dropdown">

            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>

                <span class="badge bg-warning navbar-badge">
                    15
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

                <span class="dropdown-item dropdown-header">
                    15 Notifications
                </span>

                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope me-2"></i>
                    4 new messages

                    <span class="float-end text-muted text-sm">
                        3 mins
                    </span>
                </a>

                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item">
                    <i class="fas fa-users me-2"></i>
                    8 friend requests

                    <span class="float-end text-muted text-sm">
                        12 hours
                    </span>
                </a>

                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item dropdown-footer">
                    See All Notifications
                </a>

            </div>

        </li>

        <!-- FULLSCREEN -->
        <li class="nav-item">

            <a class="nav-link" data-widget="fullscreen" href="#" role="button">

                <i class="fas fa-expand-arrows-alt"></i>

            </a>

        </li>

        <!-- CONTROL SIDEBAR -->
        <li class="nav-item">

            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">

                <i class="fas fa-th-large"></i>

            </a>

        </li>

        <!-- PROFILE -->
        <li class="nav-item dropdown">

            @php

            $user = \App\Models\Admin::find(session('admin_id'));

            $fullName = $user->name ?? '';

            $nameParts = explode(' ', $fullName, 2);

            @endphp

            <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown" href="#">

                <img src="{{ $user->profile_pic ? asset('uploads/profile/'.$user->profile_pic) : 'https://i.pravatar.cc/120' }}" class="rounded-circle me-2" width="35" height="35">

                <span>Admin</span>

            </a>

            <div class="dropdown-menu dropdown-menu-end">

                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">

                    <i class="fas fa-user me-2"></i>
                    Profile

                </a>

                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#avatarModal">

                    <i class="fas fa-image me-2"></i>
                    Avatar

                </a>


                <!-- AI CHAT OPTION -->
                <!-- AI CHAT BUTTON -->
                <a href="#" class="dropdown-item rounded-3 py-2" data-bs-toggle="modal" data-bs-target="#aiChatModal">

                    <i class="fas fa-robot me-2 text-success"></i>
                    Ask AI

                    <span class="badge bg-success ms-2">New</span>

                </a>

                <div class="dropdown-divider"></div>

                <a href="{{ route('admin.logout') }}" class="dropdown-item text-danger">

                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout

                </a>

            </div>

        </li>

    </ul>

</nav>


<!-- PROFILE MODAL -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 overflow-hidden custom-profile-modal">

            <!-- TOP GRADIENT -->
            <div class="profile-top-bar"></div>

            <!-- HEADER -->
            <div class="modal-header border-0 px-4 pt-4 pb-0">

                <div class="d-flex align-items-center">

                    <!-- PROFILE IMAGE -->
                    <div class="profile-avatar-wrapper">

                        <img src="{{ $user->profile_pic ? asset('uploads/profile/'.$user->profile_pic) : 'https://i.pravatar.cc/150' }}" class="profile-avatar-img" alt="Avatar">

                        <div class="profile-avatar-badge">
                            <i class="bi bi-camera-fill"></i>
                        </div>

                    </div>

                    <!-- INFO -->
                    <div class="ms-3">

                        <h4 class="mb-0 fw-bold text-dark">
                            My Profile
                        </h4>

                        <small class="text-muted">
                            Update your personal information
                        </small>

                    </div>

                </div>

                <!-- Close -->
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>

            </div>

            <!-- BODY -->
            <form action="{{ route('admin.profile.update') }}" method="POST">

                @csrf

                <div class="modal-body px-4 py-4">

                    <div class="row g-4">

                        <!-- First Name -->
                        <div class="col-md-6">

                            <label class="form-label profile-label">
                                First Name
                            </label>

                            <div class="input-group custom-input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input type="text" name="first_name" class="form-control custom-input" value="{{ $nameParts[0] ?? '' }}" placeholder="Enter first name">

                            </div>

                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">

                            <label class="form-label profile-label">
                                Last Name
                            </label>

                            <div class="input-group custom-input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input type="text" name="last_name" class="form-control custom-input" value="{{ $nameParts[1] ?? '' }}" placeholder="Enter last name">

                            </div>

                        </div>

                        <!-- Email -->
                        <div class="col-md-12">

                            <label class="form-label profile-label">
                                Email Address
                            </label>

                            <div class="input-group custom-input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>

                                <input type="email" class="form-control custom-input readonly-input" value="{{ $user->email ?? '' }}" readonly>

                            </div>

                        </div>

                        <!-- Mobile -->
                        <div class="col-md-12">

                            <label class="form-label profile-label">
                                Mobile Number
                            </label>

                            <div class="input-group custom-input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-telephone"></i>
                                </span>

                                <input type="text" name="phone" class="form-control custom-input" value="{{ $user->phone ?? '' }}" placeholder="Enter mobile number">

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 px-4 pb-4 pt-0">

                    <button type="button" class="btn btn-light cancel-btn" data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit" class="btn save-btn">

                        <i class="bi bi-check-circle-fill me-2"></i>
                        Update Profile

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- AVATAR MODAL -->
<!-- AVATAR MODAL -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-md">

        <div class="modal-content border-0 rounded-5 overflow-hidden shadow-lg">

            <!-- TOP GRADIENT -->
            <div class="h-2 bg-gradient" style="background:linear-gradient(90deg,#6366f1,#8b5cf6,#06b6d4)">
            </div>

            <!-- HEADER -->
            <div class="modal-header border-0 pb-0 px-4 pt-4">

                <div class="d-flex align-items-center">

                    <div class="avatar-icon-box">

                        <i class="bi bi-person-circle"></i>

                    </div>

                    <div class="ms-3">

                        <h4 class="fw-bold mb-0">
                            Update Avatar
                        </h4>

                        <small class="text-muted">
                            Upload or capture your profile photo
                        </small>

                    </div>

                </div>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>

            </div>

            <!-- BODY -->
            <div class="modal-body px-4 pb-4 pt-3">

                <!-- PREVIEW -->
                <div class="text-center mb-4">

                    <div class="avatar-preview-wrapper mx-auto">

                        <img id="avatarPreview" src="{{ $user->profile_pic ? asset('uploads/profile/'.$user->profile_pic) : 'https://i.pravatar.cc/150' }}" class="avatar-preview-img">

                        <div class="avatar-overlay">

                            <i class="bi bi-camera-fill"></i>

                        </div>

                    </div>

                </div>

                <!-- FORM -->
                <form id="avatarForm" enctype="multipart/form-data">

                    @csrf

                    <!-- ACTION BUTTONS -->
                    <div class="row g-3 mb-4">

                        <!-- UPLOAD -->
                        <div class="col-6">

                            <label for="avatarInput" class="upload-card w-100">

                                <i class="bi bi-cloud-arrow-up-fill fs-2 mb-2"></i>

                                <div class="fw-semibold">
                                    Upload
                                </div>

                                <small>
                                    Gallery Image
                                </small>

                            </label>

                            <input type="file" name="profile_pic" id="avatarInput" accept="image/*" hidden>

                        </div>

                        <!-- SELFIE -->
                        <div class="col-6">

                            <button type="button" class="selfie-card w-100" onclick="startCamera()">

                                <i class="bi bi-camera-fill fs-2 mb-2"></i>

                                <div class="fw-semibold">
                                    Selfie
                                </div>

                                <small>
                                    Use Camera
                                </small>

                            </button>

                        </div>

                    </div>

                    <!-- CAMERA SECTION -->
                    <div id="cameraSection" class="d-none">

                        <div class="camera-wrapper">

                            <video id="cameraPreview" autoplay playsinline></video>

                        </div>

                        <div class="d-flex gap-2 mt-3">

                            <button type="button" class="btn btn-success flex-fill rounded-4 py-3 fw-semibold" onclick="captureSelfie()">

                                <i class="bi bi-camera2 me-2"></i>
                                Capture

                            </button>

                            <button type="button" class="btn btn-dark flex-fill rounded-4 py-3 fw-semibold" onclick="stopCamera()">

                                Cancel

                            </button>

                        </div>

                        <canvas id="cameraCanvas" class="d-none"></canvas>

                    </div>

                    <!-- SAVE -->
                    <button type="submit" class="btn save-avatar-btn w-100 mt-4">

                        <i class="bi bi-check-circle-fill me-2"></i>
                        Save Avatar

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- AI CHAT MODAL -->
<!-- AI CHAT MODAL -->
<div class="modal fade" id="aiChatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">

            <div class="bg-white rounded-[32px] shadow-2xl overflow-hidden border border-slate-200">

                <!-- HEADER -->
                <div class="relative overflow-hidden">

                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500"></div>

                    <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>

                    <div class="relative z-10 px-8 py-6 flex items-center justify-between">

                        <div class="flex items-center gap-4">

                            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-xl border border-white/20 flex items-center justify-center shadow-lg">
                                <i class="fas fa-robot text-white text-3xl"></i>
                            </div>

                            <div>
                                <h2 class="text-2xl font-bold text-white mb-1">
                                    AI Assistant
                                </h2>

                                <p class="text-indigo-100 text-sm mb-0">
                                    Powered by Groq AI • Smart Admin Support
                                </p>
                            </div>

                        </div>

                        <button type="button"
                                data-bs-dismiss="modal"
                                class="w-12 h-12 rounded-2xl bg-white/20 hover:bg-white/30 transition-all duration-300 text-white border border-white/20">

                            <i class="fas fa-times text-lg"></i>

                        </button>

                    </div>

                </div>



                <!-- CHAT BODY -->
                <div class="bg-slate-100">

                    <div id="chatBox"
                         class="h-[350px] overflow-y-auto px-6 py-6 space-y-6 scroll-smooth">



                        <!-- AI MESSAGE -->
                        <div class="flex items-start gap-4">

                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-lg shrink-0">
                                <i class="fas fa-robot"></i>
                            </div>

                            <div class="max-w-[75%]">

                                <div class="bg-white rounded-[24px] rounded-tl-md px-5 py-4 shadow-md border border-slate-100">

                                    <p class="text-slate-700 leading-relaxed mb-0">
                                        Hello 👋 How can I help you today?
                                    </p>

                                </div>

                                <span class="text-xs text-slate-400 mt-2 block px-2">
                                    AI Assistant
                                </span>

                            </div>

                        </div>



                    </div>

                </div>




                <!-- FOOTER -->
                <div class="bg-white border-t border-slate-200 p-5">

                    <div class="flex items-center gap-4">

                        <!-- INPUT -->
                        <div class="flex-1 relative">

                            <input type="text"
                                   id="message"
                                   placeholder="Ask anything..."
                                   class="w-full h-16 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white px-6 pr-16 text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all duration-300 shadow-sm">

                            <div class="absolute top-1/2 right-5 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-message"></i>
                            </div>

                        </div>

                        <!-- SEND BUTTON -->
                        <button id="sendBtn"
                                class="w-16 h-16 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:scale-105 hover:shadow-2xl transition-all duration-300 text-white flex items-center justify-center shadow-lg">

                            <i class="fas fa-paper-plane text-lg"></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>


<style>
    /* MODAL */
    .custom-profile-modal {
        border-radius: 24px;
        background: #fff;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.15);
    }

    /* TOP BAR */
    .profile-top-bar {
        height: 8px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4);
    }

    /* AVATAR */
    .profile-avatar {
        width: 65px;
        height: 65px;
        border-radius: 18px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);

        display: flex;
        align-items: center;
        justify-content: center;

        color: white;
        font-size: 28px;

        box-shadow:
            0 10px 20px rgba(99, 102, 241, 0.25);
    }

    /* LABEL */
    .profile-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
    }

    /* INPUT GROUP */
    .custom-input-group {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: 0.3s;
        background: #fff;
    }

    .custom-input-group:focus-within {
        border-color: #6366f1;
        box-shadow:
            0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    /* ICON */
    .custom-input-group .input-group-text {
        background: #f9fafb;
        border: none;
        color: #6b7280;
        padding-left: 18px;
        padding-right: 14px;
    }

    /* INPUT */
    .custom-input {
        border: none !important;
        box-shadow: none !important;
        padding: 14px 16px;
        font-size: 15px;
        background: #fff;
    }

    /* READONLY */
    .readonly-input {
        background: #f9fafb !important;
        color: #6b7280;
    }

    /* CANCEL */
    .cancel-btn {
        border-radius: 14px;
        padding: 12px 22px;
        font-weight: 600;
    }

    /* SAVE */
    .save-btn {
        border: none;
        border-radius: 14px;
        padding: 12px 26px;

        background: linear-gradient(135deg, #6366f1, #8b5cf6);

        color: white;
        font-weight: 600;

        transition: 0.3s;

        box-shadow:
            0 10px 20px rgba(99, 102, 241, 0.25);
    }

    .save-btn:hover {
        transform: translateY(-2px);
        color: white;

        box-shadow:
            0 15px 30px rgba(99, 102, 241, 0.35);
    }

    /* BACKDROP */
    .modal-backdrop.show {
        opacity: 0.65;
        backdrop-filter: blur(4px);
    }

    .avatar-modal {
        border-radius: 24px;
        overflow: hidden;
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #eef2ff;

        box-shadow:
            0 10px 25px rgba(99, 102, 241, 0.2);
    }

    .upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 35px;
        text-align: center;
        transition: 0.3s;
        background: #f8fafc;
    }

    .upload-box:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }

    .upload-label {
        cursor: pointer;
        width: 100%;
        color: #475569;
    }

    .avatar-save-btn {
        border: none;
        border-radius: 16px;
        padding: 14px;

        background: linear-gradient(135deg, #6366f1, #8b5cf6);

        color: white;
        font-weight: 600;

        transition: 0.3s;
    }

    .avatar-save-btn:hover {
        transform: translateY(-2px);
        color: white;
    }


    .avatar-icon-box {
        width: 65px;
        height: 65px;
        border-radius: 20px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 30px;
        box-shadow: 0 15px 35px rgba(99, 102, 241, .25);
    }

    .avatar-preview-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
    }

    .avatar-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid #eef2ff;
        box-shadow: 0 15px 35px rgba(0, 0, 0, .12);
    }

    .avatar-overlay {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        border: 3px solid #fff;
    }

    .upload-card,
    .selfie-card {
        border: none;
        border-radius: 22px;
        padding: 30px 20px;
        text-align: center;
        transition: .3s;
        cursor: pointer;
    }

    .upload-card {
        background: #f8fafc;
        color: #334155;
        border: 2px dashed #cbd5e1;
    }

    .upload-card:hover {
        background: #eef2ff;
        border-color: #6366f1;
        transform: translateY(-3px);
    }

    .selfie-card {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
    }

    .selfie-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(99, 102, 241, .25);
    }

    .camera-wrapper {
        overflow: hidden;
        border-radius: 24px;
        background: #0f172a;
        padding: 10px;
    }

    #cameraPreview {
        width: 100%;
        border-radius: 18px;
    }

    .save-avatar-btn {
        border: none;
        border-radius: 20px;
        padding: 16px;
        font-weight: 700;
        font-size: 16px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        transition: .3s;
        box-shadow: 0 15px 35px rgba(99, 102, 241, .25);
    }

    .save-avatar-btn:hover {
        transform: translateY(-2px);
        color: #fff;
        box-shadow: 0 20px 40px rgba(99, 102, 241, .35);
    }


    .profile-avatar-wrapper {
        position: relative;
        width: 72px;
        height: 72px;
        flex-shrink: 0;
    }

    .profile-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 22px;

        border: 4px solid #fff;

        background: #f8fafc;

        box-shadow:
            0 10px 30px rgba(99, 102, 241, .18);
    }

    .profile-avatar-badge {
        position: absolute;
        right: -2px;
        bottom: -2px;

        width: 28px;
        height: 28px;

        border-radius: 50%;

        background: linear-gradient(135deg, #6366f1, #8b5cf6);

        display: flex;
        align-items: center;
        justify-content: center;

        color: #fff;
        font-size: 12px;

        border: 3px solid #fff;

        box-shadow:
            0 6px 16px rgba(99, 102, 241, .25);
    }


    /* updated 
    color */
    .main-header.navbar {
        background-color: #343a40 !important;
        border-color: #4b545c !important;
    }

    .main-header .nav-link,
    .main-header .navbar-brand,
    .main-header .dropdown-toggle {
        color: #ffffff !important;
    }

    .main-header .nav-link:hover {
        color: #dcdcdc !important;
    }

    .navbar-badge {
        font-size: 10px !important;
        padding: 2px 3px !important;
    }


    /* ai modal */

    /* MODAL ANIMATION */
    #aiChatModal .modal-content {
        animation: popupScale 0.35s ease;
    }

    @keyframes popupScale {
        0% {
            opacity: 0;
            transform: scale(.92) translateY(20px);
        }

        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* GLASS EFFECT */
    #aiChatModal .modal-dialog {
        backdrop-filter: blur(8px);
    }

    /* CUSTOM SCROLLBAR */
    #chatBox::-webkit-scrollbar {
        width: 8px;
    }

    #chatBox::-webkit-scrollbar-track {
        background: transparent;
    }

    #chatBox::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #6366f1, #a855f7);
        border-radius: 20px;
    }

    /* CHAT BOX BACKGROUND */
    #chatBox {
        background:
            radial-gradient(circle at top right,
                rgba(99, 102, 241, 0.08),
                transparent 25%),
            radial-gradient(circle at bottom left,
                rgba(168, 85, 247, 0.08),
                transparent 25%),
            #f8fafc;
    }

    /* INPUT GLOW */
    #message:focus {
        box-shadow:
            0 0 0 4px rgba(99, 102, 241, 0.12),
            0 10px 25px rgba(99, 102, 241, 0.15);
    }

    /* SEND BUTTON EFFECT */
    #sendBtn {
        position: relative;
        overflow: hidden;
    }

    #sendBtn::before {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.2);
        top: -40px;
        left: -100px;
        transform: rotate(25deg);
        transition: 0.6s;
    }

    #sendBtn:hover::before {
        left: 120%;
    }

    #sendBtn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 18px 40px rgba(99, 102, 241, 0.35);
    }

    /* AI AVATAR FLOAT */
    .fa-robot {
        animation: floatBot 3s ease-in-out infinite;
    }

    @keyframes floatBot {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-4px);
        }
    }

    /* MESSAGE HOVER */
    #chatBox .bg-white {
        transition: all .3s ease;
    }

    #chatBox .bg-white:hover {
        transform: translateY(-2px);
        box-shadow:
            0 12px 30px rgba(15, 23, 42, 0.08);
    }

    /* HEADER SHINE */
    #aiChatModal .relative.overflow-hidden::after {
        content: "";
        position: absolute;
        top: 0;
        left: -120%;
        width: 60%;
        height: 100%;
        background:
            linear-gradient(to right,
                transparent,
                rgba(255, 255, 255, 0.18),
                transparent);
        transform: skewX(-25deg);
        animation: shine 6s infinite;
    }

    @keyframes shine {
        0% {
            left: -120%;
        }

        100% {
            left: 150%;
        }
    }

    /* MODAL SHADOW */
    #aiChatModal .rounded-\[32px\] {
        box-shadow:
            0 25px 80px rgba(15, 23, 42, 0.25),
            0 10px 30px rgba(99, 102, 241, 0.12);
    }

    /* PLACEHOLDER */
    #message::placeholder {
        color: #94a3b8;
        font-weight: 500;
    }

    /* MOBILE RESPONSIVE */
    @media(max-width:768px) {
        #chatBox {
            height: 450px;
        }

        #aiChatModal .modal-dialog {
            margin: 10px;
        }

        #aiChatModal .px-8 {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
</style>