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

        /*
        |--------------------------------------------------------------------------
        | Product Snapshot
        |--------------------------------------------------------------------------
        */

        'name',
        'sku',
        'variant_name',
        'variant_options',
        'image',

        /*
        |--------------------------------------------------------------------------
        | Pricing Snapshot
        |--------------------------------------------------------------------------
        */

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
    | Variant Helpers
    |--------------------------------------------------------------------------
    */

    public function hasVariant(): bool
    {
        return ! is_null(
            $this->product_variant_id
        );
    }

    public function getVariantDisplayAttribute(): ?string
    {
        if ($this->variant_name) {
            return $this->variant_name;
        }

        if (empty($this->variant_options)) {
            return null;
        }

        return collect(
            $this->variant_options
        )
            ->map(function ($value, $key) {

                return ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $key
                    )
                )
                .': '
                .$value;

            })
            ->implode(', ');
    }

    /*
    |--------------------------------------------------------------------------
    | Currency Helpers
    |--------------------------------------------------------------------------
    */

    public function getCurrencySymbolAttribute(): string
    {
        return match (
            strtoupper(
                $this->currency ?? 'NGN'
            )
        ) {
            'USD' => '$',
            'NGN' => '₦',

            default => strtoupper(
                $this->currency ?? 'NGN'
            )
                .' ',
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency_symbol
            .number_format(
                (float) $this->price,
                2
            );
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->currency_symbol
            .number_format(
                (float) $this->total,
                2
            );
    }
}
