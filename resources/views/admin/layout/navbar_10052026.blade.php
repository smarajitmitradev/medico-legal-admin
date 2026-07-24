<nav class="navbar navbar-light bg-white shadow-sm px-3">

    <!-- Left: Sidebar Toggle -->
    <button class="btn btn-outline-dark" id="toggleSidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Right: Profile Dropdown -->
    <div class="ms-auto dropdown">
        @php

        $user = \App\Models\Admin::find(session('admin_id'));

        $fullName = $user->name ?? '';

        $nameParts = explode(' ', $fullName, 2);

        @endphp

        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">

            <!-- Profile Image -->
            <img src="{{ $user->profile_pic ? asset('uploads/profile/'.$user->profile_pic) : 'https://i.pravatar.cc/120' }}" class="rounded-circle me-2" width="40" height="40" alt="Profile">

            <!-- Username -->
            <span class="fw-semibold text-dark">Admin</span>
        </a>

        <!-- Dropdown Menu -->
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">


            <!-- PROFILE DROPDOWN ITEM -->
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">

                    <i class="bi bi-person me-2"></i> Profile
                </a>
            </li>




            <li>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
            </li>

            <!-- AVATAR DROPDOWN ITEM -->
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#avatarModal">

                    <i class="bi bi-image me-2"></i> Avatar
                </a>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item text-danger" href="{{route('admin.logout')}}">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>

        </ul>

    </div>

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


    .profile-avatar-wrapper{
        position:relative;
        width:72px;
        height:72px;
        flex-shrink:0;
    }

    .profile-avatar-img{
        width:100%;
        height:100%;
        object-fit:cover;
        border-radius:22px;

        border:4px solid #fff;

        background:#f8fafc;

        box-shadow:
            0 10px 30px rgba(99,102,241,.18);
    }

    .profile-avatar-badge{
        position:absolute;
        right:-2px;
        bottom:-2px;

        width:28px;
        height:28px;

        border-radius:50%;

        background:linear-gradient(135deg,#6366f1,#8b5cf6);

        display:flex;
        align-items:center;
        justify-content:center;

        color:#fff;
        font-size:12px;

        border:3px solid #fff;

        box-shadow:
            0 6px 16px rgba(99,102,241,.25);
    }
</style>