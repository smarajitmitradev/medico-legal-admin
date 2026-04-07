@extends('admin.layout.master')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4 fw-bold text-dark">Dashboard Overview</h2>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow border-0 text-white bg-primary">
                <div class="card-body">
                    <h6>Total Users</h6>
                    <h3>1,250</h3>
                    <small>+12% this month</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 text-white bg-success">
                <div class="card-body">
                    <h6>Orders</h6>
                    <h3>320</h3>
                    <small>+8% growth</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 text-white bg-warning">
                <div class="card-body">
                    <h6>Revenue</h6>
                    <h3>$5,430</h3>
                    <small>+15% increase</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 text-white bg-danger">
                <div class="card-body">
                    <h6>Pending Tasks</h6>
                    <h3>18</h3>
                    <small>Needs attention</small>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts -->
    <div class="row g-4">

        <!-- Line Chart -->
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">
                    Monthly Sales
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">
                    Traffic Sources
                </div>
                <div class="card-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Progress + Activity -->
    <div class="row g-4 mt-1">

        <!-- Progress Section -->
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">
                    Project Progress
                </div>
                <div class="card-body">

                    <p>Website Redesign</p>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: 70%">70%</div>
                    </div>

                    <p>Mobile App</p>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-info" style="width: 50%">50%</div>
                    </div>

                    <p>API Development</p>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 30%">30%</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Activity -->
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">
                    Recent Activity
                </div>
                <div class="card-body">
                    <ul class="list-group">

                        <li class="list-group-item">New user registered</li>
                        <li class="list-group-item">Order #1023 completed</li>
                        <li class="list-group-item">Payment received</li>
                        <li class="list-group-item">New support ticket</li>

                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    const ctx = document.getElementById('salesChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun'],
            datasets: [{
                label: 'Sales',
                data: [120, 190, 300, 250, 220, 400],
                borderColor: '#0d6efd',
                fill: true,
                backgroundColor: 'rgba(13,110,253,0.1)'
            }]
        }
    });

    // Pie Chart
    const pie = document.getElementById('pieChart');
    new Chart(pie, {
        type: 'pie',
        data: {
            labels: ['Direct', 'Social', 'Referral'],
            datasets: [{
                data: [55, 25, 20],
                backgroundColor: ['#0d6efd','#198754','#ffc107']
            }]
        }
    });
</script>

@endsection