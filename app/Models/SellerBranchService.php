<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerBranchService extends Model
{
    use HasFactory;

    protected $table = 'seller_branch_services';

    protected $fillable = [
        'seller_id',
        'branch_id',
        'service_id',
        'is_enabled',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'meta'       => 'array',
        ];
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(SellerBranch::class, 'branch_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', 1);
    }

    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', 0);
    }

    // ============================================================
    // HELPERS
    // ============================================================

    public function enable(): bool
    {
        return $this->update(['is_enabled' => 1]);
    }

    public function disable(): bool
    {
        return $this->update(['is_enabled' => 0]);
    }
}