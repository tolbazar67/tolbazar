<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'wallet_id', 'event_type', 'severity',
        'description', 'ip_address', 'user_agent', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeCritical($q) { return $q->where('severity', 'critical'); }
}
