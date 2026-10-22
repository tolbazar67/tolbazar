
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-4">
    <div class="max-w-md w-full bg-gray-800 border border-gray-700 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-white">Create New Password</h2>
            <p class="text-sm text-gray-400 mt-1">Enter the OTP sent to your email and your new password</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('seller.password.reset') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">OTP Code</label>
                <input type="text" name="otp" required maxlength="6"
                    class="w-full mt-1 px-4 py-2 tracking-widest text-center bg-gray-900 border border-gray-700 rounded-lg text-white font-bold focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">New Password</label>
                <input type="password" name="password" required
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition">
                Reset Password
            </button>
        </form>
    </div>
</div>
