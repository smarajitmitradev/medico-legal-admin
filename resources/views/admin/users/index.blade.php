@extends('admin.layout.master')

@section('content')

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>

<style>
/* Background */
body {
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
}

/* Card */
.custom-card {
    border-radius: 18px;
    padding: 25px;
    border: none;

    background: linear-gradient(135deg, #f8fafc, #eef2ff);

    box-shadow:
        0 10px 25px rgba(0,0,0,0.08),
        inset 0 1px 0 rgba(255,255,255,0.6);
}

.custom-card:hover {
    transform: translateY(-3px);
    transition: 0.3s;
}

/* Header */
.card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

/* Table */
.table {
    border-collapse: separate;
    border-spacing: 0 10px;
}

.table thead th {
    border: none;
    color: #6b7280;
    font-weight: 600;
}

.table tbody tr {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transition: 0.2s;
}

.table tbody tr:hover {
    transform: scale(1.01);
}

.table td {
    border: none !important;
    padding: 15px;
    vertical-align: middle;
}

/* Image */
.table img {
    border-radius: 10px;
    border: 1px solid #eee;
    object-fit: cover;
}

/* Buttons */
.btn {
    border-radius: 10px;
    transition: 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
}

.btn-warning {
    background: linear-gradient(135deg, #facc15, #eab308);
    border: none;
    color: #fff;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
}

.btn:hover {
    transform: scale(1.05);
}

/* Badge */
.badge {
    padding: 8px 12px;
    border-radius: 30px;
    font-size: 12px;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-secondary {
    background: #e5e7eb;
    color: #374151;
}

.badge-premium {
    background: #fef3c7;
    color: #92400e;
}

.badge-normal {
    background: #dbeafe;
    color: #1e40af;
}
</style>

<div class="container mt-4">

    <div class="custom-card p-4">

        <!-- Header -->
        <div class="card-header-custom">

            <h4 class="mb-0">
                <i class="fa-solid fa-users"></i> User List
            </h4>

            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add User
            </a>

        </div>

        <!-- Table -->
        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Premium</th>
                        <th>Profile</th>
                        <th>Image</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($users as $user)

                    <tr id="row-{{ $user->id }}">

                        <!-- Serial -->
                        <td>{{ $loop->iteration }}</td>

                        <!-- User -->
                        <td>
                            <strong>
                                {{ $user->full_name ?? $user->first_name.' '.$user->last_name }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                ID: #{{ $user->id }}
                            </small>
                        </td>

                        <!-- Mobile -->
                        <td>
                            {{ $user->country_code ?? '+91' }}
                            {{ $user->mobile_number }}
                        </td>

                        <!-- Email -->
                        <td>
                            {{ $user->email ?? 'N/A' }}
                        </td>

                        <!-- User Type -->
                        <td>
                            <span class="badge badge-secondary">
                                {{ ucfirst($user->user_type ?? 'User') }}
                            </span>
                        </td>

                        <!-- Premium -->
                        <td>
                            @if($user->is_premium)
                                <span class="badge badge-premium">
                                    Premium
                                </span>
                            @else
                                <span class="badge badge-normal">
                                    Normal
                                </span>
                            @endif
                        </td>

                        <!-- Profile Status -->
                        <td>
                            @if($user->is_profile_complete)
                                <span class="badge badge-success">
                                    Complete
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    Incomplete
                                </span>
                            @endif
                        </td>

                        <!-- Image -->
                        <td>

                            @if($user->img && file_exists(public_path('uploads/'.$user->img)))

                                <img src="{{ asset('uploads/'.$user->img) }}"
                                     width="60"
                                     height="60">

                            @else

                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name ?? 'User') }}&background=6366f1&color=fff"
                                     width="60"
                                     height="60">

                            @endif

                        </td>

                        <!-- Actions -->
                        <td class="text-center">

                            <!-- Edit -->
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="btn btn-warning btn-sm me-2">

                                <i class="fa-solid fa-pen-to-square"></i>

                            </a>

                            <!-- Delete -->
                            <button class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $user->id }}">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No Users Found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function(){

    $('.delete-btn').click(function(){

        let id = $(this).data('id');

        if(confirm('Are you sure you want to delete this user?')) {

            $.ajax({

                url: "{{ route('users.destroy', ':id') }}".replace(':id', id),

                type: 'POST',

                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },

                success: function(res) {

                    toastr.success('User deleted successfully');

                    $('#row-' + id).fadeOut(300, function(){
                        $(this).remove();
                    });

                },

                error: function() {
                    toastr.error('Something went wrong!');
                }

            });

        }

    });

});
</script>

@endsection