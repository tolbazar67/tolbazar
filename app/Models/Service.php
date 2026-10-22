<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'image',
        'sort_order',
        'is_active',
        'is_featured',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'settings'    => 'array',
        ];
    }

    // ============================================================
    // AUTO-GENERATE SLUG
    // ============================================================
    protected static function booted(): void
    {
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name) . '-' . rand(1000, 9999);
            }
        });
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * All branches that have this service (via pivot)
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(
            SellerBranch::class,
            'seller_branch_services',
            'service_id',
            'branch_id'
        )->withPivot(['seller_id', 'is_enabled', 'meta'])
         ->withTimestamps();
    }

    /**
     * Only enabled branches
     */
    public function enabledBranches(): BelongsToMany
    {
        return $this->branches()->wherePivot('is_enabled', 1);
    }

    /**
     * Branch-service pivot records
     */
    public function branchServices(): HasMany
    {
        return $this->hasMany(SellerBranchService::class);
    }

    /**
     * All products under this service
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Only active products
     */
    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', 1);
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ============================================================
    // HELPERS
    // ============================================================

    /**
     * Check if a specific branch has this service enabled
     */
    public function isEnabledAt(int $branchId): bool
    {
        return $this->branches()
            ->wherePivot('is_enabled', 1)
            ->where('seller_branches.id', $branchId)
            ->exists();
    }

    /**
     * Count products in this service
     */
    public function getProductCountAttribute(): int
    {
        return $this->activeProducts()->count();
    }

    // ============================================================
// DYNAMIC CATEGORIES
// ============================================================

public function categories(): HasMany
{
    return $this->hasMany(ServiceCategory::class)
        ->whereNull('parent_id')
        ->orderBy('sort_order');
}

public function allCategories(): HasMany
{
    return $this->hasMany(ServiceCategory::class);
}

// ============================================================
// TAX / VAT RULES
// ============================================================

public function taxRules(): HasMany
{
    return $this->hasMany(ServiceTaxRule::class);
}

public function activeTaxRules(): HasMany
{
    return $this->taxRules()->active()->orderBy('priority');
}

// ============================================================
// PARTNER COMMISSION RULES
// ============================================================

public function commissionRules(): HasMany
{
    return $this->hasMany(PartnerCommissionRule::class);
}

public function activeCommissionRules(): HasMany
{
    return $this->commissionRules()->active();
}

// ============================================================
// CUSTOMER CHECKOUT CHARGES
// ============================================================

public function customerChargeRules(): HasMany
{
    return $this->hasMany(CustomerChargeRule::class);
}

public function activeCustomerChargeRules(): HasMany
{
    return $this->customerChargeRules()->active();
}
}