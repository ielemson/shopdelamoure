<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->foreignId('pickup_location_id')
                ->nullable()
                ->after('delivery_method')
                ->constrained('pickup_locations')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropConstrainedForeignId(
                'pickup_location_id'
            );

        });
    }
};
