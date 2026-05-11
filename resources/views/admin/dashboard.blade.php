@extends('admin.layout.master')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<!-- <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard v2</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard v2</li>
                </ol>
            </div>
        </div>
    </div>
</div> -->
<!-- /.content-header -->

<div class="container-fluid mb-3">

    <div class="welcome-card d-flex justify-content-between align-items-center">

        <!-- LEFT -->
        <div class="text-white">
            @php

            $user = \App\Models\Admin::find(session('admin_id'));

            $fullName = $user->name ?? '';

            $nameParts = explode(' ', $fullName, 2);

            @endphp

            <h4 class="fw-bold mb-1">
                Welcome Back {{ $user->name}} ! 👋
            </h4>

            <p class="mb-0 small opacity-75">
                Have a nice day and manage your dashboard smoothly.
            </p>

        </div>

        <!-- RIGHT IMAGE -->
        <div>

            <img src="{{asset('img/health4.png')}}" alt="welcome" class="welcome-img">

        </div>

    </div>

</div>

<!-- Main content -->


<!-- Info boxes -->
<div class="row g-3">

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box cpu">

            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-cog"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">CPU Traffic</span>

                <span class="info-box-number">
                    10 <small>%</small>
                </span>
            </div>

        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box like">

            <span class="info-box-icon bg-danger elevation-1">
                <i class="fas fa-thumbs-up"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Likes</span>

                <span class="info-box-number">
                    41,410
                </span>
            </div>

        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box sale">

            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-shopping-cart"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Sales</span>

                <span class="info-box-number">
                    760
                </span>
            </div>

        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box member">

            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-users"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">New Members</span>

                <span class="info-box-number">
                    2,000
                </span>
            </div>

        </div>
    </div>

