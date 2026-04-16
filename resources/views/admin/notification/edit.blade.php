@extends('admin.layout.master')

@section('content')

<style>
/* Page background */
body {
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
}

/* Card */
.card {
    border: none;
    border-radius: 16px;
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
    color: #333;
}

/* Inputs */
.form-control {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 12px;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
}

/* Radio buttons */
input[type="radio"] {
    accent-color: #4f46e5;
    margin-right: 6px;
}

label {
    margin-right: 20px;
    font-weight: 500;
    color: #444;
}

/* Image preview */
img {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Button */
.btn-primary {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #4338ca, #4f46e5);
    transform: translateY(-1px);
}

/* Dropdown */
#contentDropdown {
    transition: all 0.3s ease;
}

/* Smooth animation */
.d-none {
    opacity: 0;
    transform: translateY(-5px);
}

#contentDropdown:not(.d-none) {
    opacity: 1;
    transform: translateY(0);
}

/* File input */
input[type="file"] {
    padding: 10px;
    background: #f9fafb;
    cursor: pointer;
}

/* Container spacing */
.container {
    max-width: 700px;
}
</style>


<div class="container mt-4">
    <div class="card p-4">

        <h4>Edit Notification</h4>

        <form id="form">
    @csrf
    @method('PUT')

    <!-- Title -->
    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" 
               name="title" 
               value="{{ $notification->title }}" 
               class="form-control" 
               placeholder="Enter notification title">
    </div>

    <!-- Description -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" 
                  class="form-control" 
                  rows="3"
                  placeholder="Enter description">{{ $notification->description }}</textarea>
    </div>

    <!-- Type -->
    <div class="mb-3">
        <label class="form-label d-block">Notification Type</label>

        <div class="form-check form-check-inline">
            <input class="form-check-input" 
                   type="radio" 
                   name="type" 
                   value="greeting"
                   {{ $notification->type=='greeting'?'checked':'' }}>
            <label class="form-check-label">Greeting</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" 
                   type="radio" 
                   name="type" 
                   value="content_update"
                   {{ $notification->type=='content_update'?'checked':'' }}>
            <label class="form-check-label">Content Update</label>
        </div>
    </div>

    <!-- Image -->
    <div class="mb-3">
        <label class="form-label">Image</label>

        <!-- Existing Image -->
        @if($notification->image)
            <div class="mb-2">
                <img src="{{ asset('storage/'.$notification->image) }}" 
                     width="150" 
                     class="rounded shadow-sm">
            </div>
        @endif

        <!-- Upload -->
        <input type="file" name="image" class="form-control">
    </div>

    <!-- Content Dropdown -->
    <div class="mb-3 {{ $notification->type=='content_update'?'':'d-none' }}" id="contentDropdown">
        <label class="form-label">Select Content</label>

        <select name="module_content_id" class="form-control">
            <option value="">-- Select Content --</option>
            @foreach($contents as $id => $title)
                <option value="{{ $id }}" 
                    {{ $notification->module_content_id==$id?'selected':'' }}>
                    {{ $title }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Submit -->
    <button class="btn btn-primary px-4">Update</button>

</form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('input[name="type"]').change(function() {
        if ($(this).val() == 'content_update') {
            $('#contentDropdown').removeClass('d-none');
        } else {
            $('#contentDropdown').addClass('d-none');
        }
    });

    $('#form').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('notification.update',$notification->id) }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                toastr.success(res.message);
            }
        });
    });
</script>

@endsection