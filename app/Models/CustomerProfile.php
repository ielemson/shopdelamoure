<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'alternate_phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'customer_type',
        'status',
        'delivery_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}