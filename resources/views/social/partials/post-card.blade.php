<div class="bg-white shadow rounded-xl mb-6 p-6 border border-gray-100" x-data="{ openComments: false }">
    <!-- User Info Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700">
                {{ substr($post->user->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-900">{{ $post->user->name ?? 'Unknown User' }}</h4>
                <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div>
            <!-- Follow Button Component Integrated Here -->
            @include('social.partials.follow-button', ['userId' => $post->user_id])
        </div>
    </div>

    <!-- Post Content with Hashtags Highlighting & Expand/Collapse Logic -->
<div class="mb-4" x-data="{ expanded: false, isOverflowing: false }" x-init="$nextTick(() => { isOverflowing = $refs.contentBox.scrollHeight > $refs.contentBox.clientHeight })">
    
    <!-- Content Box with 2-line clamp logic -->
    <div x-ref="contentBox" 
         :class="expanded ? 'line-clamp-none' : 'line-clamp-2'" 
         class="text-gray-800 whitespace-pre-line leading-relaxed text-sm sm:text-base overflow-hidden transition-all duration-300">
        {!! preg_replace('/#([a-zA-Z0-9_]+)/', '<span class="text-indigo-600 font-semibold hover:underline cursor-pointer">#$1</span>', e($post->content)) !!}
    </div>

    <!-- See More / See Less Button (Appears only if text has more than 2 lines) -->
    <button type="button" 
            x-show="isOverflowing" 
            @click="expanded = !expanded" 
            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 mt-1 focus:outline-none transition-colors" 
            style="display: none;">
        <span x-text="expanded ? 'See Less' : 'See More'"></span>
    </button>
</div>

    <!-- Swipeable Professional Multi-Media Carousel Section -->
    @php
        $mediaFiles = json_decode($post->media_path, true) ?? ($post->media_path ? [$post->media_path] : []);
        $count = count($mediaFiles);
    @endphp

    @if($count > 0)
        <div class="mb-4 rounded-2xl overflow-hidden bg-black relative shadow-inner group" 
             x-data="{ activeSlide: 0, total: {{ $count }} }">
            
            <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none scroll-smooth w-full" 
                 x-ref="slider" 
                 @scroll.debounce.30="activeSlide = Math.round($refs.slider.scrollLeft / $refs.slider.clientWidth)">
                
                @foreach($mediaFiles as $index => $media)
                    @php 
                        $extension = pathinfo($media, PATHINFO_EXTENSION);
                        $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'webm']);
                    @endphp
                    
                    <div class="w-full flex-shrink-0 snap-center flex items-center justify-center h-80 sm:h-96 relative bg-gray-950">
                        @if($isVideo)
                            <video src="{{ asset('storage/' . $media) }}" class="max-h-96 w-full object-contain" controls></video>
                        @else
                            <img src="{{ asset('storage/' . $media) }}" alt="Post media" class="max-h-96 w-full object-contain">
                        @endif
                    </div>
                @endforeach
            </div>

            @if($count > 1)
                <div class="absolute top-3 right-3 bg-black/60 text-white text-xs font-semibold px-2.5 py-1 rounded-full backdrop-blur-md z-10">
                    <span x-text="activeSlide + 1"></span> / <span x-text="total"></span>
                </div>
            @endif

            @if($count > 1)
                <button type="button" 
                        @click="activeSlide = (activeSlide > 0) ? activeSlide - 1 : total - 1; $refs.slider.scrollTo({left: activeSlide * $refs.slider.clientWidth, behavior: 'smooth'})" 
                        class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2.5 rounded-full shadow-lg transition opacity-0 group-hover:opacity-100 focus:opacity-100 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </button>

                <button type="button" 
                        @click="activeSlide = (activeSlide < total - 1) ? activeSlide + 1 : 0; $refs.slider.scrollTo({left: activeSlide * $refs.slider.clientWidth, behavior: 'smooth'})" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2.5 rounded-full shadow-lg transition opacity-0 group-hover:opacity-100 focus:opacity-100 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-1.5 z-10 bg-black/40 px-3.5 py-1.5 rounded-full backdrop-blur-md">
                    <template x-for="i in total" :key="i">
                        <button type="button" 
                                @click="activeSlide = i - 1; $refs.slider.scrollTo({left: (i - 1) * $refs.slider.clientWidth, behavior: 'smooth'})"
                                class="h-2 rounded-full transition-all duration-300"
                                :class="activeSlide === (i - 1) ? 'bg-indigo-400 w-4' : 'bg-white/60 w-2'">
                        </button>
                    </template>
                </div>
            @endif
        </div>
    @endif

    <!-- Reactions Count & Comments Summary Bar -->
    <div class="flex items-center justify-between text-xs text-gray-500 py-1">
        <span class="total-reactions-{{ $post->id }}">{{ $post->likes->count() }} Reactions</span>
        <button type="button" @click="openComments = true; loadComments({{ $post->id }})" class="hover:underline">
            <span class="total-comments-{{ $post->id }}">{{ $post->comments->count() }}</span> Comments
        </button>
    </div>

    <!-- Post Actions Footer -->
    <div class="flex items-center justify-between border-t border-b border-gray-100 py-2 my-2">
        
        <!-- Reaction Section -->
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

            <div class="relative inline-block" x-data="{ openReactionPopup: false }" @mouseenter="openReactionPopup = true" @mouseleave="openReactionPopup = false">
                <div x-show="openReactionPopup" 
                     x-transition:enter="transition ease-out duration-200"
                     class="absolute bottom-full left-0 pb-3 z-50" style="display: none;">
                    <div class="bg-white shadow-2xl rounded-full px-3.5 py-2 flex space-x-3 border border-gray-100 items-center">
                        <button type="button" @click="reactPost({{ $post->id }}, 'like'); openReactionPopup = false;" class="hover:scale-125 transition transform text-blue-600 p-1"><i class="fa-solid fa-thumbs-up text-xl"></i></button>
                        <button type="button" @click="reactPost({{ $post->id }}, 'love'); openReactionPopup = false;" class="hover:scale-125 transition transform text-red-500 p-1"><i class="fa-solid fa-heart text-xl"></i></button>
                        <button type="button" @click="reactPost({{ $post->id }}, 'haha'); openReactionPopup = false;" class="hover:scale-125 transition transform text-yellow-500 p-1"><i class="fa-solid fa-face-laugh text-xl"></i></button>
                        <button type="button" @click="reactPost({{ $post->id }}, 'wow'); openReactionPopup = false;" class="hover:scale-125 transition transform text-yellow-500 p-1"><i class="fa-solid fa-face-surprise text-xl"></i></button>
                        <button type="button" @click="reactPost({{ $post->id }}, 'sad'); openReactionPopup = false;" class="hover:scale-125 transition transform text-yellow-500 p-1"><i class="fa-solid fa-face-sad-tear text-xl"></i></button>
                        <button type="button" @click="reactPost({{ $post->id }}, 'angry'); openReactionPopup = false;" class="hover:scale-125 transition transform text-orange-600 p-1"><i class="fa-solid fa-face-angry text-xl"></i></button>
                    </div>
                </div>

                <button type="button" 
                        @click="reactPost({{ $post->id }}, '{{ $reactionType ? $reactionType : 'like' }}')" 
                        class="main-react-btn-{{ $post->id }} text-sm font-medium flex items-center space-x-1.5 transition {{ $userReaction ? $currentMeta['color'] : 'text-gray-600 hover:text-indigo-600' }}">
                    <i class="{{ $currentMeta['icon'] }} main-react-icon-{{ $post->id }} text-base"></i>
                    <span class="reaction-label main-react-text-{{ $post->id }}">{{ $currentMeta['label'] }}</span>
                </button>
            </div>
        </div>
        
        <!-- Save & Comment Modal Trigger Section -->
        @php
            $isSaved = \App\Models\SavedPost::where('user_id', auth()->id())->where('post_id', $post->id)->exists();
        @endphp

        <div class="flex space-x-4 text-sm text-gray-500">
<!-- Share Button Component with Full Context IDs -->
@php
    $shareParams = array_filter([
        'post_id' => $post->id,
        'user_id' => $post->user_id,
        'seller_id' => $post->seller_id ?? null,
        'branch_id' => $post->branch_id ?? null,
    ]);
    $shareUrl = url('/community/feed') . '?' . http_build_query($shareParams);
@endphp

<button type="button" @click="sharePost({{ $post->id }}, '{{ $shareUrl }}', '{{ addslashes(Str::limit($post->content, 80)) }}')" class="hover:text-indigo-600 transition flex items-center space-x-1 text-gray-500">
    <i class="fa-solid fa-share-nodes"></i> 
    <span class="shares-count-{{ $post->id }}">{{ $post->shares_count ?? 0 }}</span>
    <span>Share</span>
</button>

            <button type="button" onclick="toggleSavePost({{ $post->id }})" class="save-btn-{{ $post->id }} transition flex items-center space-x-1 {{ $isSaved ? 'text-amber-600 font-medium' : 'hover:text-indigo-600 text-gray-500' }}">
                <i class="save-icon-{{ $post->id }} {{ $isSaved ? 'fa-solid fa-bookmark text-amber-500' : 'fa-regular fa-bookmark' }}"></i> 
                <span class="save-text-{{ $post->id }}">{{ $isSaved ? 'Saved' : 'Save' }}</span>
            </button>
            <button type="button" @click="openComments = true; loadComments({{ $post->id }})" class="hover:text-indigo-600 transition flex items-center space-x-1">
                <i class="fa-regular fa-comment"></i> <span>Comment</span>
            </button>
        </div>
    </div>

    <!-- Instagram / Facebook Style Professional Popup Comment Modal -->
    <div x-show="openComments" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="openComments = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" 
         style="display: none;">
        
        <!-- Modal Card Container -->
        <div @click.outside="openComments = false" 
             class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                <div class="flex items-center space-x-2">
                    <h3 class="font-bold text-gray-900 text-base">Comments</h3>
                </div>
                <button type="button" @click="openComments = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Scrollable Comments Area -->
            <div id="comments-container-{{ $post->id }}" class="p-4 space-y-3.5 overflow-y-auto flex-grow bg-white scroll-smooth">
                <div class="text-center text-xs text-gray-400 py-6">Loading comments...</div>
            </div>

            <!-- Sticky Bottom Comment Input Box -->
            <div class="p-3.5 border-t border-gray-100 bg-gray-50 flex items-center space-x-3">
                <div class="h-9 w-9 rounded-full bg-indigo-100 flex-shrink-0 flex items-center justify-center font-bold text-xs text-indigo-700 shadow-inner">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-grow flex items-center bg-white rounded-full border border-gray-200 px-4 py-2 focus-within:ring-2 focus-within:ring-indigo-500 shadow-xs">
                    <input type="text" 
                           id="comment-input-{{ $post->id }}" 
                           @keydown.enter="submitComment({{ $post->id }})"
                           placeholder="Add a comment..." 
                           class="w-full bg-transparent border-none text-sm focus:outline-none text-gray-700 placeholder-gray-400">
                    <button type="button" onclick="submitComment({{ $post->id }})" class="text-indigo-600 hover:text-indigo-800 ml-2 font-medium transition transform hover:scale-110">
                        <i class="fa-solid fa-paper-plane text-base"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
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

// Handle Multi-Reactions via AJAX
function reactPost(postId, reactionType) {
    $.ajax({
        url: `/community/posts/${postId}/reaction`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', reaction_type: reactionType },
        success: function(response) {
            if(response.success) {
                $(`.total-reactions-${postId}`).text(response.data.total_reactions + ' Reactions');
                const $btn = $(`.main-react-btn-${postId}`);
                const $icon = $(`.main-react-icon-${postId}`);
                const $text = $(`.main-react-text-${postId}`);

                $btn.removeClass('text-blue-600 text-red-500 text-yellow-500 text-orange-600 text-gray-600 hover:text-indigo-600');
                $icon.removeClass('fa-solid fa-regular fa-thumbs-up fa-heart fa-face-laugh fa-face-surprise fa-face-sad-tear fa-face-angry');

                const activeType = response.data.reaction_type;
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
        }
    });
}

// Handle Save/Unsave Post via AJAX
if (typeof toggleSavePost !== 'function') {
    function toggleSavePost(postId) {
        $.ajax({
            url: `/community/posts/${postId}/save`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    const $btn = $(`.save-btn-${postId}`);
                    const $icon = $(`.save-icon-${postId}`);
                    const $text = $(`.save-text-${postId}`);
                    if (response.is_saved) {
                        $icon.removeClass('fa-regular text-gray-500').addClass('fa-solid fa-bookmark text-amber-500');
                        $text.text('Saved');
                        $btn.addClass('text-amber-600 font-medium').removeClass('text-gray-500 hover:text-indigo-600');
                    } else {
                        $icon.removeClass('fa-solid fa-bookmark text-amber-500').addClass('fa-regular fa-bookmark');
                        $text.text('Save');
                        $btn.removeClass('text-amber-600 font-medium').addClass('text-gray-500 hover:text-indigo-600');
                    }
                }
            }
        });
    }
}

