@extends('admin.layout.master')

@section('content')

<style>
    .form-container {
        max-width: 700px;
        margin: 40px auto;
    }

    .card-custom {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border: none;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
        padding: 15px 20px;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 12px;
        border: 1px solid #ddd;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: #f6c23e;
        box-shadow: 0 0 0 0.15rem rgba(246,194,62,.25);
    }

    textarea.form-control {
        min-height: 120px;
        resize: none;
    }

    .btn-custom {
        background: linear-gradient(135deg, #4e73df, #224abe);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
        transition: 0.3s;
    }

    .btn-custom:hover {
        background: linear-gradient(135deg, #224abe, #1a3a8f);
        transform: translateY(-1px);
    }

    label {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .file-info {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }
</style>

<div class="form-container">
    <div class="card card-custom">
        <div class="card-header-custom">
            Edit {{ $sub->name }}
        </div>

        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ $data->title }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $data->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label>YouTube Link</label>
                    <input type="text" name="youtube_link" value="{{ $data->youtube_link }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Upload New PDF</label>
                    <input type="file" name="pdf_file" class="form-control">
                    <div class="file-info">
                        Leave empty if you don't want to change the existing file
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-custom">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection