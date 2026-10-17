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
        'pickup_location_id',
        'status',

        'coupon_id',
        'coupon_code',
        'coupon_discount_type',
        'coupon_discount_value',
        'coupon_discount_amount',
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

    // public function items()
    // {
    //     return $this->hasMany(OrderItem::class);
    // }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(
            \App\Models\OrderItem::class,
            'order_id'
        );
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

    public function pickupLocation()
    {
        return $this->belongsTo(
            PickupLocation::class,
            'pickup_location_id'
        );
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
| Purchase Type Helpers
|--------------------------------------------------------------------------
|
| Read the purchase type saved at checkout, rather than current product
| settings. Missing or invalid markers remain unclassified.
|
*/

    public function getPurchaseTypeAttribute(): string
    {
        $types = $this->items
            ->map(function ($item) {
                $options = $item->variant_options;

                // Support JSON strings until OrderItem casting is confirmed.
                if (is_string($options)) {
                    $options = json_decode($options, true);
                }

                $type = data_get($options, '_purchase_type');

                return in_array($type, ['retail', 'wholesale'], true)
                    ? $type
                    : 'unknown';
            })
            ->unique()
            ->values();

        if ($types->isEmpty()) {
            return 'unknown';
        }

        if ($types->count() === 1) {
            return $types->first();
        }

        // Some items have no reliable purchase-type snapshot.
        if ($types->contains('unknown')) {
            return 'unknown';
        }

        // Handle historical orders containing both purchase types.
        return 'mixed';
    }

    public function getPurchaseTypeLabelAttribute(): string
    {
        return match ($this->purchase_type) {
            'wholesale' => 'Wholesale',
            'retail' => 'Retail',
            'mixed' => 'Mixed',
            default => 'Unclassified',
        };
    }

    public function getPurchaseTypeBadgeClassAttribute(): string
    {
        return match ($this->purchase_type) {
            'wholesale' => 'bg-primary',
            'retail' => 'bg-success',
            'mixed' => 'bg-warning text-dark',
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