// Load Comments via AJAX inside Modal
// Load Comments via AJAX inside Modal
function loadComments(postId) {
    $.ajax({
        url: `/community/posts/${postId}/comments`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let commentsHtml = '';
                const comments = response.data.data;
                const currentUserId = {{ auth()->id() ?? 'null' }};
                
                if (comments.length === 0) {
                    commentsHtml = '<div class="text-center py-10"><p class="text-sm text-gray-500 font-medium">No comments yet</p><p class="text-xs text-gray-400 mt-1">Start the conversation below.</p></div>';
                } else {
                    commentsHtml = comments.map(comment => {
                        const isOwner = comment.user_id === currentUserId;
                        
                        return `
                            <div class="flex items-start space-x-3 text-sm group relative" id="comment-row-${comment.id}">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex-shrink-0 flex items-center justify-center font-bold text-xs text-indigo-700 shadow-xs">
                                    ${comment.user ? comment.user.name.charAt(0) : 'U'}
                                </div>
                                <div class="flex-grow">
                                    <div class="bg-gray-50 group-hover:bg-gray-100/70 transition p-3 rounded-2xl border border-gray-100 shadow-2xs relative">
                                        <div class="flex items-center justify-between">
                                            <h6 class="font-bold text-xs text-gray-900">${comment.user ? comment.user.name : 'Unknown'}</h6>
                                            
                                            <!-- 3-Dot Menu for Owner -->
                                            ${isOwner ? `
                                                <div class="relative" x-data="{ openMenu: false }">
                                                    <button type="button" @click="openMenu = !openMenu" class="text-gray-400 hover:text-gray-600 p-1 rounded-full transition">
                                                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                                    </button>
                                                    <div x-show="openMenu" @click.outside="openMenu = false" style="display: none;" class="absolute right-0 mt-1 w-28 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-20">
                                                        <button type="button" onclick="enableEditComment(${comment.id}, '${escapeHtml(comment.body)}')" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 flex items-center space-x-1.5">
                                                            <i class="fa-regular fa-pen-to-square"></i> <span>Edit</span>
                                                        </button>
                                                        <button type="button" onclick="deleteComment(${comment.id}, ${postId})" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 flex items-center space-x-1.5">
                                                            <i class="fa-regular fa-trash-can"></i> <span>Delete</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            ` : ''}
                                        </div>
                                        
                                        <!-- Comment Body View -->
                                        <p class="text-xs text-gray-700 mt-1 leading-relaxed comment-body-${comment.id}">${comment.body}</p>
                                        
                                        <!-- Inline Edit Form (Hidden by Default) -->
                                        <div id="edit-form-${comment.id}" class="hidden mt-2">
                                            <textarea class="w-full p-2 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none" rows="2" id="edit-textarea-${comment.id}">${comment.body}</textarea>
                                            <div class="flex justify-end space-x-2 mt-1.5">
                                                <button type="button" onclick="cancelEditComment(${comment.id})" class="px-2.5 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 transition">Cancel</button>
                                                <button type="button" onclick="updateComment(${comment.id}, ${postId})" class="px-2.5 py-1 text-xs bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white transition">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }).join('');
                }
                $(`#comments-container-${postId}`).html(commentsHtml);
                $(`.total-comments-${postId}`).text(response.data.total || comments.length);
            }
        }
    });
}

// Helper to escape quotes for JS parameters
function escapeHtml(text) {
    return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// Show inline text area for editing comment
function enableEditComment(commentId, currentBody) {
    $(`.comment-body-${commentId}`).hide();
    $(`#edit-form-${commentId}`).removeClass('hidden');
}

// Cancel editing
function cancelEditComment(commentId) {
    $(`.comment-body-${commentId}`).show();
    $(`#edit-form-${commentId}`).addClass('hidden');
}

// Submit updated comment via AJAX
function updateComment(commentId, postId) {
    const newBody = $(`#edit-textarea-${commentId}`).val().trim();
    if (!newBody) return;

    $.ajax({
        url: `/community/comments/${commentId}`,
        type: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            body: newBody
        },
        success: function(response) {
            if (response.success) {
                loadComments(postId);
            }
        },
        error: function(xhr) {
            alert('Failed to update comment.');
        }
    });
}

// Delete Comment with Professional Warning Modal
function deleteComment(commentId, postId) {
    // Check if warning modal already exists, if not create it dynamically inside body
    if ($('#delete-warning-modal').length === 0) {
        const modalHtml = `
            <div id="delete-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" style="display: none;">
                <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden p-6 text-center transform transition-all">
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 text-xl shadow-inner">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Delete Comment?</h3>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Are you sure you want to delete this comment? This action cannot be undone and it will be permanently removed.
                    </p>
                    <div class="flex space-x-3">
                        <button type="button" id="cancel-delete-btn" class="flex-1 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                            Cancel
                        </button>
                        <button type="button" id="confirm-delete-btn" class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-md transition">
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('body').append(modalHtml);
    }

    // Show modal smoothly
    $('#delete-warning-modal').fadeIn(200);

    // Handle Cancel Click
    $('#cancel-delete-btn').off('click').on('click', function() {
        $('#delete-warning-modal').fadeOut(200);
    });

    // Handle Background Overlay Click to Close
    $('#delete-warning-modal').off('click').on('click', function(e) {
        if ($(e.target).is('#delete-warning-modal')) {
            $('#delete-warning-modal').fadeOut(200);
        }
    });

    // Handle Confirm Delete Click
    $('#confirm-delete-btn').off('click').on('click', function() {
        $('#delete-warning-modal').fadeOut(200);

        $.ajax({
            url: `/community/comments/${commentId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    // Reload comment list and counter smoothly
                    loadComments(postId);
                }
            },
            error: function(xhr) {
                alert('Failed to delete comment. Please try again.');
            }
        });
    });
}


