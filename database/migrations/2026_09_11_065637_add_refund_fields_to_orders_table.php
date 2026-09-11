<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('refund_status')
                ->nullable()
                ->after('payment_status');

            $table->decimal('refund_amount', 15, 2)
                ->nullable()
                ->after('refund_status');

            $table->unsignedBigInteger('paystack_refund_id')
                ->nullable()
                ->after('refund_amount');

            $table->string('refund_reference')
                ->nullable()
                ->after('paystack_refund_id');

            $table->longText('refund_gateway_response')
                ->nullable()
                ->after('refund_reference');

            $table->timestamp('refund_requested_at')
                ->nullable()
                ->after('refund_gateway_response');

            $table->timestamp('refunded_at')
                ->nullable()
                ->after('refund_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'refund_status',
                'refund_amount',
                'paystack_refund_id',
                'refund_reference',
                'refund_gateway_response',
                'refund_requested_at',
                'refunded_at',
            ]);
        });
    }
};
