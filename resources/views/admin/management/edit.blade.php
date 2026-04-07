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
body { background: #f5f7fb; }

.card { border-radius: 12px; border: none; }

.icon-dropdown { position: relative; }

.icon-selected {
    border: 1px solid #ddd;
    padding: 14px;
    border-radius: 8px;
    cursor: pointer;
    background: #f9fafb;
    font-size: 22px;
    text-align: center;
}

.icon-list {
    display: none;
    position: absolute;
    width: 100%;
    max-height: 260px;
    overflow-y: auto;
    background: #fff;
    border-radius: 10px;
    margin-top: 8px;
    padding: 10px;
    z-index: 999;

    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(55px, 1fr));
    gap: 10px;

    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.icon-item {
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    background: #f8f9fa;
    text-align: center;
}

.icon-item:hover,
.icon-item.active {
    background: #0d6efd;
    color: #fff;
}
</style>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h4>Edit Management</h4>

        <form id="updateForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="id" value="{{ $management->id }}">

            <!-- Name -->
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ $management->name }}" class="form-control">
            </div>

            <!-- Icon Picker -->
            <div class="mb-3">
                <label>Select Icon</label>

                <div class="icon-dropdown">
                    <div class="icon-selected" id="selectedIconBox">
                        <i class="{{ $management->icon }}"></i>
                    </div>

                    <div class="icon-list" id="iconList">

                        @php
                        $icons = [
                        'fa-user','fa-users','fa-gear','fa-house','fa-envelope','fa-phone','fa-star','fa-heart',
                        'fa-book','fa-file','fa-file-lines','fa-scale-balanced',
                        'fa-stethoscope','fa-hospital','fa-user-doctor','fa-briefcase-medical',
                        'fa-gavel','fa-landmark','fa-chart-line','fa-chart-bar','fa-bell','fa-comment',
                        'fa-lock','fa-key','fa-upload','fa-download','fa-image','fa-video',
                        'fa-calendar','fa-clock','fa-location-dot','fa-map',
                        'fa-cart-shopping','fa-credit-card','fa-pen','fa-trash','fa-check','fa-xmark'
                        ];
                        @endphp

                        @foreach($icons as $icon)
                            <div class="icon-item {{ $management->icon == 'fa-solid '.$icon ? 'active' : '' }}"
                                 data-icon="fa-solid {{ $icon }}">
                                <i class="fa-solid {{ $icon }}"></i>
                            </div>
                        @endforeach

                    </div>
                </div>

                <input type="hidden" name="icon" id="selectedIcon" value="{{ $management->icon }}">
            </div>

            <!-- Image -->
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
                <br>
                <img src="{{ asset('uploads/'.$management->image) }}" width="80" style="border-radius:6px;">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<script>
let selectedBox = $('#selectedIconBox');
let iconList = $('#iconList');
let hiddenInput = $('#selectedIcon');

// Toggle dropdown
selectedBox.click(function () {
    iconList.toggle();
});

// Select icon
$('.icon-item').click(function () {
    let iconClass = $(this).data('icon');

    selectedBox.html(`<i class="${iconClass}"></i>`);
    hiddenInput.val(iconClass);

    $('.icon-item').removeClass('active');
    $(this).addClass('active');

    iconList.hide();
});

// Close dropdown
$(document).click(function(e) {
    if (!$(e.target).closest('.icon-dropdown').length) {
        iconList.hide();
    }
});

// AJAX UPDATE
$('#updateForm').submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append('_method', 'PUT');

    $.ajax({
        url: "{{ route('management.update', $management->id) }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(res) {
            toastr.success(res.message);
        },

        error: function(xhr) {

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;

                Object.values(errors).forEach(err => {
                    toastr.error(err[0]);
                });

            } else if (xhr.status === 404) {
                toastr.error("Route not found!");
            } else {
                toastr.error("Something went wrong!");
            }
        }
    });
});
</script>

@endsection