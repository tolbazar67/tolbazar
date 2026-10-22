@extends('social.layouts.app')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-gray-100 my-6"
     x-data="postEditor()">
    
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Edit Post</h1>
            <p class="text-sm text-gray-500 mt-1">Make modifications to your existing post.</p>
        </div>
        <a href="{{ route('posts.my') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
            &larr; Cancel
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md text-sm">
            <p class="font-bold mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold text-gray-700">Content</label>
                <div class="flex items-center space-x-2">
                    <div class="relative" x-data="{ openEmoji: false }">
                        <button type="button" @click="openEmoji = !openEmoji" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded-lg font-medium transition">
                            😊 Emoji
                        </button>
                        <div x-show="openEmoji" @click.away="openEmoji = false" class="absolute right-0 mt-2 p-2 bg-white border border-gray-200 rounded-xl shadow-lg grid grid-cols-6 gap-2 z-20 w-48 text-lg">
                            <template x-for="emoji in ['😀','😂','😍','🔥','🚀','👍','🎉','❤️','👏','😎','💡','✨']">
                                <button type="button" @click="addEmoji(emoji); openEmoji=false" class="hover:bg-gray-100 p-1 rounded text-center">
                                    <span x-text="emoji"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <button type="button" @click="addTag('#Trending')" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg font-medium transition">
                        # Hashtag
                    </button>
                </div>
            </div>

            <textarea name="content" rows="5" required 
                x-model="content"
                class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">{{ old('content', $post->content) }}</textarea>
            
            <div class="flex justify-between items-center text-xs text-gray-400 mt-1">
                <span>Detected Hashtags: <strong class="text-indigo-600" x-text="detectedHashtags"></strong></span>
                <span x-text="content.length + '/5000 characters'"></span>
            </div>
        </div>

        @if(isset($branches) && count($branches) > 0)
        <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
            <label class="block text-sm font-semibold text-indigo-900 mb-1">Post as Business Branch</label>
            <select name="branch_id" class="w-full border border-indigo-200 bg-white rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $post->branch_id) == $branch->id ? 'selected' : '' }}>
                        🏢 {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        @php
            $mediaFiles = json_decode($post->media_path, true) ?? ($post->media_path ? [$post->media_path] : []);
        @endphp

        @if(!empty($mediaFiles))
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <p class="text-xs font-semibold text-gray-500 mb-3">Current Media Attachments:</p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($mediaFiles as $media)
                        <div class="relative group h-24 bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                            @if(Str::endsWith($media, ['.mp4', '.mov', '.avi', '.webm']))
                                <video src="{{ asset('storage/' . $media) }}" class="w-full h-full object-cover"></video>
                            @else
                                <img src="{{ asset('storage/' . $media) }}" alt="Post media" class="w-full h-full object-cover">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Add More Media Files (Optional)</label>
            <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Click to upload new files</span></p>
                        <p class="text-xs text-gray-400">PNG, JPG, GIF, MP4, MOV (MAX. 20MB)</p>
                    </div>
                    <input type="file" name="media[]" multiple class="hidden" accept="image/*,video/*" @change="previewFiles" />
                </label>
            </div>

            <template x-if="previews.length > 0">
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <template x-for="(src, index) in previews" :key="index">
                        <div class="relative group h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                            <img :src="src" class="w-full h-full object-cover">
                            <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 text-xs opacity-80 hover:opacity-100 transition shadow">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-indigo-200 transition duration-200 text-sm">
            Update Post
        </button>
    </form>
</div>

<script>
    function postEditor() {
        return {
            content: `{!! addslashes(old('content', $post->content)) !!}`,
            previews: [],
            addEmoji(emoji) {
                this.content += emoji;
            },
            addTag(tag) {
                this.content += ' ' + tag + ' ';
            },
            get detectedHashtags() {
                let matches = this.content.match(/#([a-zA-Z0-9_]+)/g);
                return matches ? matches.join(', ') : 'None';
            },
            previewFiles(event) {
                const files = event.target.files;
                this.previews = [];
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push(e.target.result);
                    }
                    reader.readAsDataURL(files[i]);
                }
            },
            removeFile(index) {
                this.previews.splice(index, 1);
            }
        }
    }
</script>
@endsection