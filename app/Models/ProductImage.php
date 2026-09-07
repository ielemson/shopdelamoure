<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductImage extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'product_id',
        'image',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Primary Image Helper
    |--------------------------------------------------------------------------
    */

    public function setAsPrimary(): void
    {
        DB::transaction(function () {
            /*
            | Remove primary status from other images
            | belonging to this product.
            */
            static::where('product_id', $this->product_id)
                ->where('id', '!=', $this->id)
                ->update([
                    'is_primary' => false,
                ]);

            /*
            | Make this image primary.
            */
            $this->update([
                'is_primary' => true,
            ]);
        });
    }

    public function isPrimary(): bool
    {
        return $this->is_primary === true;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
