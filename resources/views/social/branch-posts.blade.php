@extends('social.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Branch Posts</h1>
    
    @forelse($posts as $post)
        @include('social.partials.post-card', ['post' => $post])
    @empty
        <p class="text-center text-gray-500">No branch-specific posts found.</p>
    @endforelse

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection