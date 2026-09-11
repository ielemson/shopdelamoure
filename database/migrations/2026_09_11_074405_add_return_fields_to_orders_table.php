<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('return_status')
                ->nullable()
                ->after('refund_status');

            $table->text('return_reason')
                ->nullable()
                ->after('return_status');

            $table->text('return_note')
                ->nullable()
                ->after('return_reason');

            $table->boolean('return_required')
                ->default(true)
                ->after('return_note');

            $table->timestamp('return_requested_at')
                ->nullable()
                ->after('return_required');

            $table->timestamp('return_approved_at')
                ->nullable()
                ->after('return_requested_at');

            $table->timestamp('returned_at')
                ->nullable()
                ->after('return_approved_at');

            $table->timestamp('return_rejected_at')
                ->nullable()
                ->after('returned_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'return_status',
                'return_reason',
                'return_note',
                'return_required',
                'return_requested_at',
                'return_approved_at',
                'returned_at',
                'return_rejected_at',
            ]);
        });
    }
};
