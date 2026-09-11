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
        | Safety Check
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasColumn('order_items', 'variant_id')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find Actual Foreign Key
        |--------------------------------------------------------------------------
        */

        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'order_items'
              AND COLUMN_NAME = 'variant_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        /*
        |--------------------------------------------------------------------------
        | Drop Foreign Key Only If It Actually Exists
        |--------------------------------------------------------------------------
        */

        foreach ($foreignKeys as $foreignKey) {

            $constraintName = $foreignKey->CONSTRAINT_NAME;

            DB::statement(
                "ALTER TABLE `order_items`
                 DROP FOREIGN KEY `{$constraintName}`"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Drop Old variant_id Column
        |--------------------------------------------------------------------------
        */

        if (Schema::hasColumn('order_items', 'variant_id')) {

            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('variant_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('order_items', 'variant_id')) {

            Schema::table('order_items', function (Blueprint $table) {

                $table->unsignedBigInteger('variant_id')
                    ->nullable();
            });
        }
    }
};
