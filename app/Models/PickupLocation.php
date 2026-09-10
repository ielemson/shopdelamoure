<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state_id',
        'phone',
        'email',
        'opening_hours',
        'pickup_time',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function state()
    {
        return $this->belongsTo(
            State::class
        );
    }

    public function orders()
    {
        return $this->hasMany(
            Order::class
        );
    }
}
