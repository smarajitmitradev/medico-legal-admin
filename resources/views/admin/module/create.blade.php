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
                    <label>Summary</label>
                    <textarea name="summary" placeholder="Enter Summary" class="form-control" rows="2"></textarea>
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

                <div class="mb-3">
                    <label>Reading Time (Munites)</label>
                    <input type="number" name="reading_time" placeholder="Enter Reading Time" class="form-control">
                </div>

                <!-- Thumbnail Upload -->
                <!-- Thumbnail Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Thumbnail Image
                    </label>

                    <div class="flex flex-col md:flex-row gap-4">

                        <!-- Upload Box -->
                        <div class="flex-1">
                            <label for="thumbnail" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-300">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>

                                <span class="text-sm text-gray-600">
                                    Click to upload thumbnail
                                </span>

                                <span class="text-xs text-gray-400 mt-1">
                                    JPG, PNG, WEBP
                                </span>

                                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden">
                            </label>
                        </div>

                        <!-- Preview Box -->
                        <div class="w-full md:w-64">
                            <div class="border rounded-xl bg-white shadow-sm p-3 h-40 flex items-center justify-center">

                                <img id="thumbnailPreview" src="" alt="Preview" class="hidden w-full h-full object-cover rounded-lg">

                                <div id="previewPlaceholder" class="text-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v14H4V5zm3 3h.01M4 15l4-4 3 3 5-5 4 4" />
                                    </svg>
                                    <p class="text-sm">Image Preview</p>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-custom">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#4f46e5',
                }
            }
        }
    }
</script>

<script>
    $(document).ready(function() {

        const editor = new toastui.Editor({
            el: document.querySelector('#editor'),
            height: '400px',
            initialEditType: 'markdown',
            previewStyle: 'vertical',
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
                preview.classList.remove('hidden');

                placeholder.classList.add('hidden');
            };

            reader.readAsDataURL(file);
        }
    });
</script>

@endsection