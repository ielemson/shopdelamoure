<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('terminal_environment')->default('test');
            $table->text('terminal_credentials')->nullable();
            $table->string('dispatch_name', 150)->nullable();
            $table->string('dispatch_email')->nullable();
            $table->string('dispatch_phone', 30)->nullable();
            $table->string('dispatch_line1')->nullable();
            $table->string('dispatch_city', 100)->nullable();
            $table->string('dispatch_state', 100)->nullable();
            $table->string('dispatch_country', 2)->default('NG');
            $table->string('dispatch_postal_code', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'terminal_environment', 'terminal_credentials', 'dispatch_name',
                'dispatch_email', 'dispatch_phone', 'dispatch_line1', 'dispatch_city',
                'dispatch_state', 'dispatch_country', 'dispatch_postal_code',
            ]);
        });
    }
};
