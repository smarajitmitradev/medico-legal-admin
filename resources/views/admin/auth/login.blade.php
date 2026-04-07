<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            height: 100vh;
        }

        .login-card {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }

        .form-control {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
        }

        .form-control::placeholder {
            color: #ddd;
        }

        .form-control:focus {
            box-shadow: none;
            background: rgba(255,255,255,0.3);
        }

        .btn-primary {
            background: #fff;
            color: #4f46e5;
            font-weight: 500;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #e0e7ff;
        }

        .input-group-text {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
        }

        .footer-text {
            color: #e0e7ff;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-md-4">

            <div class="card login-card shadow-lg p-4">

                <h3 class="text-center mb-4 fw-semibold">
                    <i class="bi bi-shield-lock"></i> Admin Login
                </h3>

                {{-- Error Message --}}
                @if(session('error'))
                    <div class="alert alert-danger text-dark">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
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

                    <!-- Button -->
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary py-2">
                            Login
                        </button>
                    </div>

                </form>
            </div>

            <p class="text-center mt-3 footer-text">
                © {{ date('Y') }} Admin Panel
            </p>

        </div>

    </div>
</div>

</body>
</html>