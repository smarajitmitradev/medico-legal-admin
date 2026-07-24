@extends('admin.layout.master')

@section('content')
<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />

<style>
    body {
        background: #f4f6f9;
    }

    .form-container {
        max-width: 750px;
        margin: 40px auto;
    }

    .card-custom {
        border-radius: 15px;
        overflow: hidden;
        border: none;
        background: linear-gradient(135deg, #667eea, #764ba2);
        padding: 2px;
    }

    .card-inner {
        background: #fff;
        border-radius: 13px;
        padding: 25px;
    }

    .card-header-custom {
        font-size: 22px;
        font-weight: 600;
        color: #fff;
        padding: 18px 25px;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 12px;
        border: 1px solid #ddd;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, .25);
    }

    .btn-custom {
        background: linear-gradient(135deg, #36d1dc, #5b86e5);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
        transition: 0.3s;
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #5b86e5, #36d1dc);
    }

    label {
        font-weight: 500;
    }
</style>

<div class="form-container">
    <div class="card card-custom">
        <div class="card-header-custom">
            ✏️ Edit {{ $module->title }}
        </div>

        <div class="card-inner">
            <form action="{{ route('module.update', ['sub_slug' => $module->submanagement_id,'id' => $module->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ $module->title }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Summary</label>
                    <textarea name="summary" class="form-control" rows="2">{{ old('summary', $module->summary) }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <div id="editor"></div>
                    <input type="hidden" name="description" id="description">
                </div>

                @if($module->sub->is_video_pdf == '1' || $module->sub->is_video_pdf == '3')
                <div class="mb-3">
                    <label>YouTube Link</label>
                    <input type="text" name="youtube_link" value="{{ $module->youtube_link }}" class="form-control">
                </div>
                @endif

                @if($module->sub->is_video_pdf == '2' || $module->sub->is_video_pdf == '3')
                <div class="mb-3">
                    <label>Upload PDF</label>
                    <input type="file" name="pdf_file" class="form-control">

                    @if($module->pdf_file)
                    <small class="text-success">
                        Current File:
                        <a href="{{ asset('storage/'.$module->pdf_file) }}" target="_blank">View PDF</a>
                    </small>
                    @endif
                </div>
                @endif

                <div class="mb-3">
                    <label>Reading Time (Minutes)</label>
                    <input type="number" name="reading_time" value="{{ $module->reading_time }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Thumbnail Image</label>

                    <div class="row g-3">

                        <!-- Upload Area -->
                        <div class="col-md-8">
                            <label for="thumbnail" class="d-flex flex-column justify-content-center align-items-center border border-2 rounded-4 p-4 text-center" style="border-style:dashed !important; min-height:180px; cursor:pointer;">

                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-secondary mb-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>

                                <span class="fw-semibold">Click to upload thumbnail</span>
                                <small class="text-muted">JPG, PNG, JPEG, WEBP</small>

                                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="d-none">
                            </label>
                        </div>

                        <!-- Preview Area -->
                        <div class="col-md-4">

                            <div class="border rounded-4 shadow-sm overflow-hidden d-flex align-items-center justify-content-center" style="height:180px;">

                                @if(!empty($module->thumbnail))
                                <img id="thumbnailPreview" src="{{ asset('storage/' . $module->thumbnail) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                <img id="thumbnailPreview" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                @endif

                                <div id="previewPlaceholder" class="text-center text-muted" @if(!empty($module->thumbnail)) style="display:none;" @endif>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v14H4V5zm3 3h.01M4 15l4-4 3 3 5-5 4 4" />
                                    </svg>

                                    <p class="mt-2 mb-0">Thumbnail Preview</p>
                                </div>

                            </div>

                            @if(!empty($module->thumbnail))
                            <small class="text-success d-block mt-2">
                                Current thumbnail available
                            </small>
                            @endif

                        </div>

                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-custom">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

<script>
    $(document).ready(function() {

        const editor = new toastui.Editor({
            el: document.querySelector('#editor'),
            height: '400px',
            initialEditType: 'markdown',
            previewStyle: 'vertical',

            initialValue: @json($module -> markdown_content ?? ''),

            hooks: {
                addImageBlobHook: async (blob, callback) => {

                    const formData = new FormData();
                    formData.append('image', blob);

                    const response = await fetch("{{ route('upload.image') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: formData
                    });

                    const data = await response.json();

                    callback(data.url, 'image');
                }
            }
        });

        $("form").on("submit", function(e) {

            e.preventDefault();

            let markdown = editor.getMarkdown();

            $("#description").val(markdown);

            this.submit();
        });

    });
</script>

<script>
    document.getElementById('thumbnail').addEventListener('change', function(e) {

        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(event) {

                const preview = document.getElementById('thumbnailPreview');
                const placeholder = document.getElementById('previewPlaceholder');

                preview.src = event.target.result;
                preview.style.display = 'block';

                placeholder.classList.add('d-none');
            };

            reader.readAsDataURL(file);
        }
    });
</script>
@endsection