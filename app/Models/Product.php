<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'branch_id',
        'service_id',
        'category_id',

        // 👇 NEW: created-by tracking
        'created_by',
        'created_by_role',
        'created_by_name',

        // Info
        'name',
        'slug',
        'sku',
        'short_description',
        'description',

        // Pricing
        'price',
        'compare_price',
        'cost_price',
        'unit',
        'tax_percent',

        // Stock
        'stock_quantity',
        'min_order_qty',
        'max_order_qty',
        'track_stock',
        'in_stock',
        'has_variants',

        // Media
        'thumbnail',

        // Status
        'is_active',
        'is_featured',
        'sort_order',
        'published_at',

        // SEO & extra
        'meta_title',
        'meta_description',
        'attributes',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'decimal:2',
            'compare_price'  => 'decimal:2',
            'cost_price'     => 'decimal:2',
            'tax_percent'    => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_order_qty'  => 'integer',
            'max_order_qty'  => 'integer',
            'track_stock'    => 'boolean',
            'in_stock'       => 'boolean',
            'has_variants'   => 'boolean',
            'is_active'      => 'boolean',
            'is_featured'    => 'boolean',
            'attributes'     => 'array',
            'published_at'   => 'datetime',
        ];
    }

    // ============================================================
    // AUTO-GENERATE SLUG & SKU
    // ============================================================
    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . rand(1000, 99999);
            }

            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }

            if (empty($product->published_at) && $product->is_active) {
                $product->published_at = now();
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('is_active') && $product->is_active && !$product->published_at) {
                $product->published_at = now();
            }
        });
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 👇 NEW: creator relationship
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', 1);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', 1);
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

    public function scopeInStock($query)
    {
        return $query->where('in_stock', 1);
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('short_description', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }

    // ============================================================
    // HELPERS
    // ============================================================

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->compare_price || $this->compare_price <= $this->price) {
            return 0;
        }
        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->track_stock && $this->stock_quantity <= 0) return false;
        return $this->in_stock;
    }

    public function decreaseStock(int $qty = 1): bool
    {
        if (!$this->track_stock) return true;
        if ($this->stock_quantity < $qty) return false;

        $this->decrement('stock_quantity', $qty);

        if ($this->stock_quantity <= 0) {
            $this->update(['in_stock' => 0]);
        }

        return true;
    }

    public function increaseStock(int $qty = 1): void
    {
        if (!$this->track_stock) return;
        $this->increment('stock_quantity', $qty);
        $this->update(['in_stock' => 1]);
    }

    public function getFinalPriceAttribute(): float
    {
        if ($this->has_variants) {
            $minVariantPrice = $this->variants()->min('price');
            if ($minVariantPrice) return (float) $minVariantPrice;
        }
        return (float) $this->price;
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        $primary = $this->images()->where('is_primary', 1)->first();
        if ($primary) {
            return asset('storage/' . $primary->image_path);
        }

        $first = $this->images()->first();
        if ($first) {
            return asset('storage/' . $first->image_path);
        }

        return null;
    }

    // 👇 NEW: helper to get "Created by" label
    public function getCreatedByLabelAttribute(): string
    {
        $name = $this->created_by_name
            ?? $this->creator?->name
            ?? 'Unknown';

        $role = match ($this->created_by_role) {
            'owner'  => 'Owner',
            'staff'  => 'Staff',
            'admin'  => 'Admin',
            default  => ucfirst((string) $this->created_by_role),
        };

        return "{$name} ({$role})";
    }

    // ============================================================
// SERVICE-SPECIFIC DETAILS
// ============================================================

public function hotelRoomDetail(): HasOne
{
    return $this->hasOne(HotelRoomDetail::class);
}

public function retailProductDetail(): HasOne
{
    return $this->hasOne(RetailProductDetail::class);
}

public function restaurantItemDetail(): HasOne
{
    return $this->hasOne(RestaurantItemDetail::class);
}

public function educationCourseDetail(): HasOne
{
    return $this->hasOne(EducationCourseDetail::class);
}

public function healthcareServiceDetail(): HasOne
{
    return $this->hasOne(HealthcareServiceDetail::class);
}

public function transportServiceDetail(): HasOne
{
    return $this->hasOne(TransportServiceDetail::class);
}

public function beautyServiceDetail(): HasOne
{
    return $this->hasOne(BeautyServiceDetail::class);
}

public function repairServiceDetail(): HasOne
{
    return $this->hasOne(RepairServiceDetail::class);
}

public function constructionServiceDetail(): HasOne
{
    return $this->hasOne(ConstructionServiceDetail::class);
}

public function travelPackageDetail(): HasOne
{
    return $this->hasOne(TravelPackageDetail::class);
}

}