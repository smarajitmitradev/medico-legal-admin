@extends('admin.layout.master')

@section('content')

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
body {
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
}

/* Card */
.card {
    border-radius: 18px;
    border: none;
    padding: 25px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card:hover { transform: translateY(-2px); }

/* Heading */
h4 { font-weight: 600; margin-bottom: 20px; color: #2c3e50; }

/* Label */
label { font-weight: 500; margin-bottom: 6px; color: #555; }

/* Inputs */
.form-control {
    border-radius: 10px;
    height: 45px;
    border: 1px solid #e5e7eb;
    padding: 10px;
    transition: all 0.2s ease;
    background: #f9fafb;
}

.form-control:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

/* Table */
.table { border-collapse: separate; border-spacing: 0 10px; }
.table thead th { border: none; font-weight: 600; color: #6b7280; padding-bottom: 10px; }
.table tbody tr {
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    border-radius: 12px;
    transition: 0.2s;
}
.table tbody tr:hover { transform: scale(1.01); }
.table td { border: none !important; padding: 15px; }

/* Buttons */
.btn { border-radius: 10px; transition: 0.2s; }
.btn-success {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none; color: #fff;
}
.btn-success:hover { transform: scale(1.1); }
.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
}
.btn-danger:hover { transform: scale(1.1); }
.btn-primary {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none; padding: 10px 22px; font-weight: 500; font-size: 15px;
}
.btn-primary:hover { transform: translateY(-2px); }

/* Table container */
.table-responsive { margin-top: 15px; }

/* Smooth animation for new rows */
tr { animation: fadeIn 0.3s ease; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="container mt-4">
    <div class="card p-4">
        <h4>Add Sub Management</h4>

        <form id="subManagementForm">
            @csrf

            <!-- Management Dropdown -->
            <div class="mb-3">
                <label class="mb-1">Select Management</label>
                <select name="management_id" class="form-control">
                    <option value="">-- Select Management --</option>
                    @foreach($managements as $management)
                        <option value="{{ $management->id }}">
                            {{ $management->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Dynamic Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="items[0][name]" class="form-control" placeholder="Enter name">
                            </td>
                            <td>
                                <select name="items[0][type]" class="form-control">
                                    <option value="1">Video</option>
                                    <option value="2">PDF</option>
                                    <option value="3">Both</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-success addRow">
                                <i class="fa-solid fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                Save
            </button>
        </form>
    </div>
</div>

<script>
let rowIndex = 1;

// Add Row
$(document).on('click', '.addRow', function () {

    let newRow = `
        <tr>
            <td>
                <input type="text" name="items[${rowIndex}][name]" class="form-control" placeholder="Enter name">
            </td>
            <td>
                <select name="items[${rowIndex}][type]" class="form-control">
                    <option value="1">Video</option>
                    <option value="2">PDF</option>
                    <option value="3">Both</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow">
                <i class="fa-solid fa-minus"></i>
                </button>
            </td>
        </tr>
    `;

    $('#dynamicTable tbody').append(newRow);
    rowIndex++;
});

// Remove Row
$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
});

// AJAX Submit
$('#subManagementForm').submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('submanagement.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {

            toastr.success(response.message);

            $('#subManagementForm')[0].reset();
            $('#dynamicTable tbody').html(`
                <tr>
                    <td>
                        <input type="text" name="items[0][name]" class="form-control">
                    </td>
                    <td>
                        <select name="items[0][type]" class="form-control">
                            <option value="1">Video</option>
                            <option value="2">PDF</option>
                            <option value="3">Both</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-success addRow">
                        <i class="fa-solid fa-plus"></i>
                        </button>
                    </td>
                </tr>
            `);

            rowIndex = 1;
        },
        error: function(xhr) {
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