<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'phone',
        'address',
        'pan_vat_number',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(SellerBranch::class);
    }

    // ============================================================
    // NEW RELATIONSHIPS (add these)
    // ============================================================

    /**
     * Branch-service pivot records owned by this seller
     */
    public function branchServices(): HasMany
    {
        return $this->hasMany(SellerBranchService::class);
    }

    /**
     * All products by this seller
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
    

    public function commissionRules(): HasMany
{
    return $this->hasMany(PartnerCommissionRule::class);
}

// Partner का branches मार्फत लागू भएका services
public function enabledBranchServices(): HasMany
{
    return $this->hasMany(SellerBranchService::class)
        ->where('is_enabled', true);
}
}