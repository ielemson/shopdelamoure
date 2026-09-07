<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Address extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'label',

        'first_name',
        'last_name',
        'phone',

        'street_address',
        'country_id',
        'state_id',
        'city',
        'postal_code',

        'delivery_note',
        'is_default',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Orders Using This Saved Address
    |--------------------------------------------------------------------------
    */
    public function orders()
    {
        return $this->hasMany(
            Order::class,
            'address_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            ($this->first_name ?? '') . ' ' .
                ($this->last_name ?? '')
        );
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->street_address,
            $this->city,
            $this->state?->name,
            $this->country?->name,
            $this->postal_code,
        ])
            ->filter()
            ->implode(', ');
    }

    public function isDefault(): bool
    {
        return $this->is_default === true;
    }

    /*
    |--------------------------------------------------------------------------
    | Set As Default Address
    |--------------------------------------------------------------------------
    */
    public function setAsDefault(): void
    {
        DB::transaction(function () {

            /*
            | Remove default status from all
            | other addresses belonging to this user.
            */
            static::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->update([
                    'is_default' => false,
                ]);

            /*
            | Set current address as default.
            */
            $this->update([
                'is_default' => true,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDefaultAddress($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
