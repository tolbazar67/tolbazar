
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-4">
    <div class="max-w-md w-full bg-gray-800 border border-gray-700 rounded-2xl p-8 shadow-2xl text-center">
        <h2 class="text-2xl font-bold text-white mb-2">Verify Your Email</h2>
        <p class="text-sm text-gray-400 mb-6">We have sent a 6-digit verification code to <span class="text-indigo-400">{{ $email }}</span></p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400 text-sm text-left">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('seller.verify.otp') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div>
                <input type="text" name="otp" maxlength="6" required autofocus
                    placeholder="Enter 6-digit OTP"
                    class="w-full tracking-widest text-center text-2xl font-bold py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition">
                Verify OTP
            </button>
        </form>

        <form method="POST" action="{{ route('seller.resend.otp') }}" class="mt-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="text-xs text-gray-400 hover:text-indigo-400 transition">
                Didn't receive code? <span class="underline">Resend OTP</span>
            </button>
        </form>
    </div>
</div>