// Submit Comment via AJAX and Refresh List
function submitComment(postId) {
    const inputField = $(`#comment-input-${postId}`);
    const body = inputField.val().trim();

    if (!body) return;

    $.ajax({
        url: `/community/posts/${postId}/comments`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            body: body
        },
        success: function(response) {
            inputField.val('');
            loadComments(postId); 
        },
        error: function(xhr) {
            alert('Failed to post comment. Please try again.');
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get('post_id');
    
    if (postId) {
        // Find the post element (assume you have a wrapper like id="post-{id}")
        const targetPost = document.getElementById(`post-${postId}`);
        if (targetPost) {
            targetPost.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Add a temporary highlight border effect
            targetPost.classList.add('ring-4', 'ring-indigo-400', 'transition-all', 'duration-500');
            setTimeout(() => {
                targetPost.classList.remove('ring-4', 'ring-indigo-400');
            }, 3000);
        }
    }
});


// Handle Share Post via Web Share API & AJAX
function sharePost(postId, postUrl, postTitle) {
    // 1. Check if Web Share API is supported (Mobile/Supported browsers)
    if (navigator.share) {
        navigator.share({
            title: 'Post',
            text: postTitle,
            url: postUrl,
        })
        .then(() => {
            recordShareOnBackend(postId);
        })
        .catch((error) => {
            console.log('Error sharing:', error);
        });
    } else {
        // Fallback for desktop browsers (Copy link to clipboard)
        navigator.clipboard.writeText(postUrl).then(() => {
            // Elegant Toast or Alert fallback
            alert('Post link copied to clipboard!');
            recordShareOnBackend(postId);
        }).catch(() => {
            alert('Failed to copy link.');
        });
    }
}

// Helper to notify Laravel backend about the share
function recordShareOnBackend(postId) {
    $.ajax({
        url: `/community/posts/${postId}/share`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            caption: null
        },
        success: function(response) {
            if (response.success) {
                const $countSpan = $(`.shares-count-${postId}`);
                let currentCount = parseInt($countSpan.text()) || 0;
                $countSpan.text(currentCount + 1);
            }
        }
    });
}

</script>
@endsection