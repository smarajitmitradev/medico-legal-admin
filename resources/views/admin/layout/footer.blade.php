<!-- Footer -->
<footer class="bg-light text-center py-3 mt-auto shadow-sm">
    <small>© {{ date('Y') }} Admin Dashboard. All rights reserved.</small>
</footer>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('toggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jquery.vmap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/maps/jquery.vmap.world.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
    // IMAGE PREVIEW
    $('#avatarInput').on('change', function(e) {
        let file = e.target.files[0];

        if (file) {
            let reader = new FileReader();

            reader.onload = function(event) {
                $('#avatarPreview').attr('src', event.target.result);
            }

            reader.readAsDataURL(file);
        }
    });

    // AJAX SUBMIT
    $('#avatarForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({

            url: "{{ route('admin.avatar.update') }}",

            type: "POST",

            data: formData,

            processData: false,

            contentType: false,

            beforeSend: function() {
                $('.avatar-save-btn').html(`
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Uploading...
                `);
            },

            success: function(response) {
                toastr.success(response.message);

                $('.avatar-save-btn').html(`
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Update Avatar
                `);

                $('#avatarModal').modal('hide');

                // UPDATE NAVBAR IMAGE
                $('.navbar img.rounded-circle')
                    .attr('src', response.image_url + '?' + new Date().getTime());
            },

            error: function(xhr) {
                $('.avatar-save-btn').html(`
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Update Avatar
                `);

                if (xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('Something went wrong');
                }
            }

        });
    });
</script>
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
    };
</script>

<script>
    let cameraStream = null;

    // START CAMERA
    async function startCamera() {
        try {
            // CHECK SUPPORT
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                toastr.error('Camera not supported in this browser');
                return;
            }

            // OPEN CAMERA
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user"
                },
                audio: false
            });

            // SHOW CAMERA SECTION
            $('#cameraSection').removeClass('d-none');

            // SET VIDEO SOURCE
            const video = document.getElementById('cameraPreview');

            video.srcObject = cameraStream;

            video.play();

            toastr.success('Camera opened');

        } catch (error) {
            console.log(error);

            toastr.error('Camera permission denied or unavailable');
        }
    }

    // STOP CAMERA
    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }

        $('#cameraSection').addClass('d-none');
    }

    // CAPTURE SELFIE
    function captureSelfie() {
        const video = document.getElementById('cameraPreview');

        const canvas = document.getElementById('cameraCanvas');

        const context = canvas.getContext('2d');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // DRAW FRAME
        context.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        // CONVERT TO FILE
        canvas.toBlob(function(blob) {
            const file = new File(
                [blob],
                "selfie.png", {
                    type: "image/png"
                }
            );

            // CREATE FILE INPUT
            const dataTransfer = new DataTransfer();

            dataTransfer.items.add(file);

            document.getElementById('avatarInput').files =
                dataTransfer.files;

            // PREVIEW IMAGE
            $('#avatarPreview').attr(
                'src',
                URL.createObjectURL(blob)
            );

            toastr.success('Selfie captured');

            stopCamera();

        }, 'image/png');
    }

    // FILE PREVIEW
    $('#avatarInput').on('change', function(e) {
        let file = e.target.files[0];

        if (file) {
            let reader = new FileReader();

            reader.onload = function(event) {
                $('#avatarPreview').attr(
                    'src',
                    event.target.result
                );
            }

            reader.readAsDataURL(file);
        }
    });

    // CLOSE CAMERA WHEN MODAL CLOSES
    $('#avatarModal').on('hidden.bs.modal', function() {
        stopCamera();
    });
</script>


<script>

$("#sendBtn").click(function () {

    let message = $("#message").val();

    if (message.trim() == '') return;


    // USER MESSAGE
    $("#chatBox").append(`

        <div class="flex justify-end">

            <div class="max-w-[75%]">

                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-[24px] rounded-tr-md px-5 py-4 shadow-lg">

                    <p class="mb-0 leading-relaxed">
                        ${message}
                    </p>

                </div>

                <span class="text-xs text-slate-400 mt-2 block text-end px-2">
                    You
                </span>

            </div>

        </div>

    `);


    $("#message").val('');



    // LOADING
    let loadingId = 'loading_' + Date.now();


    $("#chatBox").append(`

        <div id="${loadingId}" class="flex items-start gap-4">

            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-lg shrink-0">
                <i class="fas fa-robot"></i>
            </div>

            <div class="bg-white rounded-[24px] rounded-tl-md px-5 py-4 shadow-md border border-slate-100">

                <div class="flex gap-2">

                    <span class="w-3 h-3 bg-indigo-400 rounded-full animate-bounce"></span>

                    <span class="w-3 h-3 bg-purple-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>

                    <span class="w-3 h-3 bg-pink-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>

                </div>

            </div>

        </div>

    `);


    $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);




    $.ajax({

        url: "{{ route('admin.ai.chat.send') }}",
        type: "POST",

        data: {
            _token: "{{ csrf_token() }}",
            message: message
        },

        success: function (res) {

            $("#" + loadingId).remove();


            $("#chatBox").append(`

                <div class="flex items-start gap-4">

                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-lg shrink-0">
                        <i class="fas fa-robot"></i>
                    </div>

                    <div class="max-w-[75%]">

                        <div class="bg-white rounded-[24px] rounded-tl-md px-5 py-4 shadow-md border border-slate-100">

                            <p class="text-slate-700 whitespace-pre-line leading-relaxed mb-0">
                                ${res.reply}
                            </p>

                        </div>

                        <span class="text-xs text-slate-400 mt-2 block px-2">
                            AI Assistant
                        </span>

                    </div>

                </div>

            `);


            $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
        }

    });

});




// ENTER KEY
$("#message").keypress(function(e){

    if(e.which == 13){
        $("#sendBtn").click();
    }

});

</script>
<script>

    $(document).ready(function () {

        $('#world-map').vectorMap({

            map: 'world_en',

            backgroundColor: 'transparent',

            color: '#d1d5db',

            hoverOpacity: 1,

            hoverColor: '#6366f1',

            selectedColor: '#8b5cf6',

            borderColor: '#ffffff',

            borderWidth: 1,

            enableZoom: true,

            showTooltip: true,

            selectedRegions: ['IN', 'US', 'CA', 'AU'],

        });

    });

</script>