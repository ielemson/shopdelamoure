<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('delivery_provider')
                ->default('local');

            $table->decimal('shipping_markup_percentage', 5, 2)
                ->default(10);
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_provider',
                'shipping_markup_percentage',
            ]);
        });
    }
};
