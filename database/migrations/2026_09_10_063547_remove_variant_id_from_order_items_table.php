<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Preserve Existing Variant Data
        |--------------------------------------------------------------------------
        |
        | Some recently created orders may have variant_id populated while
        | product_variant_id is still null.
        |
        */

        DB::table('order_items')
            ->whereNull('product_variant_id')
            ->whereNotNull('variant_id')
            ->update([
                'product_variant_id' => DB::raw('variant_id'),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Remove Duplicate Variant Foreign Key
        |--------------------------------------------------------------------------
        */

        Schema::table('order_items', function (Blueprint $table) {

            $table->dropConstrainedForeignId('variant_id');

        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            $table->foreignId('variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();

        });

        /*
        |--------------------------------------------------------------------------
        | Restore Values If Migration Is Rolled Back
        |--------------------------------------------------------------------------
        */

        DB::table('order_items')
            ->whereNull('variant_id')
            ->whereNotNull('product_variant_id')
            ->update([
                'variant_id' => DB::raw('product_variant_id'),
            ]);
    }
};
