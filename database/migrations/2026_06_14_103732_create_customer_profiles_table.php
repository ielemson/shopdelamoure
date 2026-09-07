<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('customer_profiles', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('first_name')->nullable();
    $table->string('last_name')->nullable();

    $table->string('alternate_phone')->nullable();

    $table->string('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->default('Nigeria');

    $table->string('postal_code')->nullable();

    $table->enum('customer_type', [
        'regular',
        'wholesale',
        'vip'
    ])->default('regular');

    $table->enum('status', [
        'active',
        'inactive',
        'suspended'
    ])->default('active');

    $table->text('delivery_note')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
