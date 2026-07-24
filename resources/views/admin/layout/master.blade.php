<!DOCTYPE html>
<html lang="en">

@include('admin.layout.header')

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

    <!-- Navbar -->
    @include('admin.layout.navbar')

    <!-- Sidebar -->
    @include('admin.layout.sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <section class="content pt-3">

            <div class="container-fluid">

                @yield('content')

            </div>

        </section>

    </div>

    <!-- Footer -->
    @include('admin.layout.footer')

</div>

</body>
</html>