</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Monthly Recap Report</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Action</a>
                            <a href="#" class="dropdown-item">Another action</a>
                            <a href="#" class="dropdown-item">Something else here</a>
                            <a class="dropdown-divider"></a>
                            <a href="#" class="dropdown-item">Separated link</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-center">
                            <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                        </p>

                        <div class="chart" style="height:200px;">
                            <!-- Sales Chart Canvas -->
                            <canvas id="salesChart"></canvas>
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <p class="text-center">
                            <strong>Goal Completion</strong>
                        </p>

                        <div class="progress-group">
                            Add Products to Cart
                            <span class="float-right"><b>160</b>/200</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 80%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->

                        <div class="progress-group">
                            Complete Purchase
                            <span class="float-right"><b>310</b>/400</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger" style="width: 75%"></div>
                            </div>
                        </div>

                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Visit Premium Page</span>
                            <span class="float-right"><b>480</b>/800</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 60%"></div>
                            </div>
                        </div>

                        <!-- /.progress-group -->
                        <div class="progress-group">
                            Send Inquiries
                            <span class="float-right"><b>250</b>/500</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 50%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- ./card-body -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                            <h5 class="description-header">$35,210.43</h5>
                            <span class="description-text">TOTAL REVENUE</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                            <h5 class="description-header">$10,390.90</h5>
                            <span class="description-text">TOTAL COST</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                            <h5 class="description-header">$24,813.53</h5>
                            <span class="description-text">TOTAL PROFIT</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block">
                            <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                            <h5 class="description-header">1200</h5>
                            <span class="description-text">GOAL COMPLETIONS</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <!-- MAP & BOX PANE -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">
                    US Visitors Report
                </h5>

                <div>
                    <button class="btn btn-sm btn-light">
                        <i class="fas fa-minus"></i>
                    </button>

                    <button class="btn btn-sm btn-light">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

            </div>

            <div class="card-body">

                <div class="row">

                    <!-- LEFT -->
                    <div class="col-md-8">

                        <div id="world-map" class="bg-light rounded" style="height:325px; width:100%; border:1px solid #dee2e6;">
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-md-4">

                        <div class="bg-success text-white rounded p-4 h-100">

                            <div class="mb-4">

                                <h3 class="fw-bold mb-1">
                                    8390
                                </h3>

                                <small>
                                    Visits
                                </small>

                                <div class="progress mt-2" style="height:6px;">
                                    <div class="progress-bar bg-light" style="width:85%"></div>
                                </div>

                            </div>

                            <div class="mb-4">

                                <h3 class="fw-bold mb-1">
                                    30%
                                </h3>

                                <small>
                                    Referrals
                                </small>

                                <div class="progress mt-2" style="height:6px;">
                                    <div class="progress-bar bg-light" style="width:30%"></div>
                                </div>

                            </div>

                            <div>

                                <h3 class="fw-bold mb-1">
                                    70%
                                </h3>

                                <small>
                                    Organic
                                </small>

                                <div class="progress mt-2" style="height:6px;">
                                    <div class="progress-bar bg-light" style="width:70%"></div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <!-- /.card -->
        <div class="row">
            <div class="col-md-6">
                <!-- DIRECT CHAT -->
                <div class="card direct-chat direct-chat-warning">
                    <div class="card-header">
                        <h3 class="card-title">Direct Chat</h3>

                        <div class="card-tools">
                            <span title="3 New Messages" class="badge badge-warning">3</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                                <i class="fas fa-comments"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">
                            <!-- Message. Default to the left -->
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="{{asset('img/user1-128x128.jpg')}}" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    Is this template really for free? That's unbelievable!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message to the right -->
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="{{asset('img/user3-128x128.jpg')}}" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    You better believe it!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message. Default to the left -->
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="{{asset('img/user1-128x128.jpg')}}" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    Working with AdminLTE on a great new app! Wanna join?
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message to the right -->
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="{{asset('img/user3-128x128.jpg')}}" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    I would love to.
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                        </div>
                        <!--/.direct-chat-messages-->

                        <!-- Contacts are loaded here -->
                        <div class="direct-chat-contacts">
                            <ul class="contacts-list">
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="{{asset('img/user1-128x128.jpg')}}" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Count Dracula
                                                <small class="contacts-list-date float-right">2/28/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">How have you been? I was...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="{{asset('img/user7-128x128.jpg')}}" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Sarah Doe
                                                <small class="contacts-list-date float-right">2/23/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">I will be waiting for...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="{{asset('img/user3-128x128.jpg')}}" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Nadia Jolie
                                                <small class="contacts-list-date float-right">2/20/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">I'll call you back at...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="{{asset('img/user5-128x128.jpg')}}" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Nora S. Vans
                                                <small class="contacts-list-date float-right">2/10/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">Where is your new...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="{{asset('img/user6-128x128.jpg')}}" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                John K.
                                                <small class="contacts-list-date float-right">1/27/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">Can I take a look at...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="{{asset('img/user8-128x128.jpg')}}" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Kenneth M.
                                                <small class="contacts-list-date float-right">1/4/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">Never mind I found...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                            </ul>
                            <!-- /.contacts-list -->
                        </div>
                        <!-- /.direct-chat-pane -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <form action="#" method="post">
                            <div class="input-group">
                                <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-warning">Send</button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-footer-->
                </div>
                <!--/.direct-chat -->
            </div>
            <!-- /.col -->

            <div class="col-md-6">
                <!-- USERS LIST -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Latest Members</h3>

                        <div class="card-tools">
                            <span class="badge badge-danger">8 New Members</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <ul class="users-list clearfix">
                            <li>
                                <img src="{{ asset('img/user1-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Alexander Pierce</a>
                                <span class="users-list-date">Today</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user8-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Norman</a>
                                <span class="users-list-date">Yesterday</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user7-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Jane</a>
                                <span class="users-list-date">12 Jan</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user6-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">John</a>
                                <span class="users-list-date">12 Jan</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user2-160x160.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Alexander</a>
                                <span class="users-list-date">13 Jan</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user5-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Sarah</a>
                                <span class="users-list-date">14 Jan</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user4-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Nora</a>
                                <span class="users-list-date">15 Jan</span>
                            </li>
                            <li>
                                <img src="{{ asset('img/user3-128x128.jpg')}}" alt="User Image">
                                <a class="users-list-name" href="#">Nadia</a>
                                <span class="users-list-date">15 Jan</span>
                            </li>
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-center">
                        <a href="javascript:">View All Users</a>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!--/.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- TABLE: LATEST ORDERS -->
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title">Latest Orders</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Item</th>
                                <th>Status</th>
                                <th>Popularity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                <td>Call of Duty IV</td>
                                <td><span class="badge badge-success">Shipped</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                <td>Samsung Smart TV</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                <td>iPhone 6 Plus</td>
                                <td><span class="badge badge-danger">Delivered</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                <td>Samsung Smart TV</td>
                                <td><span class="badge badge-info">Processing</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63</div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                <td>Samsung Smart TV</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                <td>iPhone 6 Plus</td>
                                <td><span class="badge badge-danger">Delivered</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                <td>Call of Duty IV</td>
                                <td><span class="badge badge-success">Shipped</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
            </div>
            <!-- /.card-footer -->
        </div>

        <div class="card shadow-sm" style="height: 448px;padding-top: 9px;">

            <div class="card-header">
                <h3 class="card-title mb-0">Sales Overview</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- First Graph -->
                    <div class="col-md-6 mb-4">
                        <h5 class="text-center mb-3">Monthly Sales</h5>

                        <canvas id="barChart" height="260px"></canvas>
                    </div>

                    <!-- Second Graph -->
                    <div class="col-md-6 mb-4">
                        <h5 class="text-center mb-3">Revenue Growth</h5>

                        <canvas id="lineChart" height="260px"></canvas>
                    </div>

                </div>

            </div>
        </div>




        <!-- /.card -->
    </div>
    <!-- /.col -->

    <div class="col-md-4">
        <!-- Info Boxes Style 2 -->
        <div class="info-box mb-3 bg-warning">
            <span class="info-box-icon"><i class="fas fa-tag"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Inventory</span>
                <span class="info-box-number">5,200</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-success">
            <span class="info-box-icon"><i class="far fa-heart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Mentions</span>
                <span class="info-box-number">92,050</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-danger">
            <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Downloads</span>
                <span class="info-box-number">114,381</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-info">
            <span class="info-box-icon"><i class="far fa-comment"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Direct Messages</span>
                <span class="info-box-number">163,921</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Browser Usage</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="chart-responsive">
                            <canvas id="pieChart" height="150"></canvas>
                        </div>
                        <!-- ./chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <ul class="chart-legend clearfix">
                            <li><i class="far fa-circle text-danger"></i> Chrome</li>
                            <li><i class="far fa-circle text-success"></i> IE</li>
                            <li><i class="far fa-circle text-warning"></i> FireFox</li>
                            <li><i class="far fa-circle text-info"></i> Safari</li>
                            <li><i class="far fa-circle text-primary"></i> Opera</li>
                            <li><i class="far fa-circle text-secondary"></i> Navigator</li>
                        </ul>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-light p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            United States of America
                            <span class="float-right text-danger">
                                <i class="fas fa-arrow-down text-sm"></i>
                                12%</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            India
                            <span class="float-right text-success">
                                <i class="fas fa-arrow-up text-sm"></i> 4%
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            China
                            <span class="float-right text-warning">
                                <i class="fas fa-arrow-left text-sm"></i> 0%
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.card -->

        <!-- PRODUCT LIST -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recently Added Products</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ asset('img/default-150x150.png')}}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">Samsung TV
                                <span class="badge badge-warning float-right">$1800</span></a>
                            <span class="product-description">
                                Samsung 32" 1080p 60Hz LED Smart HDTV.
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ asset('img/default-150x150.png')}}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">Bicycle
                                <span class="badge badge-info float-right">$700</span></a>
                            <span class="product-description">
                                26" Mongoose Dolomite Men's 7-speed, Navy Blue.
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ asset('img/default-150x150.png')}}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">
                                Xbox One <span class="badge badge-danger float-right">
                                    $350
                                </span>
                            </a>
                            <span class="product-description">
                                Xbox One Console Bundle with Halo Master Chief Collection.
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ asset('img/default-150x150.png')}}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">PlayStation 4
                                <span class="badge badge-success float-right">$399</span></a>
                            <span class="product-description">
                                PlayStation 4 500GB Console (PS4)
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="javascript:void(0)" class="uppercase">View All Products</a>
            </div>
            <!-- /.card-footer -->
        </div>


        <div class="card border-0 overflow-hidden shadow-lg rounded-4 weather-card-small">

            <!-- Gradient Area -->
            <div class="position-relative p-3 text-white" style="background: linear-gradient(135deg, #6d28d9, #7c3aed, #06b6d4);">

                <!-- Top -->
                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div>
                        <small class="text-white-50 fw-bold text-uppercase">
                            Today's Weather
                        </small>

                        <h5 class="fw-bold mb-0 mt-1">
                            Kolkata
                        </h5>
                    </div>

                    <button class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center weather-btn">

                        <i class="fas fa-ellipsis-h text-dark small"></i>

                    </button>

                </div>

                <!-- Middle -->
                <div class="d-flex justify-content-between align-items-center">

                    <!-- Left -->
                    <div class="d-flex align-items-center">

                        <div class="weather-icon-box me-3">
                            <img src="{{asset('img/sun.png')}}" alt="weather" width="45" style="
    height: 87px;
    width: auto;
