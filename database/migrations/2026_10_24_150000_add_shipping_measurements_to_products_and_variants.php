<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'product_variants'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('shipping_weight_kg', 10, 3)->nullable();
                $table->decimal('shipping_length_cm', 10, 2)->nullable();
                $table->decimal('shipping_width_cm', 10, 2)->nullable();
                $table->decimal('shipping_height_cm', 10, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['products', 'product_variants'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn([
                    'shipping_weight_kg', 'shipping_length_cm',
                    'shipping_width_cm', 'shipping_height_cm',
                ]);
            });
        }
    }
};
