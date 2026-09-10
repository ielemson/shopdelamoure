<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_locations', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('address')
                ->nullable();

            $table->string('city')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            $table->unsignedMediumInteger('state_id')
                ->nullable();

            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            $table->string('phone')
                ->nullable();

            $table->string('email')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pickup Information
            |--------------------------------------------------------------------------
            */

            $table->string('opening_hours')
                ->nullable();

            $table->string('pickup_time')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_default')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_locations');
    }
};
