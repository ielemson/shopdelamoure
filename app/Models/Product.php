<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'category_id',
        'subcategory_id',

        'name',
        'slug',
        'brand',
        'sku',
        'barcode',

        'main_image',
        'short_description',
        'description',

        'has_variants',

        'price_ngn',
        'sale_price_ngn',
        'price_usd',
        'sale_price_usd',
        'cost_price_ngn',

        'sale_starts_at',
        'sale_ends_at',

        'track_stock',
        'quantity',
        'low_stock_alert',
        'stock_status',

        'weight',
        'material',
        'model',

        'is_featured',
        'is_new_arrival',
        'is_best_seller',
        'is_trending',

        'meta_title',
        'meta_description',
        'meta_keywords',

        'status',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'has_variants' => 'boolean',
        'track_stock' => 'boolean',

        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_trending' => 'boolean',
        'status' => 'boolean',

        'price_ngn' => 'decimal:2',
        'sale_price_ngn' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'sale_price_usd' => 'decimal:2',
        'cost_price_ngn' => 'decimal:2',

        'quantity' => 'integer',
        'low_stock_alert' => 'integer',
        'sort_order' => 'integer',

        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Category Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Product Images
    |--------------------------------------------------------------------------
    */

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Product Variants
    |--------------------------------------------------------------------------
    */

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)
            ->where('is_default', true)
            ->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Order Items
    |--------------------------------------------------------------------------
    */

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing Helpers
    |--------------------------------------------------------------------------
    */

    public function getRegularPrice(string $currency = 'NGN'): ?float
    {
        $currency = strtoupper($currency);

        return match ($currency) {
            'USD' => $this->price_usd !== null
                ? (float) $this->price_usd
                : null,

            default => $this->price_ngn !== null
                ? (float) $this->price_ngn
                : null,
        };
    }

    public function getSalePrice(string $currency = 'NGN'): ?float
    {
        $currency = strtoupper($currency);

        $salePrice = match ($currency) {
            'USD' => $this->sale_price_usd,
            default => $this->sale_price_ngn,
        };

        if ($salePrice === null) {
            return null;
        }

        if (! $this->saleIsCurrentlyActive()) {
            return null;
        }

        return (float) $salePrice;
    }

    public function getCurrentPrice(string $currency = 'NGN'): ?float
    {
        return $this->getSalePrice($currency)
            ?? $this->getRegularPrice($currency);
    }

    public function isOnSale(string $currency = 'NGN'): bool
    {
        return $this->getSalePrice($currency) !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Sale Period Helper
    |--------------------------------------------------------------------------
    */

    public function saleIsCurrentlyActive(): bool
    {
        $now = now();

        if (
            $this->sale_starts_at &&
            $now->lt($this->sale_starts_at)
        ) {
            return false;
        }

        if (
            $this->sale_ends_at &&
            $now->gt($this->sale_ends_at)
        ) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Helpers
    |--------------------------------------------------------------------------
    */

    public function isInStock(): bool
    {
        /*
        |----------------------------------------------------------------------
        | Variant Product
        |----------------------------------------------------------------------
        */
        if ($this->has_variants) {
            return $this->activeVariants()
                ->where(function ($query) {
                    $query->where('track_stock', false)
                        ->where('stock_status', '!=', 'out_of_stock')
                        ->orWhere(function ($query) {
                            $query->where('track_stock', true)
                                ->where('stock_quantity', '>', 0)
                                ->where('stock_status', 'in_stock');
                        });
                })
                ->exists();
        }

        /*
        |----------------------------------------------------------------------
        | Simple Product
        |----------------------------------------------------------------------
        */
        if (! $this->track_stock) {
            return $this->stock_status !== 'out_of_stock';
        }

        return $this->quantity > 0
            && $this->stock_status === 'in_stock';
    }

    public function isOutOfStock(): bool
    {
        return ! $this->isInStock();
    }

    public function isLowStock(): bool
    {
        /*
        | Variant stock should be checked at individual variant level.
        */
        if ($this->has_variants) {
            return $this->activeVariants()
                ->where('track_stock', true)
                ->where('stock_quantity', '>', 0)
                ->whereColumn(
                    'stock_quantity',
                    '<=',
                    'low_stock_threshold'
                )
                ->exists();
        }

        if (! $this->track_stock) {
            return false;
        }

        return $this->quantity > 0
            && $this->quantity <= $this->low_stock_alert;
    }

    /*
    |--------------------------------------------------------------------------
    | Display Image Helper
    |--------------------------------------------------------------------------
    */

    public function getDisplayImageAttribute(): ?string
    {
        /*
        | Prefer the product's explicitly stored main image.
        */
        if ($this->main_image) {
            return $this->main_image;
        }

        /*
        | Fall back to the primary gallery image.
        */
        return $this->primaryImage?->image;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeBestSellers($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeSimpleProducts($query)
    {
        return $query->where('has_variants', false);
    }

    public function scopeVariantProducts($query)
    {
        return $query->where('has_variants', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')
            ->orderByDesc('created_at');
    }
}
