@extends('admin.layout.master')

@section('content')

<!-- Font Awesome 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

<div class="card shadow p-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Management List</h4>
        <a href="{{ route('management.create') }}" class="btn btn-primary">Add</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Icon</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        @foreach($managements as $item)
            <tr id="row-{{ $item->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->name }}</td>
                <td>
                    @if($item->icon)
                        <i class="{{ $item->icon }}"></i>
                    @endif
                </td>
                <td>
                    @if($item->image && file_exists(public_path('uploads/'.$item->image)))
                        <img src="{{ asset('uploads/'.$item->image) }}" width="60">
                    @endif
                </td>
                <td>
                    <a href="{{ route('management.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $item->id }}">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- jQuery (needed for AJAX) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function(){

    // DELETE MANAGEMENT
    $('.delete-btn').click(function(){
        let id = $(this).data('id');
        if(confirm('Are you sure you want to delete this management?')) {

            $.ajax({
                url: "{{ route('management.destroy', ':id') }}".replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if(res.status) {
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