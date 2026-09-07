<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->unsignedMediumInteger('country_id');

            $table->string('name');
            $table->string('code', 20)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnDelete();

            $table->index(['country_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
