<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {

            // Remove old rule that allowed only one rate per state
            $table->dropUnique(
                'shipping_rates_country_id_state_id_unique'
            );

            // Each rate will now represent a delivery zone
            $table->string('zone_name')
                ->nullable()
                ->after('state_id');

            // Allow several zones inside one state
            $table->unique([
                'country_id',
                'state_id',
                'zone_name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {

            $table->dropUnique([
                'country_id',
                'state_id',
                'zone_name'
            ]);

            $table->dropColumn('zone_name');

            $table->unique([
                'country_id',
                'state_id'
            ]);
        });
    }
};
