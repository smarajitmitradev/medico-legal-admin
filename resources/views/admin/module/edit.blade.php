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

                <div class="text-end">
                    <button class="btn btn-custom">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

<script>
    const editor = new toastui.Editor({
        el: document.querySelector('#editor'),
        height: '400px',
        initialEditType: 'markdown',
        previewStyle: 'vertical',

        // ✅ Use markdown_content (IMPORTANT)
        initialValue: @json($module->markdown_content ?? ''), 

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

    // submit markdown
    document.querySelector("form").addEventListener("submit", function () {
        document.querySelector("#description").value = editor.getMarkdown();
    });
</script>
@endsection