

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 p-3 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Verify OTP Form -->
        <form action="{{ route('verify.otp') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-6">
                <label for="otp" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                    Enter 6-Digit OTP
                </label>
                <input 
                    type="text" 
                    id="otp" 
                    name="otp" 
                    placeholder="------" 
                    maxlength="6" 
                    minlength="6" 
                    inputmode="numeric" 
                    pattern="[0-9]{6}" 
                    autocomplete="one-time-code" 
                    required
                    class="w-full text-center tracking-[1em] text-2xl font-bold px-3.5 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-semibold text-sm transition duration-200 mb-4">
                Verify OTP
            </button>
        </form>

        <!-- Resend OTP Form -->
        <form action="{{ route('resend.otp') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg font-medium text-sm transition duration-200">
                Resend OTP
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Wrong email? 
            <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Register again</a>
        </p>
    </div>
