<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'branch_name',
        'branch_code',
        'phone',
        'email',
        'address',
        'is_default',
        'manager_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'status'     => 'boolean',
        ];
    }

    // ============================================================
    // EXISTING RELATIONSHIPS
    // ============================================================

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(BranchStaff::class, 'branch_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'branch_id');
    }

    // ============================================================
    // NEW: SERVICE RELATIONSHIPS
    // ============================================================

    /**
     * All services this branch is linked to (via pivot)
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'seller_branch_services',
            'branch_id',
            'service_id'
        )->withPivot(['seller_id', 'is_enabled', 'meta'])
         ->withTimestamps();
    }

    /**
     * Only enabled services
     */
    public function enabledServices(): BelongsToMany
    {
        return $this->services()->wherePivot('is_enabled', 1);
    }

    /**
     * Pivot records for this branch
     */
    public function branchServices(): HasMany
    {
        return $this->hasMany(SellerBranchService::class, 'branch_id');
    }

    // ============================================================
    // NEW: PRODUCT RELATIONSHIPS
    // ============================================================

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'branch_id');
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', 1);
    }

    // ============================================================
    // HELPERS
    // ============================================================

    /**
     * Enable a service for this branch
     */
    public function enableService(int $serviceId, array $meta = []): void
    {
        $this->services()->syncWithoutDetaching([
            $serviceId => [
                'seller_id'  => $this->seller_id,
                'is_enabled' => 1,
                'meta'       => !empty($meta) ? json_encode($meta) : null,
            ],
        ]);
    }

    /**
     * Disable a service
     */
    public function disableService(int $serviceId): void
    {
        $this->services()->updateExistingPivot($serviceId, [
            'is_enabled' => 0,
        ]);
    }

    /**
     * Check if branch has a service enabled
     */
    public function hasService(int $serviceId): bool
    {
        return $this->enabledServices()
            ->where('services.id', $serviceId)
            ->exists();
    }

    /**
     * Get all enabled service IDs
     */
    public function getEnabledServiceIdsAttribute(): array
    {
        return $this->enabledServices()->pluck('services.id')->toArray();
    }

    // ============================================================
    // BOOTED: AUTO BATCH CODE
    // ============================================================
    protected static function booted(): void
    {
        static::creating(function ($branch) {
            if (empty($branch->branch_code)) {
                $branch->branch_code = 'BR-' . strtoupper(uniqid());
            }
        });
    }
}