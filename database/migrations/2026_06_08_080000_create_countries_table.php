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
        Schema::create('countries', function (Blueprint $table) {
            $table->mediumIncrements('id');

            /*
            |--------------------------------------------------------------------------
            | Country Information
            |--------------------------------------------------------------------------
            */
            $table->string('name')->index();

            $table->char('iso2', 2)
                ->unique();

            $table->char('iso3', 3)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Telephone
            |--------------------------------------------------------------------------
            */
            $table->string('phone_code', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            | Native/default currency of the country.
            |
            | Examples:
            | Nigeria       -> NGN
            | United States -> USD
            | Ghana         -> GHS
            */
            $table->char('currency_code', 3)
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
