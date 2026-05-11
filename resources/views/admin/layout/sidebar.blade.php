<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
        <img src="{{asset('img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text fw-bold">
            Admin Panel
        </span>

    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{asset('img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Alexander Pierce</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Menu -->
        <nav class="mt-3">

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">

                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-tachometer-alt"></i>

                        <p>Dashboard</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-users"></i>

                        <p>Users</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('management.index') }}" class="nav-link {{ request()->routeIs('management.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-sitemap"></i>

                        <p>Management</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('submanagement.index') }}" class="nav-link {{ request()->routeIs('submanagement.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-layer-group"></i>

                        <p>Sub Management</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ url('admin/module') }}" class="nav-link {{ request()->is('admin/module*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-cubes"></i>

                        <p>Modules</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('notification.index') }}" class="nav-link {{ request()->routeIs('notification.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-bell"></i>

                        <p>Notification</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-cog"></i>

                        <p>Settings</p>

                    </a>

                </li>

            </ul>

        </nav>

    </div>

</aside>