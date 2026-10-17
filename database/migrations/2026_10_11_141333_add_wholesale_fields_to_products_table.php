<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Wholesale Settings
            |--------------------------------------------------------------------------
            | Existing products remain retail-only until wholesale is enabled.
            */
            $table->boolean('wholesale_enabled')
                ->default(false);

            /*
            | Minimum total units required for a wholesale purchase.
            | Admin validation must require a value of at least 1.
            */
            $table->unsignedInteger('wholesale_min_quantity')
                ->default(12);

            /*
            | false: each selected variant must meet the minimum independently.
            | true: eligible variants of this product can combine to meet it.
            | Quantities from different products do not combine.
            */
            $table->boolean('wholesale_allow_mixed_variants')
                ->default(false);

            /*
            | Base wholesale unit price for simple products.
            | Products with variants will use variant wholesale prices.
            | Additional quantity tiers will be stored separately.
            */
            $table->decimal('wholesale_price_ngn', 15, 2)
                ->nullable();

            $table->index(
                ['wholesale_enabled', 'status'],
                'products_wholesale_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_wholesale_status_index');

            $table->dropColumn([
                'wholesale_enabled',
                'wholesale_min_quantity',
                'wholesale_allow_mixed_variants',
                'wholesale_price_ngn',
            ]);
        });
    }
};
