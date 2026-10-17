<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWholesalePriceTier extends Model
{
    protected $fillable = [
        'product_id',
        'min_quantity',
        'price_ngn',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'price_ngn' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
