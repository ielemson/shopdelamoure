<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRateArea extends Model
{
    protected $fillable = [
        'shipping_rate_id',
        'name',
    ];

    public function shippingRate()
    {
        return $this->belongsTo(ShippingRate::class);
    }
}
