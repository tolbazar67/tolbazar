<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Forgot Password | TolBazar</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">


    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">


        <div class="text-center mb-8">

            <h1 class="text-2xl font-bold text-gray-800">
                Forgot Password?
            </h1>

            <p class="text-gray-500 text-sm mt-2">
                Enter your registered email address and we will send you a password reset OTP.
            </p>

        </div>



        @if (session('success'))

            <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">

                {{ session('success') }}

            </div>

        @endif



        @if ($errors->any())

            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 p-3 rounded-lg text-sm">

                <ul class="list-disc list-inside">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif



        <form
            action="{{ route('password.email') }}"
            method="POST"
        >

            @csrf


            <div class="mb-6">

                <label
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Email Address
                </label>


                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your registered email"
                    required

                    class="
                        w-full
                        px-4
                        py-3
                        border
                        border-gray-300
                        rounded-lg
                        focus:outline-none
                        focus:ring-2
                        focus:ring-indigo-500
                    "
                >

            </div>



            <button
                type="submit"

                class="
                    w-full
                    bg-indigo-600
                    hover:bg-indigo-700
                    text-white
                    py-3
                    rounded-lg
                    font-semibold
                    transition
                "
            >

                Send Reset OTP

            </button>

        </form>



        <div class="text-center mt-6">

            <a
                href="{{ route('login') }}"

                class="
                    text-sm
                    text-indigo-600
                    font-medium
                    hover:underline
                "
            >

                ← Back to Login

            </a>

        </div>


    </div>


</body>

</html>