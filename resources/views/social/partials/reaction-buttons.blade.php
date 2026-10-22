<div class="flex items-center space-x-3">
    
    @php
        $userReaction = $post->likes->where('user_id', auth()->id())->first();
        $reactionType = $userReaction ? strtolower($userReaction->reaction_type ?? 'like') : null;
        
        $reactionMeta = [
            'like' => ['icon' => 'fa-solid fa-thumbs-up', 'color' => 'text-blue-600', 'label' => 'Like'],
            'love' => ['icon' => 'fa-solid fa-heart', 'color' => 'text-red-500', 'label' => 'Love'],
            'haha' => ['icon' => 'fa-solid fa-face-laugh', 'color' => 'text-yellow-500', 'label' => 'Haha'],
            'wow' => ['icon' => 'fa-solid fa-face-surprise', 'color' => 'text-yellow-500', 'label' => 'Wow'],
            'sad' => ['icon' => 'fa-solid fa-face-sad-tear', 'color' => 'text-yellow-500', 'label' => 'Sad'],
            'angry' => ['icon' => 'fa-solid fa-face-angry', 'color' => 'text-orange-600', 'label' => 'Angry'],
        ];

        $currentMeta = $reactionMeta[$reactionType] ?? ['icon' => 'fa-regular fa-thumbs-up', 'color' => 'text-gray-600', 'label' => 'Like'];
    @endphp

    <!-- Reaction Container -->
    <div class="relative inline-block reaction-container-{{ $post->id }}" x-data="{ openReactionPopup: false }" @mouseenter="openReactionPopup = true" @mouseleave="openReactionPopup = false">
        
        <!-- Floating Popup Box -->
        <div x-show="openReactionPopup" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="absolute bottom-full left-0 pb-3 z-50" style="display: none;">
            
            <div class="bg-white shadow-2xl rounded-full px-3.5 py-2 flex space-x-3 border border-gray-100 items-center">
                <button type="button" @click="reactPost({{ $post->id }}, 'like'); openReactionPopup = false;" class="hover:scale-125 transition transform text-blue-600 p-1" title="Like">
                    <i class="fa-solid fa-thumbs-up text-xl"></i>
                </button>
                <button type="button" @click="reactPost({{ $post->id }}, 'love'); openReactionPopup = false;" class="hover:scale-125 transition transform text-red-500 p-1" title="Love">
                    <i class="fa-solid fa-heart text-xl"></i>
                </button>
                <button type="button" @click="reactPost({{ $post->id }}, 'haha'); openReactionPopup = false;" class="hover:scale-125 transition transform text-yellow-500 p-1" title="Haha">
                    <i class="fa-solid fa-face-laugh text-xl"></i>
                </button>
                <button type="button" @click="reactPost({{ $post->id }}, 'wow'); openReactionPopup = false;" class="hover:scale-125 transition transform text-yellow-500 p-1" title="Wow">
                    <i class="fa-solid fa-face-surprise text-xl"></i>
                </button>
                <button type="button" @click="reactPost({{ $post->id }}, 'sad'); openReactionPopup = false;" class="hover:scale-125 transition transform text-yellow-500 p-1" title="Sad">
                    <i class="fa-solid fa-face-sad-tear text-xl"></i>
                </button>
                <button type="button" @click="reactPost({{ $post->id }}, 'angry'); openReactionPopup = false;" class="hover:scale-125 transition transform text-orange-600 p-1" title="Angry">
                    <i class="fa-solid fa-face-angry text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Main Reaction Button -->
        <button type="button" 
                @click="reactPost({{ $post->id }}, '{{ $reactionType ? $reactionType : 'like' }}')" 
                class="main-react-btn-{{ $post->id }} text-sm font-medium flex items-center space-x-1.5 transition {{ $userReaction ? $currentMeta['color'] : 'text-gray-600 hover:text-indigo-600' }}">
            <i class="{{ $currentMeta['icon'] }} main-react-icon-{{ $post->id }} text-base"></i>
            <span class="reaction-label main-react-text-{{ $post->id }}">{{ $currentMeta['label'] }}</span>
        </button>
    </div>

    <!-- Total Reactions Count -->
    <span class="text-xs text-gray-500 total-reactions-{{ $post->id }}">
        {{ $post->likes->count() }} Reactions
    </span>
</div>

@section('scripts')
<script>
const reactionConfigs = {
    'like': { icon: 'fa-solid fa-thumbs-up', color: 'text-blue-600', label: 'Like' },
    'love': { icon: 'fa-solid fa-heart', color: 'text-red-500', label: 'Love' },
    'haha': { icon: 'fa-solid fa-face-laugh', color: 'text-yellow-500', label: 'Haha' },
    'wow': { icon: 'fa-solid fa-face-surprise', color: 'text-yellow-500', label: 'Wow' },
    'sad': { icon: 'fa-solid fa-face-sad-tear', color: 'text-yellow-500', label: 'Sad' },
    'angry': { icon: 'fa-solid fa-face-angry', color: 'text-orange-600', label: 'Angry' },
    'default': { icon: 'fa-regular fa-thumbs-up', color: 'text-gray-600', label: 'Like' }
};

function reactPost(postId, reactionType) {
    $.ajax({
        url: `/community/posts/${postId}/reaction`,
        type: 'POST',
        data: { 
            _token: '{{ csrf_token() }}',
            reaction_type: reactionType 
        },
        success: function(response) {
            if(response.success) {
                // 1. Update Total Reaction Count
                $(`.total-reactions-${postId}`).text(response.data.total_reactions + ' Reactions');

                // 2. Dynamic Icon, Text & Color update without reload
                const $btn = $(`.main-react-btn-${postId}`);
                const $icon = $(`.main-react-icon-${postId}`);
                const $text = $(`.main-react-text-${postId}`);

                // Purano sabai color classes hataune
                $btn.removeClass('text-blue-600 text-red-500 text-yellow-500 text-orange-600 text-gray-600 hover:text-indigo-600');
                
                // Icon ko purano classes haru clear garne
                $icon.removeClass('fa-solid fa-regular fa-thumbs-up fa-heart fa-face-laugh fa-face-surprise fa-face-sad-tear fa-face-angry');

                const activeType = response.data.reaction_type; // null (if removed) or reaction string

                if (activeType && reactionConfigs[activeType]) {
                    const config = reactionConfigs[activeType];
                    $btn.addClass(config.color);
                    $icon.addClass(config.icon);
                    $text.text(config.label);
                } else {
                    const defConfig = reactionConfigs['default'];
                    $btn.addClass(defConfig.color + ' hover:text-indigo-600');
                    $icon.addClass(defConfig.icon);
                    $text.text(defConfig.label);
                }
            }
        },
        error: function(xhr) {
            console.error('Error reacting to post');
        }
    });
}
</script>
@endsection