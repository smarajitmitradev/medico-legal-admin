<!-- Footer -->
<footer class="bg-light text-center py-3 mt-auto shadow-sm">
    <small>© {{ date('Y') }} Admin Dashboard. All rights reserved.</small>
</footer>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>

    // IMAGE PREVIEW
    $('#avatarInput').on('change', function(e)
    {
        let file = e.target.files[0];

        if (file)
        {
            let reader = new FileReader();

            reader.onload = function(event)
            {
                $('#avatarPreview').attr('src', event.target.result);
            }

            reader.readAsDataURL(file);
        }
    });

    // AJAX SUBMIT
    $('#avatarForm').submit(function(e)
    {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({

            url: "{{ route('admin.avatar.update') }}",

            type: "POST",

            data: formData,

            processData: false,

            contentType: false,

            beforeSend: function()
            {
                $('.avatar-save-btn').html(`
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Uploading...
                `);
            },

            success: function(response)
            {
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

            error: function(xhr)
            {
                $('.avatar-save-btn').html(`
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Update Avatar
                `);

                if (xhr.responseJSON.errors)
                {
                    $.each(xhr.responseJSON.errors, function(key, value)
                    {
                        toastr.error(value[0]);
                    });
                }
                else
                {
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
    async function startCamera()
    {
        try
        {
            // CHECK SUPPORT
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia)
            {
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

        }
        catch (error)
        {
            console.log(error);

            toastr.error('Camera permission denied or unavailable');
        }
    }

    // STOP CAMERA
    function stopCamera()
    {
        if (cameraStream)
        {
            cameraStream.getTracks().forEach(track => track.stop());
        }

        $('#cameraSection').addClass('d-none');
    }

    // CAPTURE SELFIE
    function captureSelfie()
    {
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
        canvas.toBlob(function(blob)
        {
            const file = new File(
                [blob],
                "selfie.png",
                {
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
    $('#avatarInput').on('change', function(e)
    {
        let file = e.target.files[0];

        if (file)
        {
            let reader = new FileReader();

            reader.onload = function(event)
            {
                $('#avatarPreview').attr(
                    'src',
                    event.target.result
                );
            }

            reader.readAsDataURL(file);
        }
    });

    // CLOSE CAMERA WHEN MODAL CLOSES
    $('#avatarModal').on('hidden.bs.modal', function ()
    {
        stopCamera();
    });

</script>