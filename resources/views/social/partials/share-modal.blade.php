<div id="shareModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-bold mb-4">Share Post</h3>
        <textarea id="shareCaption" placeholder="Say something about this..." class="w-full border rounded p-2 mb-4 text-sm"></textarea>
        <div class="flex justify-end space-x-2">
            <button onclick="$('#shareModal').addClass('hidden')" class="px-4 py-2 bg-gray-200 rounded text-sm">Cancel</button>
            <button id="confirmShareBtn" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Share</button>
        </div>
    </div>
</div>