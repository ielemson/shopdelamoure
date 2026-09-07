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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Order
            |--------------------------------------------------------------------------
            */
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Product Variant
            |--------------------------------------------------------------------------
            */
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot
            |--------------------------------------------------------------------------
            */
            $table->string('name');

            $table->string('sku')
                ->nullable();

            $table->string('variant_name')
                ->nullable();

            $table->json('variant_options')
                ->nullable();

            $table->string('image')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            */
            $table->char('currency', 3)
                ->default('NGN');

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            $table->decimal('price', 15, 2)
                ->default(0);

            $table->unsignedInteger('quantity')
                ->default(1);

            $table->decimal('total', 15, 2)
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
            $table->index(['order_id', 'product_id']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
