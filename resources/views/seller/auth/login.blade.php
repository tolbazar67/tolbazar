
<div class="min-h-screen flex items-center justify-center bg-gray-900 px-4">
    <div class="max-w-md w-full bg-gray-800 border border-gray-700 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-white">Seller Login</h2>
            <p class="text-sm text-gray-400 mt-1">Access your store dashboard</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/50 rounded-lg text-green-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('seller.login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-semibold text-gray-300 uppercase">Password</label>
                    <a href="{{ route('seller.password.request') }}" class="text-xs text-indigo-400 hover:underline">Forgot?</a>
                </div>
                <input type="password" name="password" required
                    class="w-full mt-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition duration-200">
                Login to Seller Dashboard
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('seller.auth.google') }}" 
                class="w-full flex items-center justify-center gap-2 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition text-sm font-medium">
                Sign in with Google
            </a>
        </div>

        <p class="text-xs text-center text-gray-400 mt-6">
            New seller? 
            <a href="{{ route('seller.register') }}" class="text-indigo-400 hover:underline">Register store</a>
        </p>
    </div>
</div>
