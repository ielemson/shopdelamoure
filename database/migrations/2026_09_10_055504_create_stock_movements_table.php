<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('order_item_id')
                ->nullable()
                ->constrained('order_items')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Movement Type
            |--------------------------------------------------------------------------
            */

            $table->string('type', 50);

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            |
            | Signed quantity:
            |
            | sale       = -2
            | restock    = +10
            | adjustment = +/- quantity
            |
            */

            $table->integer('quantity');

            $table->integer('quantity_before');

            $table->integer('quantity_after');

            /*
            |--------------------------------------------------------------------------
            | Idempotency Reference
            |--------------------------------------------------------------------------
            |
            | Prevents one paid order from deducting stock twice.
            |
            */

            $table->string('reference')
                ->nullable()
                ->unique();

            $table->text('note')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'product_id',
                'variant_id',
            ]);

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
