@extends('admin.layout.master')

@section('content')

<!-- Font Awesome 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

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
                <tr id="row-{{ $management->id }}">

                    <!-- Serial -->
                    <td>{{ $loop->iteration }}</td>

                    <!-- Management Name -->
                    <td>
                        <strong>{{ $management->name }}</strong>
                    </td>

                    <!-- SubManagement List -->
                    <td>
                        @php
                        $type = [1 => 'Video', 2 => 'PDF', 3 => 'Both'];
                        @endphp

                        @forelse($management->submanagements as $sub)
                        <div class="mb-2 px-3 py-2 rounded d-flex justify-content-between align-items-center" style="background:#f1f5f9;">

                            <span>{{ $sub->name }}</span>

                            <span class="badge bg-secondary">
                                {{ $type[$sub->is_video_pdf] ?? '-' }}
                            </span>

                        </div>
                        @empty
                        <span class="text-muted">No SubManagement</span>
                        @endforelse
                    </td>

                    <!-- Action -->
                    <td>
                        <a href="{{ route('management.edit', $management->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

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
        $('.delete-btn').click(function() {
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
                        if (res.status) {
                            toastr.success(res.message);
                            $('#row-' + id).remove();
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(err) {
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });

    });
</script>

@endsection