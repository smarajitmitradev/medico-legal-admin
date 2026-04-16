@extends('admin.layout.master')

@section('content')

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
    /* Card */
    .card {
        border-radius: 18px;
        border: none;
        background: linear-gradient(135deg, #f8fafc, #eef2ff);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    /* Heading */
    h4 {
        font-weight: 600;
        color: #1f2937;
    }

    /* Table */
    .table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table thead th {
        border: none;
        font-weight: 600;
        color: #6b7280;
    }

    .table tbody tr {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: 0.2s;
    }

    .table tbody tr:hover {
        transform: translateY(-2px);
    }

    /* Table cell */
    .table td {
        border: none !important;
        padding: 15px;
        vertical-align: middle;
    }

    /* Sub items */
    .sub-box {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 10px 15px;
        margin-bottom: 8px;
        transition: 0.2s;
    }

    .sub-box:hover {
        background: #e2e8f0;
    }

    /* Badge */
    .badge {
        font-size: 12px;
        padding: 6px 10px;
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

    .name-cell i {
        margin-right: 8px;
        color: #6366f1;
    }
</style>

<div class="card shadow p-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>SubManagement List</h4>
        <a href="{{ route('submanagement.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Management</th>
                    <th>SubManagement (Name & Type)</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($managements as $management)
                <tr>

                    <!-- Serial -->
                    <td>{{ $loop->iteration }}</td>

                    <!-- Management -->
                    <td class="name-cell">
                        @if($management->icon)
                        <i class="{{ $management->icon }}"></i>
                        @endif
                        <strong>{{ $management->name }}</strong>
                    </td>

                    <!-- SubManagement List -->
                    <td>
                        @php
                        $type = [1 => 'Video', 2 => 'PDF', 3 => 'Both'];
                        @endphp

                        @forelse($management->submanagements as $sub)

                        <div class="sub-box d-flex justify-content-between align-items-center">

                            <!-- Name + Type -->
                            <div>
                                <strong>{{ $sub->name }}</strong>
                                <span class="badge bg-secondary ms-2">
                                    {{ $type[$sub->is_video_pdf] ?? '-' }}
                                </span>
                            </div>

                        </div>

                        @empty
                        <span class="text-muted">No SubManagement</span>
                        @endforelse
                    </td>

                    <!-- Action (ONLY MANAGEMENT LEVEL) -->
                    <td>

                        <!-- Edit -->
                        @if($management->submanagements->count() > 0)
                        <a href="{{ route('submanagement.edit', $management->submanagements->first()->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endif

                        <!-- Delete (Delete ALL submanagements under this management) -->
                        <button class="btn btn-danger btn-sm delete-management" data-id="{{ $management->id }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No Data Found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function() {

        // DELETE SUBMANAGEMENT
        $(document).on('click', '.delete-sub', function() {

            let id = $(this).data('id');

            if (confirm('Are you sure you want to delete this SubManagement?')) {

                $.ajax({
                    url: "{{ route('submanagement.destroy', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {

                        toastr.success('Deleted successfully');

                        $('#sub-row-' + id).remove();
                    },
                    error: function() {
                        toastr.error('Something went wrong!');
                    }
                });
            }

        });

    });


    $(document).on('click', '.delete-management', function() {

        let managementId = $(this).data('id');

        if (confirm('Delete all submanagements under this management?')) {

            $.ajax({
                url: "{{ route('submanagement.destroy', 0) }}", // dummy id
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                    management_id: managementId // ✅ pass this
                },
                success: function(res) {
                    toastr.success(res.message);
                    location.reload();
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });

        }

    });
</script>


@endsection