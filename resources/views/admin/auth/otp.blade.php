<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md text-center">

        <h2 class="text-2xl font-bold text-gray-800 mb-2">🔐 Verify OTP</h2>
        <p class="text-gray-500 mb-6">Enter the 6-digit code sent to your mobile</p>

        @if(session('error'))
        <div class="bg-red-100 text-red-600 p-2 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.otp.verify') }}">
            @csrf

            <div class="flex justify-between gap-2 mb-4">
                @for ($i = 0; $i < 6; $i++) <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    @endfor
            </div>

            <!-- Hidden input (final OTP value goes here) -->
            <input type="hidden" name="otp" id="otp">

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg mb-3">
                Verify OTP
            </button>
        </form>

        <!-- ✅ Resend OTP -->
        <form method="POST" action="{{ route('admin.otp.resend') }}">
            @csrf
            <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg">
                Resend OTP
            </button>
        </form>

        @if(session('success'))
        <p class="text-green-600 mt-3">{{ session('success') }}</p>
        @endif

        @if(session('error'))
        <p class="text-red-600 mt-3">{{ session('error') }}</p>
        @endif

        <p class="text-sm text-gray-400 mt-4">
            OTP expires in 5 minutes
        </p>

    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('otp');

        inputs.forEach((input, index) => {

            // ✅ Auto move to next
            input.addEventListener('input', (e) => {
                let value = e.target.value;

                // Allow only digits
                value = value.replace(/[^0-9]/g, '');
                input.value = value;

                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                updateHiddenOtp();
            });

            // ✅ Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // ✅ Handle paste (full OTP)
            input.addEventListener('paste', (e) => {
                const pasteData = e.clipboardData.getData('text').trim();

                if (/^\d{6}$/.test(pasteData)) {
                    inputs.forEach((inp, i) => {
                        inp.value = pasteData[i];
                    });
                    updateHiddenOtp();
                    e.preventDefault();
                }
            });
        });

        function updateHiddenOtp() {
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });
            hiddenInput.value = otp;
        }
    </script>

</body>

</html>