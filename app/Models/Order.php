<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'address_id',

        'order_no',

        'first_name',
        'last_name',
        'email',
        'phone',

        'country_id',
        'state_id',
        'city',
        'address',
        'order_note',

        'delivery_method',

        'currency',

        'payment_method',
        'payment_reference',
        'payment_status',
        'payment_gateway_response',
        'paid_at',

        'subtotal',
        'shipping',
        'vat',
        'discount',
        'total',

        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'vat' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',

        'paid_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

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
    | Saved Address
    |--------------------------------------------------------------------------
    | Do NOT call this relationship "address()" because the orders table
    | already contains an "address" snapshot column.
    */
    public function savedAddress()
    {
        return $this->belongsTo(
            Address::class,
            'address_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Helpers
    |--------------------------------------------------------------------------
    */

    public function getCustomerNameAttribute(): string
    {
        return trim(
            ($this->first_name ?? '').' '.
                ($this->last_name ?? '')
        );
    }

    /**
     * Determine whether this order was placed by a guest.
     */
    public function isGuest(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Determine whether this order belongs to a registered customer.
     */
    public function isRegisteredCustomer(): bool
    {
        return ! is_null($this->user_id);
    }

    /**
     * Customer type label.
     */
    public function getCustomerTypeAttribute(): string
    {
        return $this->isGuest()
            ? 'Guest'
            : 'Registered Customer';
    }

    /*
    |--------------------------------------------------------------------------
    | Currency Helpers
    |--------------------------------------------------------------------------
    */

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper($this->currency ?? 'NGN')) {
            'NGN' => '₦',
            'USD' => '$',
            default => strtoupper($this->currency ?? 'NGN').' ',
        };
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return $this->formatMoney($this->subtotal);
    }

    public function getFormattedShippingAttribute(): string
    {
        return $this->formatMoney($this->shipping);
    }

    public function getFormattedVatAttribute(): string
    {
        return $this->formatMoney($this->vat);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return $this->formatMoney($this->discount);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->formatMoney($this->total);
    }

    protected function formatMoney($amount): string
    {
        return $this->currency_symbol
            .number_format((float) $amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Helpers
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /*
    |--------------------------------------------------------------------------
    | Order Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Guest orders only.
     */
    public function scopeGuests($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Registered customer orders only.
     */
    public function scopeRegisteredCustomers($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
