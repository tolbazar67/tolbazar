<div class="bg-gray-50 p-3 rounded-md mb-2">
    <div class="flex justify-between items-center mb-1">
        <span class="text-xs font-bold text-gray-700">{{ $comment->user->name ?? 'User' }}</span>
        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <p class="text-sm text-gray-800">{{ $comment->body }}</p>
</div>