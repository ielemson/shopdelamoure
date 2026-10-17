<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',

        // Product snapshot
        'name',
        'sku',
        'variant_name',
        'variant_options',
        'image',

        // Pricing snapshot
        'currency',
        'price',
        'quantity',
        'total',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'variant_options' => 'array',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Type Helpers
    |--------------------------------------------------------------------------
    */

    public function getPurchaseTypeAttribute(): string
    {
        $type = data_get(
            $this->variant_options,
            '_purchase_type'
        );

        return in_array($type, ['retail', 'wholesale'], true)
            ? $type
            : 'unknown';
    }

    public function getPurchaseTypeLabelAttribute(): string
    {
        return match ($this->purchase_type) {
            'wholesale' => 'Wholesale',
            'retail' => 'Retail',
            default => 'Unclassified',
        };
    }

    public function getPurchaseTypeBadgeClassAttribute(): string
    {
        return match ($this->purchase_type) {
            'wholesale' => 'bg-primary',
            'retail' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function isWholesale(): bool
    {
        return $this->purchase_type === 'wholesale';
    }

    public function isRetail(): bool
    {
        return $this->purchase_type === 'retail';
    }

    /*
    |--------------------------------------------------------------------------
    | Variant Helpers
    |--------------------------------------------------------------------------
    */

    public function hasVariant(): bool
    {
        return ! is_null($this->product_variant_id);
    }

    public function getVariantDisplayAttribute(): ?string
    {
        if ($this->variant_name) {
            return $this->variant_name;
        }

        $display = collect($this->variant_options ?? [])
            ->reject(function ($value, $key) {
                // Underscore-prefixed keys contain internal metadata.
                return str_starts_with((string) $key, '_')
                    || ! is_scalar($value)
                    || is_bool($value)
                    || trim((string) $value) === '';
            })
            ->map(function ($value, $key) {
                $label = ucfirst(
                    str_replace('_', ' ', (string) $key)
                );

                return $label.': '.$value;
            })
            ->implode(', ');

        return $display !== '' ? $display : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Currency Helpers
    |--------------------------------------------------------------------------
    */

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper($this->currency ?? 'NGN')) {
            'USD' => '$',
            'NGN' => '₦',
            default => strtoupper($this->currency ?? 'NGN').' ',
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency_symbol
            .number_format((float) $this->price, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->currency_symbol
            .number_format((float) $this->total, 2);
    }
}
