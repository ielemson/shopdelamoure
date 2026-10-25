<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Nullable so historical orders remain unclassified.
            $table->string('shipping_provider')->nullable();
            $table->decimal('shipping_base_cost', 15, 2)->nullable();
            $table->decimal('shipping_markup_percentage', 5, 2)->nullable();
            $table->decimal('shipping_markup_amount', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_provider',
                'shipping_base_cost',
                'shipping_markup_percentage',
                'shipping_markup_amount',
            ]);
        });
    }
};
