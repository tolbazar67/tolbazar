<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'ip_address', 'user_agent', 'device_type',
        'browser', 'platform', 'status', 'session_id', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'success' => 'success',
            'failed'  => 'danger',
            'logout'  => 'secondary',
            default   => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'success' => 'Successful',
            'failed'  => 'Failed',
            'logout'  => 'Logged Out',
            default   => 'Unknown',
        };
    }

    public function scopeRecent($query, int $limit = 20)
    {
        return $query->latest()->limit($limit);
    }
}
