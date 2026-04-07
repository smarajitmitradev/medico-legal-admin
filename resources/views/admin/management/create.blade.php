@extends('admin.layout.master')

@section('content')

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

<!-- jQuery (required for AJAX) -->
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
        <h4>Add Management</h4>

        <form id="managementForm" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control">
            </div>

            <!-- Icon -->
            <div class="mb-3">
                <label>Select Icon</label>

                <div class="icon-dropdown">
                    <div class="icon-selected" id="selectedIconBox">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div class="icon-list" id="iconList">
                        @php
                        $icons = [
                            'fa-user','fa-users','fa-user-tie','fa-user-secret','fa-user-check','fa-user-plus',

                            'fa-gear','fa-cog','fa-sliders','fa-wrench','fa-screwdriver','fa-toolbox',

                            'fa-house','fa-building','fa-city','fa-hotel','fa-store','fa-warehouse',

                            'fa-envelope','fa-paper-plane','fa-inbox','fa-at','fa-reply','fa-share',

                            'fa-phone','fa-phone-volume','fa-mobile','fa-mobile-screen','fa-fax','fa-headset',

                            'fa-star','fa-star-half','fa-heart','fa-thumbs-up','fa-thumbs-down','fa-award',

                            'fa-book','fa-book-open','fa-file','fa-file-lines','fa-folder','fa-folder-open',

                            'fa-scale-balanced','fa-gavel','fa-landmark','fa-briefcase','fa-briefcase-medical','fa-balance-scale',

                            'fa-stethoscope','fa-hospital','fa-user-doctor','fa-syringe','fa-pills','fa-kit-medical',

                            'fa-chart-line','fa-chart-bar','fa-chart-pie','fa-chart-area','fa-coins','fa-dollar-sign',

                            'fa-bell','fa-comment','fa-comments','fa-message','fa-bullhorn','fa-bell-slash',

                            'fa-lock','fa-unlock','fa-key','fa-shield','fa-shield-halved','fa-user-shield',

                            'fa-upload','fa-download','fa-cloud-upload','fa-cloud-download','fa-database','fa-server',

                            'fa-image','fa-images','fa-video','fa-camera','fa-camera-retro','fa-photo-film',

                            'fa-calendar','fa-calendar-days','fa-clock','fa-stopwatch','fa-hourglass','fa-business-time',

                            'fa-location-dot','fa-map','fa-map-location','fa-globe','fa-compass','fa-map-pin',

                            'fa-cart-shopping','fa-bag-shopping','fa-credit-card','fa-wallet','fa-cash-register','fa-receipt',

                            'fa-pen','fa-pencil','fa-edit','fa-marker','fa-highlighter','fa-eraser',

                            'fa-trash','fa-trash-can','fa-recycle','fa-delete-left','fa-broom','fa-soap',

                            'fa-check','fa-check-double','fa-circle-check','fa-xmark','fa-circle-xmark','fa-ban'
                        ];
                        @endphp

                        @foreach($icons as $icon)
                            <div class="icon-item" data-icon="fa-solid {{ $icon }}">
                                <i class="fa-solid {{ $icon }}"></i>
                            </div>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="icon" id="selectedIcon">
            </div>

            <!-- Image -->
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Save</button>
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

// AJAX SUBMIT
$('#managementForm').submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('management.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {

            toastr.success(response.message);

            // Reset form
            $('#managementForm')[0].reset();
            $('#selectedIconBox').html('<i class="fa-solid fa-user"></i>');
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