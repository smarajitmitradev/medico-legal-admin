@extends('admin.layout.master')

@section('content')

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
.table td, .table th {
    vertical-align: middle;
}

/* Small square buttons */
.btn-sm {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-danger {
    border-radius: 8px !important;
}

.btn i {
    font-size: 14px;
}

/* Bottom buttons */
.action-btns {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h4>Edit Sub Management</h4>

        <form id="subManagementForm">
            @csrf
            @method('PUT')

            <!-- Management Dropdown -->
            <div class="mb-3">
                <label>Select Management</label>
                <select name="management_id" class="form-control">
                    @foreach($managements as $management)
                        <option value="{{ $management->id }}"
                            {{ $management->id == $submanagement->management_id ? 'selected' : '' }}>
                            {{ $management->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="dynamicTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $rowIndex = 0; @endphp

                        @foreach($submanagements as $sub)
                        <tr>
                            <td>
                                <input type="text"
                                       name="items[{{ $rowIndex }}][name]"
                                       class="form-control"
                                       value="{{ $sub->name }}">
                            </td>

                            <td>
                                <select name="items[{{ $rowIndex }}][type]" class="form-control">
                                    <option value="1" {{ $sub->is_video_pdf == 1 ? 'selected' : '' }}>Video</option>
                                    <option value="2" {{ $sub->is_video_pdf == 2 ? 'selected' : '' }}>PDF</option>
                                    <option value="3" {{ $sub->is_video_pdf == 3 ? 'selected' : '' }}>Both</option>
                                </select>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRow">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                            </td>
                        </tr>
                        @php $rowIndex++; @endphp
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Bottom Buttons -->
            <div class="action-btns">
                <button type="button" class="btn btn-success addRow">
                    <i class="fa-solid fa-plus"></i> Add Row
                </button>

                <button type="submit" class="btn btn-primary">
                    Update
                </button>
            </div>

        </form>
    </div>
</div>

<script>
let rowIndex = {{ count($submanagements) }};

// ADD ROW
$(document).on('click', '.addRow', function () {

    let row = `
        <tr>
            <td>
                <input type="text" name="items[${rowIndex}][name]" class="form-control">
            </td>
            <td>
                <select name="items[${rowIndex}][type]" class="form-control">
                    <option value="1">Video</option>
                    <option value="2">PDF</option>
                    <option value="3">Both</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm removeRow">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </td>
        </tr>
    `;

    $('#dynamicTable tbody').append(row);
    rowIndex++;
});

// REMOVE ROW (prevent deleting last row)
$(document).on('click', '.removeRow', function () {

    if ($('#dynamicTable tbody tr').length === 1) {
        toastr.error('At least one row is required!');
        return;
    }

    $(this).closest('tr').remove();
});

// SUBMIT FORM
$('#subManagementForm').submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('submanagement.update', $submanagement->id) }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res) {
            toastr.success(res.message);

            setTimeout(() => {
                window.location.href = "{{ route('submanagement.index') }}";
            }, 1000);
        },
        error: function(xhr) {
            console.log(xhr)
            let errors = xhr.responseJSON.errors;

            if (errors) {
                Object.values(errors).forEach(err => {
                    toastr.error(err[0]);
                });
            } else {
                toastr.error("Something went wrong!");
            }
        }
    });
});
</script>

@endsection