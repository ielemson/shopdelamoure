<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');

            $table->decimal('shipping_cost', 12, 2);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['country_id', 'state_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
