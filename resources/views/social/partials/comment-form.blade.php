<form action="{{ route('comments.store', $postId) }}" method="POST" class="mt-4">
    @csrf
    @if(isset($parentId))
        <input type="hidden" name="parent_id" value="{{ $parentId }}">
    @endif
    <div class="flex gap-2">
        <input type="text" name="body" placeholder="Write a comment..." required class="flex-1 border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Post</button>
    </div>
</form>