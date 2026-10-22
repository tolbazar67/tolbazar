@extends('social.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="flex items-center justify-between mb-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">My Published Posts</h1>
            <p class="text-sm text-gray-500 mt-1">Manage, update, or remove your community contributions.</p>
        </div>
        <a href="{{ route('posts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow transition">
            + Create New Post
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-md text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($posts as $post)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative">
                <div class="absolute top-6 right-6 flex items-center space-x-2">
                    <a href="{{ route('posts.edit', $post->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Post">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                    
                    <!-- Delete Form with Custom Modal Trigger -->
                    <form id="delete-post-form-{{ $post->id }}" action="{{ route('posts.destroy', $post->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDeletePost({{ $post->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete Post">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>

                @include('social.partials.post-card', ['post' => $post])
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100 p-8 shadow-sm">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <h3 class="text-lg font-bold text-gray-700">No posts found</h3>
                <p class="text-sm text-gray-500 mt-1 mb-4">You haven't shared anything on the social community yet.</p>
                <a href="{{ route('posts.create') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm px-4 py-2 rounded-lg transition">
                    Create Your First Post
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
// Professional Delete Warning Modal for Posts
function confirmDeletePost(postId) {
    if ($('#post-delete-warning-modal').length === 0) {
        const modalHtml = `
            <div id="post-delete-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" style="display: none;">
                <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden p-6 text-center transform transition-all">
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 text-xl shadow-inner">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Delete Post?</h3>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Are you sure you want to delete this post? This action cannot be undone and all its media and interactions will be removed.
                    </p>
                    <div class="flex space-x-3">
                        <button type="button" id="cancel-post-delete-btn" class="flex-1 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                            Cancel
                        </button>
                        <button type="button" id="confirm-post-delete-btn" class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-md transition">
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('body').append(modalHtml);
    }

    // Show modal smoothly
    $('#post-delete-warning-modal').fadeIn(200);

    // Handle Cancel Click
    $('#cancel-post-delete-btn').off('click').on('click', function() {
        $('#post-delete-warning-modal').fadeOut(200);
    });

    // Handle Background Overlay Click to Close
    $('#post-delete-warning-modal').off('click').on('click', function(e) {
        if ($(e.target).is('#post-delete-warning-modal')) {
            $('#post-delete-warning-modal').fadeOut(200);
        }
    });

    // Handle Confirm Delete Click
    $('#confirm-post-delete-btn').off('click').on('click', function() {
        $('#post-delete-warning-modal').fadeOut(200);
        // Submit the specific form programmatically
        $(`#delete-post-form-${postId}`).submit();
    });
}
</script>
@endsection