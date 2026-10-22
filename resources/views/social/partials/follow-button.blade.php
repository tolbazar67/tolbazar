@props(['userId'])

@if(auth()->id() !== $userId)

    @php
        $currentUserId = auth()->id();
        $targetUserId = $userId;

        $isFollowing = \App\Models\Follow::where('follower_id', $currentUserId)
            ->where('following_id', $targetUserId)
            ->exists();

        $isFollower = \App\Models\Follow::where('follower_id', $targetUserId)
            ->where('following_id', $currentUserId)
            ->exists();

        if ($isFollowing && $isFollower) {
            $buttonText = 'Following';
            $buttonClass = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
            $iconClass = 'fa-solid fa-user-check';

        } elseif ($isFollowing) {
            $buttonText = 'Following';
            $buttonClass = 'bg-indigo-50 text-indigo-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200';
            $iconClass = 'fa-solid fa-check';

        } elseif ($isFollower) {
            $buttonText = 'Follow Back';
            $buttonClass = 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-xs';
            $iconClass = 'fa-solid fa-user-plus';

        } else {
            $buttonText = 'Follow';
            $buttonClass = 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100';
            $iconClass = 'fa-solid fa-plus';
        }
    @endphp


    <button
        type="button"
        data-follow-user="{{ $targetUserId }}"
        onclick="handleFollowClick({{ $targetUserId }}, this)"
        class="follow-btn px-3.5 py-1.5 text-xs font-semibold rounded-full transition-all duration-200 flex items-center space-x-1.5 border border-transparent {{ $buttonClass }}"
    >

        <i class="follow-icon {{ $iconClass }}"></i>

        <span class="follow-text">
            {{ $buttonText }}
        </span>

    </button>

@endif