">
                        </div>

                        <div>

                            <div class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-semibold mb-2">
                                ☀ Sunny
                            </div>

                            <div class="small text-white-50">
                                Humidity: 50%
                            </div>

                            <div class="small text-white-50">
                                Wind: 12 km/h
                            </div>

                        </div>

                    </div>

                    <!-- Temp -->
                    <div class="text-end">

                        <h1 class="fw-bold mb-2 temp-text">
                            31°
                        </h1>

                        <div class="d-flex gap-2 justify-content-end">

                            <span class="badge bg-light text-dark px-2 py-2 rounded-pill">
                                H: 32°
                            </span>

                            <span class="badge bg-dark px-2 py-2 rounded-pill">
                                L: 25°
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Bottom -->
            <div class="bg-white py-2 px-3">

                <div class="row text-center">

                    <div class="col-4 border-end">
                        <div class="fw-bold text-primary small">
                            UV Index
                        </div>

                        <small class="text-muted">
                            Moderate
                        </small>
                    </div>

                    <div class="col-4 border-end">
                        <div class="fw-bold text-success small">
                            Visibility
                        </div>

                        <small class="text-muted">
                            10 km
                        </small>
                    </div>

                    <div class="col-4">
                        <div class="fw-bold text-danger small">
                            Pressure
                        </div>

                        <small class="text-muted">
                            1008 mb
                        </small>
                    </div>

                </div>

            </div>

        </div>



        <div class="card">

            <!-- Header -->
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Reviews</h5>

                    <button type="button" class="btn btn-sm btn-light rounded-circle">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div class="card-body review-scroll">

                <!-- Review 1 -->
                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/60?img=1" class="rounded-circle me-3" width="50" height="50" alt="">
                        <div>
                            <h6 class="mb-0">John Doe</h6>

                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        Amazing service and very professional support team.
                    </p>
                </div>

                <!-- Review 2 -->
                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/60?img=2" class="rounded-circle me-3" width="50" height="50" alt="">
                        <div>
                            <h6 class="mb-0">Sophia</h6>

                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        Great UI and easy to use dashboard.
                    </p>
                </div>

                <!-- Review 3 -->
                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/60?img=3" class="rounded-circle me-3" width="50" height="50" alt="">
                        <div>
                            <h6 class="mb-0">Michael</h6>

                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        Performance is smooth and responsive.
                    </p>
                </div>

                <!-- Review 4 -->
                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://i.pravatar.cc/60?img=1" class="rounded-circle me-3" width="50" height="50" alt="">
                        <div>
                            <h6 class="mb-0">S Smith</h6>

                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        Amazing service and very professional support team.
                    </p>
                </div>

            </div>
        </div>

        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<style>
    .welcome-card {

        background: linear-gradient(135deg, #7b2ff7, #9d4edd);

        border-radius: 18px;

        padding: 15px 25px;

        box-shadow: 0 6px 18px rgba(123, 47, 247, 0.20);
    }

    .welcome-img {

        width: 156px;

        height: 90px;

        object-fit: contain;
    }

    .cpu {
        background: #17a2b821;
        box-shadow: 0 0 1px 0px rgb(0 0 0 / 13%), 0 1px 8px 1px rgb(0 0 0 / 28%);
    }

    .like {
        background: #fbd3d7;
        box-shadow: 0 0 1px 0px rgb(0 0 0 / 13%), 0 1px 8px 1px rgb(0 0 0 / 28%);
    }

    .sale {
        background: #c6efcf;
        box-shadow: 0 0 1px 0px rgb(0 0 0 / 13%), 0 1px 8px 1px rgb(0 0 0 / 28%);
    }

    .member {
        background: #f3e5ba;
        box-shadow: 0 0 1px 0px rgb(0 0 0 / 13%), 0 1px 8px 1px rgb(0 0 0 / 28%);
    }

    /* Scroll Area */
    .review-scroll {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 5px;
    }

    /* Review Item */
    .review-item {
        padding-bottom: 15px;
        margin-bottom: 15px;
        border-bottom: 1px solid #f1f1f1;
    }

    /* Custom Scrollbar */
    .review-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .review-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .review-scroll::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    #world-map {
        width: 100%;
        height: 325px;

        border-radius: 22px;

        overflow: hidden;

        border: 1px solid #e5e7eb;

        background:
            radial-gradient(circle at top right,
                rgba(99, 102, 241, .08),
                transparent 30%),

            radial-gradient(circle at bottom left,
                rgba(168, 85, 247, .08),
                transparent 30%),

            #f8fafc;

        box-shadow:
            0 10px 30px rgba(15, 23, 42, .06);

        transition: .3s;
    }

    #world-map:hover {
        transform: translateY(-2px);

        box-shadow:
            0 20px 40px rgba(15, 23, 42, .08);
    }

    .jqvmap-region {
        transition: fill .25s ease;
    }


    .weather-card-small {
        border-radius: 22px;
    }

    .weather-icon-box {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .temp-text {
        font-size: 55px;
        line-height: 1;
    }

    .weather-btn {
        width: 34px;
        height: 34px;
    }

    .card:hover {
        transform: translateY(-3px);
        transition: 0.3s ease;
    }
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" src="{{ asset('js/dashboard2.js') }}"></script>
<script>
    // =========================
    // BAR CHART
    // =========================

    const barCtx = document.getElementById('barChart');

    new Chart(barCtx, {

        type: 'bar',

        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],

            datasets: [{
                    label: '2024',

                    data: [1200, 1900, 1500, 2500, 2200, 3000],

                    backgroundColor: '#4e73df',

                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 16
                },

                {
                    label: '2025',

                    data: [1400, 2100, 1800, 2800, 2600, 3400],

                    backgroundColor: '#6c757d',

                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 16
                }
            ]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'top'
                }
            },

            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });



    // =========================
    // LINE CHART
    // =========================

    const lineCtx = document.getElementById('lineChart');

    new Chart(lineCtx, {

        type: 'line',

        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],

            datasets: [{
                label: 'Revenue',

                data: [null, 1200, 1800, 1500, 2500, 3200],

                borderColor: '#20c997',

                backgroundColor: 'rgba(32,201,151,0.15)',

                fill: false,

                tension: 0,

                borderWidth: 3,

                pointRadius: 5,

                pointBackgroundColor: '#20c997'
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'top'
                }
            },

            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>


@endsection