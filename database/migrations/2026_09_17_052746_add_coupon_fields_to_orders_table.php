<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->foreignId('coupon_id')
                ->nullable()
                ->after('discount')
                ->constrained('coupons')
                ->nullOnDelete();

            $table->string('coupon_code')
                ->nullable()
                ->after('coupon_id');

            $table->string('coupon_discount_type')
                ->nullable()
                ->after('coupon_code');

            $table->decimal('coupon_discount_value', 15, 2)
                ->nullable()
                ->after('coupon_discount_type');

            $table->decimal('coupon_discount_amount', 15, 2)
                ->default(0)
                ->after('coupon_discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropForeign(['coupon_id']);

            $table->dropColumn([
                'coupon_id',
                'coupon_code',
                'coupon_discount_type',
                'coupon_discount_value',
                'coupon_discount_amount',
            ]);
        });
    }
};
