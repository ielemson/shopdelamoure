<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('sku')
                ->nullable()
                ->unique();

            $table->json('options')
                ->nullable();

            $table->string('image')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            $table->decimal('price_ngn', 15, 2)
                ->nullable();

            $table->decimal('sale_price_ngn', 15, 2)
                ->nullable();

            $table->decimal('price_usd', 15, 2)
                ->nullable();

            $table->decimal('sale_price_usd', 15, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */
            $table->boolean('track_stock')
                ->default(true);

            $table->unsignedInteger('stock_quantity')
                ->default(0);

            $table->string('stock_status', 30)
                ->default('out_of_stock');

            $table->unsignedInteger('low_stock_threshold')
                ->default(5);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_default')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'stock_status']);
            $table->index(['product_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
