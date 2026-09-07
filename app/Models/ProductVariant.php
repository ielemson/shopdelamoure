<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'options',
        'image',

        'price_ngn',
        'sale_price_ngn',
        'price_usd',
        'sale_price_usd',

        'track_stock',
        'stock_quantity',
        'stock_status',
        'low_stock_threshold',

        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',

        'price_ngn' => 'decimal:2',
        'sale_price_ngn' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'sale_price_usd' => 'decimal:2',

        'track_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',

        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing Helpers
    |--------------------------------------------------------------------------
    */

    public function getEffectivePriceNgnAttribute()
    {
        return $this->price_ngn ?? $this->product?->price_ngn;
    }

    public function getEffectivePriceUsdAttribute()
    {
        return $this->price_usd ?? $this->product?->price_usd;
    }

    public function getEffectiveSalePriceNgnAttribute()
    {
        return $this->sale_price_ngn ?? $this->product?->sale_price_ngn;
    }

    public function getEffectiveSalePriceUsdAttribute()
    {
        return $this->sale_price_usd ?? $this->product?->sale_price_usd;
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Helpers
    |--------------------------------------------------------------------------
    */

    public function isInStock(): bool
    {
        if (! $this->track_stock) {
            return $this->stock_status !== 'out_of_stock';
        }

        return $this->stock_quantity > 0
            && $this->stock_status === 'in_stock';
    }

    public function isLowStock(): bool
    {
        if (! $this->track_stock) {
            return false;
        }

        return $this->stock_quantity > 0
            && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        if (! $this->track_stock) {
            return $this->stock_status === 'out_of_stock';
        }

        return $this->stock_quantity <= 0
            || $this->stock_status === 'out_of_stock';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefaultVariant($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')
            ->orderBy('id');
    }
}
