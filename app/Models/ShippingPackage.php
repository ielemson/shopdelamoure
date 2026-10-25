<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPackage extends Model
{
    protected $fillable = ['name', 'length_cm', 'width_cm', 'height_cm', 'empty_weight_kg', 'is_active'];

    protected $casts = [
        'length_cm' => 'decimal:2', 'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2', 'empty_weight_kg' => 'decimal:3',
        'is_active' => 'boolean',
    ];
}
