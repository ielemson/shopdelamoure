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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Saved Address Reference
            |--------------------------------------------------------------------------
            */
            $table->foreignId('address_id')
                ->nullable()
                ->constrained('addresses')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Order Identification
            |--------------------------------------------------------------------------
            */
            $table->string('order_no')->unique();

            /*
            |--------------------------------------------------------------------------
            | Customer Snapshot
            |--------------------------------------------------------------------------
            */
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Delivery Address Snapshot
            |--------------------------------------------------------------------------
            */
            $table->unsignedMediumInteger('country_id')->nullable();
            $table->unsignedMediumInteger('state_id')->nullable();

            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->text('order_note')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Delivery
            |--------------------------------------------------------------------------
            */
            $table->string('delivery_method')
                ->default('store_pickup');

            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            */
            $table->char('currency', 3)
                ->default('NGN');

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */
            $table->string('payment_method')->nullable();

            $table->string('payment_reference')
                ->nullable()
                ->index();

            $table->string('payment_status')
                ->default('unpaid')
                ->index();

            $table->longText('payment_gateway_response')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Order Amounts
            |--------------------------------------------------------------------------
            */
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Order Status
            |--------------------------------------------------------------------------
            */
            $table->string('status')
                ->default('pending')
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->nullOnDelete();

            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
