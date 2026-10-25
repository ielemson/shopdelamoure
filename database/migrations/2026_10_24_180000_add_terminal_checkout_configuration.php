<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->boolean('terminal_checkout_enabled')->default(false);
            $table->unsignedBigInteger('terminal_checkout_package_id')->nullable();
            $table->unsignedSmallInteger('terminal_checkout_max_units')->default(1);
            $table->decimal('terminal_checkout_extra_fee', 12, 2)->default(0);
            $table->text('terminal_checkout_services')->nullable();
            $table->boolean('terminal_checkout_confirmed')->default(false);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->text('terminal_shipping_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('terminal_shipping_snapshot'));
        Schema::table('website_settings', fn (Blueprint $table) => $table->dropColumn([
            'terminal_checkout_enabled', 'terminal_checkout_package_id', 'terminal_checkout_max_units',
            'terminal_checkout_extra_fee', 'terminal_checkout_services', 'terminal_checkout_confirmed',
        ]));
    }
};
