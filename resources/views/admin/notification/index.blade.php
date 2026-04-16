@extends('admin.layout.master')

@section('content')

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>

<style>

/* Background */
body {
    background: linear-gradient(135deg, #eef2ff, #f8fafc, #f0fdf4);
}

/* Card */
.custom-card {
    border-radius: 18px;
    padding: 25px;
    border: none;

    background: linear-gradient(135deg, #ffffff, #eef2ff);

    box-shadow: 
        0 10px 25px rgba(0,0,0,0.08),
        inset 0 1px 0 rgba(255,255,255,0.6);
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
}

/* Buttons */
.btn {
    border-radius: 10px;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
}

.btn-warning {
    background: linear-gradient(135deg, #facc15, #eab308);
    border: none;
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
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
}

.badge-greeting {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-content {
    background: #dcfce7;
    color: #166534;
}

</style>

<div class="container mt-4">

    <div class="custom-card">

        <!-- Header -->
        <div class="card-header-custom">
            <h4 class="mb-0">Notification List</h4>

            <a href="{{ route('notification.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add Notification
            </a>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Content</th>
                        <th>Image</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($notifications as $item)
                    <tr id="row-{{ $item->id }}">

                        <!-- Serial -->
                        <td>{{ $loop->iteration }}</td>

                        <!-- Title -->
                        <td>
                            <strong>{{ $item->title }}</strong><br>
                            <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                        </td>

                        <!-- Type -->
                        <td>
                            @if($item->type == 'greeting')
                                <span class="badge badge-greeting">Greeting</span>
                            @else
                                <span class="badge badge-content">Content Update</span>
                            @endif
                        </td>

                        <!-- Content -->
                        <td>
                            @if($item->type == 'content_update' && $item->content)
                                {{ $item->content->title }}
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>

                        <!-- Image -->
                        <td>
                            @if($item->image)
                                <img src="{{ asset('storage/'.$item->image) }}" width="60">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-center">

                            <a href="{{ route('notification.edit', $item->id) }}"
                               class="btn btn-warning btn-sm me-2">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <button class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $item->id }}">
                                <i class="fa-solid fa-trash"></i>
                            </button>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No Notifications Found
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

        if(confirm('Are you sure you want to delete this notification?')) {

            $.ajax({
                url: "{{ route('notification.destroy', ':id') }}".replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {

                    toastr.success('Deleted successfully');

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