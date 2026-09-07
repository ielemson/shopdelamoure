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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Address Identification
            |--------------------------------------------------------------------------
            | Allows customers to distinguish saved addresses such as:
            | Home, Office, School, etc.
            */
            $table->string('label', 50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Recipient Information
            |--------------------------------------------------------------------------
            */
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 30);

            /*
            |--------------------------------------------------------------------------
            | Delivery Address
            |--------------------------------------------------------------------------
            */
            $table->string('street_address');

            $table->unsignedMediumInteger('country_id');
            $table->unsignedMediumInteger('state_id');

            $table->string('city');

            $table->string('postal_code', 20)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Delivery Instructions
            |--------------------------------------------------------------------------
            */
            $table->text('delivery_note')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Default Address
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_default')->default(false);

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
                ->on('countries');

            $table->foreign('state_id')
                ->references('id')
                ->on('states');

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
