<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rate_areas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('shipping_rate_id')
                ->constrained('shipping_rates')
                ->cascadeOnDelete();

            $table->string('name');

            $table->timestamps();

            $table->index([
                'shipping_rate_id',
                'name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rate_areas');
    }
};
