<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Wholesale Settings
            |--------------------------------------------------------------------------
            | A variant qualifies only when wholesale is also enabled
            | on its parent product and the variant is active.
            */
            $table->boolean('wholesale_enabled')
                ->default(false);

            /*
            | Wholesale unit price for this specific variant.
            | A valid price is required before accepting wholesale orders.
            */
            $table->decimal('wholesale_price_ngn', 15, 2)
                ->nullable();

            $table->index(
                ['product_id', 'wholesale_enabled', 'is_active'],
                'variants_wholesale_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('variants_wholesale_active_index');

            $table->dropColumn([
                'wholesale_enabled',
                'wholesale_price_ngn',
            ]);
        });
    }
};
