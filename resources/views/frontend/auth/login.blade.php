<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #ebbe1ce0, #2a5298);
            height: 100vh;
        }

        .login-card {
            border-radius: 15px;
            overflow: hidden;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            border-radius: 8px;
            background: #2a5298;
            border: none;
        }

        .btn-primary:hover {
            background: #1e3c72;
        }

        .login-title {
            font-weight: 700;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow-lg login-card">
                <div class="card-body p-4">

                    <!-- Title -->
                    <div class="text-center mb-4">
                        <h3 class="login-title">Welcome Back 👋</h3>
                        <p class="text-muted">Login to your account</p>
                    </div>

                    <!-- Error Message -->
                    @if(session('error'))
                        <div class="alert alert-danger text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('user.login.submit') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <input type="checkbox"> Remember me
                            </div>
                            <a href="#" class="text-decoration-none">Forgot Password?</a>
                        </div>

                        <!-- Button -->
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>

                    </form>

                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-white mt-3">
                © {{ date('Y') }} Your App. All rights reserved.
            </p>

        </div>

    </div>
</div>

</body>
</html>