<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('subcategory_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            /*
        |--------------------------------------------------------------------------
        | Product Identification
        |--------------------------------------------------------------------------
        */
            $table->string('name');

            $table->string('slug')
                ->unique();

            $table->string('brand')
                ->nullable();

            $table->string('sku')
                ->nullable()
                ->unique();

            $table->string('barcode')
                ->nullable()
                ->unique();

            /*
        |--------------------------------------------------------------------------
        | Product Content
        |--------------------------------------------------------------------------
        */
            $table->string('main_image')
                ->nullable();

            $table->text('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Product Variants
        |--------------------------------------------------------------------------
        | false = simple product
        | true  = product has variants such as 50ml, 100ml, colour, scent, etc.
        */
            $table->boolean('has_variants')
                ->default(false);

            /*
        |--------------------------------------------------------------------------
        | Pricing - NGN
        |--------------------------------------------------------------------------
        */
            $table->decimal('price_ngn', 15, 2)
                ->nullable();

            $table->decimal('sale_price_ngn', 15, 2)
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Pricing - USD
        |--------------------------------------------------------------------------
        */
            $table->decimal('price_usd', 15, 2)
                ->nullable();

            $table->decimal('sale_price_usd', 15, 2)
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Cost Price
        |--------------------------------------------------------------------------
        | Internal cost price. NGN will remain our primary/base accounting
        | currency for now.
        */
            $table->decimal('cost_price_ngn', 15, 2)
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Sale Period
        |--------------------------------------------------------------------------
        */
            $table->timestamp('sale_starts_at')
                ->nullable();

            $table->timestamp('sale_ends_at')
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        | For products with variants, stock is primarily controlled from
        | product_variants. For simple products, these fields are used directly.
        */
            $table->boolean('track_stock')
                ->default(true);

            $table->unsignedInteger('quantity')
                ->default(0);

            $table->unsignedInteger('low_stock_alert')
                ->default(5);

            $table->string('stock_status', 30)
                ->default('in_stock');

            /*
        |--------------------------------------------------------------------------
        | General Product Attributes
        |--------------------------------------------------------------------------
        | These describe the product generally.
        | Variant-specific values such as size, colour or scent belong in
        | product_variants.options.
        */
            $table->string('weight')
                ->nullable();

            $table->string('material')
                ->nullable();

            $table->string('model')
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Merchandising
        |--------------------------------------------------------------------------
        */
            $table->boolean('is_featured')
                ->default(false);

            $table->boolean('is_new_arrival')
                ->default(false);

            $table->boolean('is_best_seller')
                ->default(false);

            $table->boolean('is_trending')
                ->default(false);

            /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */
            $table->string('meta_title')
                ->nullable();

            $table->text('meta_description')
                ->nullable();

            $table->text('meta_keywords')
                ->nullable();

            /*
        |--------------------------------------------------------------------------
        | Publishing / Display
        |--------------------------------------------------------------------------
        */
            $table->boolean('status')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
        |--------------------------------------------------------------------------
        | Timestamps
        |--------------------------------------------------------------------------
        */
            $table->timestamps();

            /*
        |--------------------------------------------------------------------------
        | Indexes
        |--------------------------------------------------------------------------
        */
            $table->index(['category_id', 'status']);
            $table->index(['subcategory_id', 'status']);
            $table->index('stock_status');

            $table->index([
                'is_featured',
                'is_new_arrival',
                'is_best_seller'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
