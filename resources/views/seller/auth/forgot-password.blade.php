
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-4">
    <div class="max-w-md w-full bg-gray-800 border border-gray-700 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-white">Reset Password</h2>
            <p class="text-sm text-gray-400 mt-1">Enter your email to receive a reset OTP</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('seller.password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition">
                Send Reset OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('seller.login') }}" class="text-xs text-indigo-400 hover:underline">Back to Login</a>
        </div>
    </div>
</div>
