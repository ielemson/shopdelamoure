<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {

            $table->string('support_name')->nullable()->after('address');

            $table->string('support_role')->nullable()->after('support_name');

            $table->string('support_phone')->nullable()->after('support_role');

            $table->string('support_image')->nullable()->after('support_phone');

            $table->string('support_message_title')->nullable()->after('support_image');

            $table->text('support_message_body')->nullable()->after('support_message_title');

        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {

            $table->dropColumn([
                'support_name',
                'support_role',
                'support_phone',
                'support_image',
                'support_message_title',
                'support_message_body',
            ]);

        });
    }
};