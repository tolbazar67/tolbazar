<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'branch_id',
        'content',
        'media_path',
        'shares_count',
    ];

    // Author relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Seller relation
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Branch relation (Each branch can create posts)
    public function branch()
    {
        return $this->belongsTo(SellerBranch::class, 'branch_id');
    }

    // Comments relation (Only main parent comments)
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    // All comments including replies
    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    // Likes / Reactions relation
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    // Saved bookmarks relation
    public function savedByUsers()
    {
        return $this->hasMany(SavedPost::class);
    }

    // Shares relation
    public function shares()
    {
        return $this->hasMany(SharedPost::class);
    }

    // Reports polymorphic relation
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // Helper to check if liked by specific user
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    // Helper to check if saved by specific user
    public function isSavedBy($userId)
    {
        return $this->savedByUsers()->where('user_id', $userId)->exists();
    }
}