<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tolbazar') }} - Community</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js (Crucial for Swipeable Carousel & Sliders) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('social.feed') }}" class="text-xl font-bold text-indigo-600">Tolbazar Community</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('social.feed') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Feed</a>
                        <a href="{{ route('posts.my') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">My Posts</a>
                        <a href="{{ route('posts.saved') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Saved Posts</a>
                        @if(auth()->user()->seller)
                            <a href="{{ route('posts.branches') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Branch Posts</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('posts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Create Post</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
function handleFollowClick(userId, clickedButton) {

    const token = $('meta[name="csrf-token"]').attr('content');

    // Same user ko page ma bhako sabai follow buttons
    const $buttons = $(`.follow-btn[data-follow-user="${userId}"]`);

    // Prevent multiple clicks
    $buttons.prop('disabled', true).addClass('opacity-60 cursor-not-allowed');

    $.ajax({
        url: `/community/users/${userId}/follow`,
        type: 'POST',

        data: {
            _token: token
        },

        success: function(response) {

            if (!response.success) {
                return;
            }

            const isFollowing = response.is_following;

            // Same user ko page bhari bhako sabai buttons update garne
            $buttons.each(function() {

                const $btn = $(this);
                const $icon = $btn.find('.follow-icon');
                const $text = $btn.find('.follow-text');

                // Purano classes remove
                $btn.removeClass(
                    'bg-indigo-50 ' +
                    'text-indigo-600 ' +
                    'hover:bg-indigo-100 ' +
                    'hover:bg-red-50 ' +
                    'hover:text-red-600 ' +
                    'hover:border-red-200 ' +
                    'bg-indigo-600 ' +
                    'text-white ' +
                    'hover:bg-indigo-700 ' +
                    'bg-gray-100 ' +
                    'text-gray-700 ' +
                    'hover:bg-gray-200 ' +
                    'shadow-xs'
                );


                // Icon classes reset
                $icon.removeClass(
                    'fa-solid ' +
                    'fa-plus ' +
                    'fa-check ' +
                    'fa-user-plus ' +
                    'fa-user-check'
                );


                if (isFollowing) {

                    /*
                     * Following
                     */

                    $text.text('Following');

                    $icon.addClass(
                        'fa-solid fa-check'
                    );

                    $btn.addClass(
                        'bg-indigo-50 ' +
                        'text-indigo-600 ' +
                        'hover:bg-red-50 ' +
                        'hover:text-red-600 ' +
                        'hover:border-red-200'
                    );

                } else {

                    /*
                     * Follow
                     */

                    $text.text('Follow');

                    $icon.addClass(
                        'fa-solid fa-plus'
                    );

                    $btn.addClass(
                        'bg-indigo-50 ' +
                        'text-indigo-600 ' +
                        'hover:bg-indigo-100'
                    );
                }

            });


            /*
             * Optional follower count update
             * यदि page मा follower count राखेको छ भने
             */

            $(`.follower-count-${userId}`).text(
                response.follower_count
            );

        },

        error: function(xhr) {

            if (xhr.status === 400 || xhr.status === 422) {

                alert(
                    xhr.responseJSON?.message ||
                    'Action failed.'
                );

            } else if (xhr.status === 401) {

                alert(
                    'Please login first to follow users.'
                );

            } else {

                alert(
                    'Something went wrong.'
                );
            }

        },

        complete: function() {

            // Enable again
            $buttons
                .prop('disabled', false)
                .removeClass('opacity-60 cursor-not-allowed');

        }

    });

}
</script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>