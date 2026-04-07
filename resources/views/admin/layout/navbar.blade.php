<nav class="navbar navbar-light bg-white shadow-sm px-3">

    <!-- Left: Sidebar Toggle -->
    <button class="btn btn-outline-dark" id="toggleSidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Right: Profile Dropdown -->
    <div class="ms-auto dropdown">

        <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
           href="#"
           id="profileDropdown"
           data-bs-toggle="dropdown"
           aria-expanded="false">

            <!-- Profile Image -->
            <img src="https://i.pravatar.cc/40"
                 class="rounded-circle me-2"
                 width="40"
                 height="40"
                 alt="Profile">

            <!-- Username -->
            <span class="fw-semibold text-dark">Admin</span>
        </a>

        <!-- Dropdown Menu -->
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">

            <li>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-person me-2"></i> Profile
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
                <a class="dropdown-item text-danger" href="{{route('admin.logout')}}">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>

        </ul>

    </div>

</nav>