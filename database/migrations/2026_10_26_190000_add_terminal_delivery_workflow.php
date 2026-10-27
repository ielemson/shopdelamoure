<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $columns = [
            'terminal_checkout_enabled' => fn($t) => $t->boolean('terminal_checkout_enabled')->default(false),
            'terminal_checkout_confirmed' => fn($t) => $t->boolean('terminal_checkout_confirmed')->default(false),
            'terminal_checkout_services' => fn($t) => $t->text('terminal_checkout_services')->nullable(),
            'terminal_checkout_service_labels' => fn($t) => $t->text('terminal_checkout_service_labels')->nullable(),
            'terminal_checkout_package_id' => fn($t) => $t->unsignedBigInteger('terminal_checkout_package_id')->nullable(),
            'terminal_checkout_max_units' => fn($t) => $t->unsignedInteger('terminal_checkout_max_units')->default(1),
            'terminal_checkout_extra_fee' => fn($t) => $t->decimal('terminal_checkout_extra_fee', 12, 2)->default(0),
            'terminal_auto_book' => fn($t) => $t->boolean('terminal_auto_book')->default(false),
        ];
        foreach ($columns as $name => $add) {
            if (!Schema::hasColumn('website_settings', $name)) Schema::table('website_settings', $add);
        }
        if (!Schema::hasColumn('orders', 'terminal_shipping_snapshot')) {
            Schema::table('orders', fn(Blueprint $t) => $t->longText('terminal_shipping_snapshot')->nullable());
        }
        if (!Schema::hasTable('terminal_deliveries')) Schema::create('terminal_deliveries', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('order_id')->unique();
            $t->string('state')->default('ready')->index();
            $t->string('shipment_id')->nullable()->unique();
            $t->string('provider_status')->nullable();
            $t->string('tracking_number')->nullable();
            $t->text('tracking_url')->nullable();
            $t->text('shipping_label')->nullable();
            $t->text('last_error')->nullable();
            $t->timestamp('attempted_at')->nullable();
            $t->timestamp('synced_at')->nullable();
            $t->timestamps();
        });
    }
    // Additive migration: do not destroy operational records on rollback.
    public function down(): void {}
};
