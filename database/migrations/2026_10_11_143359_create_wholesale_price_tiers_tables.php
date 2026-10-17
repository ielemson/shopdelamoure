<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Simple Product Wholesale Tiers
        |--------------------------------------------------------------------------
        */
        Schema::create('product_wholesale_price_tiers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->unsignedInteger('min_quantity');

            $table->decimal('price_ngn', 15, 2);

            $table->timestamps();

            $table->unique(
                ['product_id', 'min_quantity'],
                'product_wholesale_tier_quantity_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Variant Wholesale Tiers
        |--------------------------------------------------------------------------
        */
        Schema::create('variant_wholesale_price_tiers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->unsignedInteger('min_quantity');

            $table->decimal('price_ngn', 15, 2);

            $table->timestamps();

            $table->unique(
                ['product_variant_id', 'min_quantity'],
                'variant_wholesale_tier_quantity_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_wholesale_price_tiers');
        Schema::dropIfExists('product_wholesale_price_tiers');
    }
};
