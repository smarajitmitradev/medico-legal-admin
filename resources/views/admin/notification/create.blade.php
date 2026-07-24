@extends('admin.layout.master')

@section('content')

<style>

/* Background */
body {
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
    min-height: 100vh;
}

/* Center wrapper */
.page-wrapper {
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Card width */
.custom-card {
    width: 100%;
    max-width: 650px;
}

/* Card */
.card {
    border-radius: 16px;
    border: none;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-2px);
}

/* Title */
h4 {
    font-weight: 600;
    margin-bottom: 20px;
    color: #2d3748;
}

/* Labels */
.form-label {
    font-weight: 500;
    color: #4a5568;
}

/* Inputs */
.form-control {
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 12px;
    transition: 0.25s;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

/* Radio */
.form-check-input {
    accent-color: #6366f1;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}

/* Dropdown animation */
#contentDropdown {
    transition: all 0.3s ease;
}

.d-none {
    opacity: 0;
    transform: translateY(-6px);
}

#contentDropdown:not(.d-none) {
    opacity: 1;
    transform: translateY(0);
}

/* Image preview */
#previewImg {
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

/* Button */
.btn-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    transition: 0.3s;
}

.btn-success:hover {
    transform: translateY(-1px);
}

/* File input */
input[type="file"] {
    background: #f9fafb;
    cursor: pointer;
}

</style>


<div class="page-wrapper">
    <div class="custom-card">

        <div class="card p-4">

            <h4>Add Notification</h4>

            <form id="form" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter notification title">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                </div>

                <!-- Type -->
                <div class="mb-3">
                    <label class="form-label d-block">Notification Type</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" value="greeting">
                        <label class="form-check-label">Greeting</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" value="content_update">
                        <label class="form-check-label">Content Update</label>
                    </div>
                </div>

                <!-- Content Dropdown -->
                <div class="mb-3 d-none" id="contentDropdown">
                    <label class="form-label">Select Content</label>

                    <select name="module_content_id" id="moduleContent" class="form-control">
                        <option value="">-- Select Content --</option>
                        @foreach($contents as $id => $title)
                            <option value="{{ $id }}">{{ $title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Image -->
                <div class="mb-3">
                    <label class="form-label">Image</label>

                    <input type="file" name="image" id="imageInput" class="form-control">

                    <!-- Preview -->
                    <div class="mt-2 d-none" id="previewWrapper">
                        <img id="previewImg" width="150">
                    </div>
                </div>

                <!-- Submit -->
                <button class="btn btn-success px-4">Save</button>

            </form>

        </div>

    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    // Toggle dropdown
    $('input[name="type"]').change(function() {
        if ($(this).val() === 'content_update') {
            $('#contentDropdown').removeClass('d-none');
        } else {
            $('#contentDropdown').addClass('d-none');
            $('#moduleContent').val('');
        }
    });

    // Image preview
    $('#imageInput').change(function(){
        if (this.files && this.files[0]) {
            let reader = new FileReader();

            reader.onload = function(e){
                $('#previewImg').attr('src', e.target.result);
                $('#previewWrapper').removeClass('d-none');
            };

            reader.readAsDataURL(this.files[0]);
        }
    });

    // Submit
    $('#form').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('notification.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                toastr.success(res.message);

                $('#form')[0].reset();
                $('#contentDropdown').addClass('d-none');
                $('#previewWrapper').addClass('d-none');
            },
            error: function() {
                toastr.error("Something went wrong");
            }
        });
    });

});
</script>

@endsection