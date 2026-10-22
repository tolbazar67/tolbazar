<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\PostLike;
use App\Models\SavedPost;
use App\Models\SharedPost;
use App\Models\Follow;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\SellerBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SocialCommunityController extends Controller
{

public function index(Request $request)
{
    $user = Auth::user();

    // Get following user IDs for privacy filtering
    $followingIds = Follow::where('follower_id', $user->id)
        ->pluck('following_id')
        ->toArray();

    // Build the query
    $posts = Post::with([
            'user',
            'seller',
            'branch',
            'likes',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with('replies');
            }
        ])
        ->where(function ($query) use ($user, $followingIds) {

            // Public posts
            $query->where('privacy', 'public')

                // Followers only posts
                ->orWhere(function ($q) use ($followingIds) {
                    $q->where('privacy', 'followers_only')
                        ->whereIn('user_id', $followingIds);
                })

                // Own private posts
                ->orWhere(function ($q) use ($user) {
                    $q->where('privacy', 'private')
                        ->where('user_id', $user->id);
                });

        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Get user's branch if they are a seller
    $userBranch = null;

    if ($user->seller) {
        $userBranch = $user->seller->branches()->first();
    }

    // Return VIEW
    return view('social.index', compact('posts', 'userBranch'));
}

    public function createPost()
    {
        $user = Auth::user();
        
        // Get branches if user is a seller
        $branches = [];
        if ($user->seller) {
            $branches = $user->seller->branches()->get();
        }
        
        // Return VIEW instead of JSON
        return view('social.create', compact('branches'));
    }

public function storePost(Request $request)
{
    $validator = Validator::make($request->all(), [
        'content' => 'required|string|max:5000',
        'branch_id' => 'nullable|exists:seller_branches,id',
        'media' => 'nullable|array|max:5', // Max 5 images/files at once
        'media.*' => 'file|max:20480|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = Auth::user();
    
    // Branch resolve logic
    $branchId = $request->branch_id;
    if (!$branchId && $user->active_mode === 'seller' && $user->sellerProfile) {
        $branchId = session('active_branch_id');
    }

    // Verify branch belongs to user's seller account
    if ($branchId && $user->sellerProfile) {
        $branch = \App\Models\SellerBranch::find($branchId);
        if (!$branch || $branch->seller_id !== $user->sellerProfile->id) {
            return redirect()->back()->with('error', 'Invalid branch access')->withInput();
        }
    }

    // Handle Multiple Media Uploads
    $mediaPaths = [];
    if ($request->hasFile('media')) {
        foreach ($request->file('media') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('social/posts', $filename, 'public');
            $mediaPaths[] = $path;
        }
    }

    // Extract Hashtags from content (e.g., #Laravel, #Developer)
    preg_match_all('/#([a-zA-Z0-9_]+)/', $request->content, $matches);
    $hashtags = !empty($matches[1]) ? json_encode($matches[1]) : null;

    // Create Post with JSON media paths & hashtags
    $post = \App\Models\Post::create([
        'user_id' => $user->id,
        'seller_id' => $user->sellerProfile ? $user->sellerProfile->id : null,
        'branch_id' => $branchId,
        'content' => $request->content,
        'media_path' => !empty($mediaPaths) ? json_encode($mediaPaths) : null, 
        'hashtags' => $hashtags, 
    ]);

    return redirect()->route('social.feed')->with('success', 'Post created successfully under your active branch!');
}

public function editPost($id)
    {
        $user = Auth::user();
        
        $post = Post::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // Get branches if user is a seller (Using sellerProfile relation to match your setup)
        $branches = [];
        if ($user->sellerProfile) {
            $branches = $user->sellerProfile->branches()->get();
        } elseif ($user->seller) { // Fallback incase relation name is different
            $branches = $user->seller->branches()->get();
        }
        
        return view('social.edit', compact('post', 'branches'));
    }

public function updatePost(Request $request, $id)
{
    $user = Auth::user();
    
    $post = Post::where('id', $id)
        ->where('user_id', $user->id)
        ->firstOrFail();
        
    $validator = Validator::make($request->all(), [
        'content' => 'required|string|max:5000',
        'branch_id' => 'nullable|exists:seller_branches,id',
        'media' => 'nullable|array|max:5', // Max 5 files at once
        'media.*' => 'file|max:20480|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Determine branch_id
    $branchId = $request->branch_id;
    if (!$branchId && $user->active_mode === 'seller' && $user->sellerProfile) {
        $branchId = session('active_branch_id');
    }

    // Verify branch belongs to user's seller account
    $sellerProfile = $user->sellerProfile ?? $user->seller;
    if ($branchId && $sellerProfile) {
        $branch = SellerBranch::find($branchId);
        if (!$branch || $branch->seller_id !== $sellerProfile->id) {
            return redirect()->back()
                ->with('error', 'Invalid branch access')
                ->withInput();
        }
    }

    // Handle Media Upload Replacement / Addition
    $mediaPaths = json_decode($post->media_path, true) ?? ($post->media_path ? [$post->media_path] : []);

    if ($request->hasFile('media')) {
        // Option A: Purana files sabai hataune naya le replace garni vaye, yo uncomment garnu:
        /*
        foreach ($mediaPaths as $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
        $mediaPaths = [];
        */

        // Naya files push garne
        foreach ($request->file('media') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('social/posts', $filename, 'public');
            $mediaPaths[] = $path;
        }
    }

    // Extract Hashtags from content
    preg_match_all('/#([a-zA-Z0-9_]+)/', $request->content, $matches);
    $hashtags = !empty($matches[1]) ? json_encode($matches[1]) : null;

    // Update post attributes
    $post->content = $request->content;
    $post->seller_id = $sellerProfile ? $sellerProfile->id : null;
    $post->branch_id = $branchId;
    $post->media_path = !empty($mediaPaths) ? json_encode($mediaPaths) : null;
    if (Schema::hasColumn('posts', 'hashtags')) {
        $post->hashtags = $hashtags;
    }
    $post->save();

    return redirect()->route('posts.my')
        ->with('success', 'Post updated successfully under your active branch!');
}

public function destroyPost($id)
    {
        $user = Auth::user();
        
        // Find post ensuring it belongs to the authenticated user OR their seller store/branches
        $post = Post::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                
                // Yadi user ko seller profile cha vane, tyo seller ko under baneko post haru pani delete garna milne banaune
                if ($user->sellerProfile) {
                    $query->orWhere('seller_id', $user->sellerProfile->id);
                } elseif ($user->seller) {
                    $query->orWhere('seller_id', $user->seller->id);
                }
            })
            ->firstOrFail();
            
        // Delete associated media file from storage if it exists
        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }
        
        // Delete the post record from database
        $post->delete();

        // Redirect back to 'my posts' or feed with success message
        return redirect()->route('posts.my')
            ->with('success', 'Post deleted successfully!');
    }

public function myPosts(Request $request)
    {
        $user = Auth::user();
        
        $posts = Post::with(['user', 'seller', 'branch', 'likes', 'comments' => function($query) {
                $query->whereNull('parent_id')->with('replies');
            }])
            ->where(function($query) use ($user) {
                if ($user->active_mode === 'seller') {
                    // Seller mode ma cha vane active branch ko matra posts dekhaune
                    $activeBranchId = session('active_branch_id');
                    $sellerProfile = $user->sellerProfile ?? $user->seller;

                    if ($activeBranchId) {
                        // Strictly active branch ID sanga match garne
                        $query->where('branch_id', $activeBranchId);
                    } elseif ($sellerProfile) {
                        // Fallback: yadi branch select vako chaina vane seller ko sabai posts
                        $query->where('seller_id', $sellerProfile->id);
                    }
                } else {
                    // Personal mode ma cha vane user ko aafnai personal posts (branch_id null vako)
                    $query->where('user_id', $user->id)
                          ->whereNull('branch_id');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('social.my_posts', compact('posts'));
    }

    public function savedPostsFeed(Request $request)
    {
        $user = Auth::user();
        
        $savedPostIds = SavedPost::where('user_id', $user->id)
            ->pluck('post_id')
            ->toArray();
            
        $posts = Post::with(['user', 'seller', 'branch', 'likes', 'comments' => function($query) {
                $query->whereNull('parent_id')->with('replies');
            }])
            ->whereIn('id', $savedPostIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Return VIEW instead of JSON
        return view('social.saved_posts', compact('posts'));
    }

    public function toggleSavePost($postId)
    {
        $user = Auth::user();
        
        $post = Post::findOrFail($postId);
        
        $saved = SavedPost::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();
            
        if ($saved) {
            $saved->delete();
            $isSaved = false;
            $message = 'Post unsaved';
        } else {
            SavedPost::create([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
            $isSaved = true;
            $message = 'Post saved';
        }
        
        // Return JSON for AJAX
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_saved' => $isSaved
        ]);
    }

    /**
     * Toggle reaction on a post (AJAX)
     */
    public function toggleReaction(Request $request, $postId)
    {
        $validator = Validator::make($request->all(), [
            'reaction_type' => 'required|in:like,love,care,haha,wow,sad,angry'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        $post = Post::findOrFail($postId);
        
        $like = PostLike::where('post_id', $postId)
            ->where('user_id', $user->id)
            ->first();
            
        if ($like) {
            if ($like->reaction_type === $request->reaction_type) {
                $like->delete();
                $reactionType = null;
                $action = 'removed';
            } else {
                $like->reaction_type = $request->reaction_type;
                $like->save();
                $reactionType = $request->reaction_type;
                $action = 'updated';
            }
        } else {
            $like = PostLike::create([
                'post_id' => $postId,
                'user_id' => $user->id,
                'reaction_type' => $request->reaction_type
            ]);
            $reactionType = $request->reaction_type;
            $action = 'added';
        }
        
        $reactionCounts = PostLike::where('post_id', $postId)
            ->select('reaction_type', \DB::raw('count(*) as count'))
            ->groupBy('reaction_type')
            ->get()
            ->pluck('count', 'reaction_type')
            ->toArray();
            
        return response()->json([
            'success' => true,
            'message' => 'Reaction ' . $action . ' successfully',
            'data' => [
                'reaction_type' => $reactionType,
                'total_reactions' => array_sum($reactionCounts),
                'reaction_counts' => $reactionCounts
            ]
        ]);
    }

    public function getComments($postId, Request $request)
    {
        $post = Post::findOrFail($postId);
        
        $comments = Comment::with(['user', 'replies.user'])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

public function storeComment(Request $request, $postId)
{
    $validator = Validator::make($request->all(), [
        'body' => 'required|string|max:2000',
        'parent_id' => 'nullable|exists:comments,id'
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = Auth::user();
    $post = Post::findOrFail($postId);
    
    $comment = Comment::create([
        'post_id' => $postId,
        'user_id' => $user->id,
        'parent_id' => $request->parent_id,
        'body' => $request->body
    ]);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => $comment->load('user')
        ]);
    }

    return redirect()->route('posts.show', $postId)->with('success', 'Comment added successfully');
}

public function updateComment(Request $request, $commentId)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        $comment = Comment::where('id', $commentId)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        $comment->body = $request->body;
        $comment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment
        ]);
    }

    public function destroyComment(Request $request, $commentId)
    {
        $user = Auth::user();
        
        $comment = Comment::where('id', $commentId)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $postId = $comment->post_id;
        $comment->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        }

        return redirect()->route('posts.show', $postId)
            ->with('success', 'Comment deleted successfully');
    }

    public function sharePost(Request $request, $postId)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $post = Post::findOrFail($postId);
        
        $share = SharedPost::create([
            'user_id' => $user->id,
            'post_id' => $postId,
            'caption' => $request->caption
        ]);
        
        $post->increment('shares_count');

        return response()->json([
            'success' => true,
            'message' => 'Post shared successfully',
            'data' => $share
        ]);
    }

public function toggleFollow($userId)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    if ($user->id == $userId) {
        return response()->json([
            'success' => false,
            'message' => 'You cannot follow yourself'
        ], 400);
    }

    $targetUser = User::findOrFail($userId);

    $follow = Follow::where('follower_id', $user->id)
        ->where('following_id', $targetUser->id)
        ->first();

    if ($follow) {

        $follow->delete();

        $isFollowing = false;

        $message = 'Unfollowed successfully';

    } else {

        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $targetUser->id
        ]);

        $isFollowing = true;

        $message = 'Followed successfully';
    }


    $followerCount = Follow::where(
        'following_id',
        $targetUser->id
    )->count();


    return response()->json([
        'success' => true,
        'message' => $message,
        'is_following' => $isFollowing,
        'follower_count' => $followerCount,
        'user_id' => $targetUser->id
    ]);
}
  
  
}