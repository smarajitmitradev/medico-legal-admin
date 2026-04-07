<div id="sidebar" class="text-white p-3">

    <h4 class="text-center mb-4 fw-bold">Admin</h4>

    <ul class="nav flex-column">

        <!-- Dashboard -->
        <li class="nav-item mb-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Users -->
        <li class="nav-item mb-2">
            <a href="#" class="nav-link sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>
        </li>

        {{-- MANAGEMENT --}}
        <li class="nav-item mb-2">
            <a href="{{ route('management.index') }}" class="nav-link sidebar-link {{ request()->routeIs('management.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Management</span>
            </a>
        </li>

        {{-- SUBMANAGEMENT --}}
        <li class="nav-item mb-2">
            <a href="{{ route('submanagement.index') }}" class="nav-link sidebar-link {{ request()->routeIs('submanagement.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Sub Management</span>
            </a>
        </li>

        {{-- MODULES --}}
        <li class="nav-item mb-2">
            {{-- Use a default route that does not require sub_slug --}}
            <a href="{{ url('admin/module') }}" class="nav-link sidebar-link {{ request()->is('admin/module*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>Modules</span>
            </a>
        </li>

        <!-- Settings -->
        <li class="nav-item">
            <a href="#" class="nav-link sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </li>

    </ul>

</div>