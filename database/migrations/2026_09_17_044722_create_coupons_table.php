<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // Basic coupon information
            $table->string('name');
            $table->string('code')->unique();

            /*
            |--------------------------------------------------------------------------
            | Discount Type
            |--------------------------------------------------------------------------
            |
            | percentage = e.g. 10%
            | fixed      = e.g. ₦5,000
            |
            */
            $table->enum('discount_type', [
                'percentage',
                'fixed',
            ]);

            $table->decimal('discount_value', 15, 2);

            /*
            |--------------------------------------------------------------------------
            | Coupon Scope
            |--------------------------------------------------------------------------
            |
            | general = coupon applies to all eligible products
            | product = coupon applies to one specific product
            |
            */
            $table->enum('scope', [
                'general',
                'product',
            ])->default('general');

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            |
            | NULL when scope = general.
            | Required when scope = product.
            |
            */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Order Conditions
            |--------------------------------------------------------------------------
            */

            $table->decimal('minimum_order_amount', 15, 2)
                ->default(0);

            // Particularly useful for percentage discounts
            $table->decimal('maximum_discount_amount', 15, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Usage Controls
            |--------------------------------------------------------------------------
            */

            // Total number of times coupon can be used
            $table->unsignedInteger('usage_limit')
                ->nullable();

            // Number of times already used
            $table->unsignedInteger('usage_count')
                ->default(0);

            // Maximum uses by one customer
            $table->unsignedInteger('per_customer_limit')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Validity Period
            |--------------------------------------------------------------------------
            */

            $table->timestamp('starts_at')
                ->nullable();

            $table->timestamp('expires_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
