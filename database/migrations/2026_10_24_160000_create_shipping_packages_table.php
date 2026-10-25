<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->decimal('length_cm', 10, 2);
            $table->decimal('width_cm', 10, 2);
            $table->decimal('height_cm', 10, 2);
            $table->decimal('empty_weight_kg', 10, 3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_packages');
    }
};
