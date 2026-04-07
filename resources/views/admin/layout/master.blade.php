<!DOCTYPE html>
<html lang="en">

@include('admin.layout.header')

<body>

<div class="d-flex">

    <!-- Sidebar -->
    @include('admin.layout.sidebar')

    <!-- Main Content -->
    <div id="content">

        <!-- Navbar -->
        @include('admin.layout.navbar')

        <div class="p-4">
            @yield('content')
        </div>

        <!-- Footer -->
        @include('admin.layout.footer')

    </div>

</div>

</body>
</html>