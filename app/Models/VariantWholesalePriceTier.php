<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantWholesalePriceTier extends Model
{
    protected $fillable = [
        'product_variant_id',
        'min_quantity',
        'price_ngn',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'price_ngn' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}
