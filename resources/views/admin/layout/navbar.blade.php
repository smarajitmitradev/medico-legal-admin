<nav class="navbar navbar-light bg-white shadow-sm px-3">

    <!-- Left: Sidebar Toggle -->
    <button class="btn btn-outline-dark" id="toggleSidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Right: Profile Dropdown -->
    <div class="ms-auto dropdown">

        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">

            <!-- Profile Image -->
            <img src="https://i.pravatar.cc/40" class="rounded-circle me-2" width="40" height="40" alt="Profile">

            <!-- Username -->
            <span class="fw-semibold text-dark">Admin</span>
        </a>

        <!-- Dropdown Menu -->
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">

            @php

            $user = \App\Models\Admin::find(session('admin_id'));

            $fullName = $user->name ?? '';

            $nameParts = explode(' ', $fullName, 2);

            @endphp
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

                    <!-- Avatar -->
                    <div class="profile-avatar">

                        <i class="bi bi-person-fill"></i>

                    </div>

                    <!-- Info -->
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
</style>