<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->renameColumn('twitter', 'snapchat');
            $table->dropColumn(['youtube', 'linkedin']);
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->renameColumn('snapchat', 'twitter');
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
        });
    }
};
