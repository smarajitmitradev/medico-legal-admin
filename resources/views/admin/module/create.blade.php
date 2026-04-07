@extends('admin.layout.master')

@section('content')
<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />


<style>
    .form-container {
        max-width: 700px;
        margin: 40px auto;
    }

    .card-custom {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #4e73df, #224abe);
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
        border-color: #4e73df;
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, .25);
    }

    textarea.form-control {
        min-height: 120px;
        resize: none;
    }

    .btn-custom {
        background: linear-gradient(135deg, #1cc88a, #17a673);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
        transition: 0.3s;
    }

    .btn-custom:hover {
        background: linear-gradient(135deg, #17a673, #13855c);
        transform: translateY(-1px);
    }

    label {
        font-weight: 500;
        margin-bottom: 5px;
    }
</style>

<div class="form-container">
    <div class="card card-custom">
        <div class="card-header-custom">
            Create {{ $sub->name }}
        </div>

        <div class="card-body p-4">
            <form action="{{ route('module.store', $sub->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Enter title" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <div id="editor"></div>
                    <input type="hidden" name="description" id="description">
                </div>

                @if($sub->is_video_pdf == '1' || $sub->is_video_pdf == '3')
                <div class="mb-3">
                    <label>YouTube Link</label>
                    <input type="text" name="youtube_link" placeholder="Paste YouTube link" class="form-control">
                </div>
                @endif

                @if($sub->is_video_pdf == '2' || $sub->is_video_pdf == '3')
                <div class="mb-3">
                    <label>Upload PDF</label>
                    <input type="file" name="pdf_file" class="form-control">
                </div>
                @endif

                <div class="text-end">
                    <button class="btn btn-custom">Save</button>
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
        initialEditType: 'markdown', // markdown + preview
        previewStyle: 'vertical',    // side-by-side like Notion
        placeholder: 'Write something...',
        
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

                // insert image into markdown
                callback(data.url, 'image');
            }
        }
    });

    // before submit, put markdown into hidden input
    document.querySelector("form").addEventListener("submit", function () {
        document.querySelector("#description").value = editor.getMarkdown();
    });
</script>

@endsection