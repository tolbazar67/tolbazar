
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-4">
    <div class="max-w-md w-full bg-gray-800 border border-gray-700 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-white">Seller Registration</h2>
            <p class="text-sm text-gray-400 mt-1">Join TolBazar as a Seller and grow your business</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Password</label>
                <input type="password" name="password" required
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition duration-200 shadow-lg shadow-indigo-600/30">
                Register as Seller
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('seller.auth.google') }}" 
                class="w-full flex items-center justify-center gap-2 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition text-sm font-medium">
                <i class="fab fa-google"></i> Continue with Google
            </a>
        </div>

        <p class="text-xs text-center text-gray-400 mt-6">
            Already have a seller account? 
            <a href="{{ route('seller.login') }}" class="text-indigo-400 hover:underline">Login here</a>
        </p>
    </div>
</div>
