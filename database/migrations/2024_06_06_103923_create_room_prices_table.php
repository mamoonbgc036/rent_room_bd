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
        Schema::create('room_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['Day', 'Week', 'Month']);
            $table->decimal('fixed_price', 8, 2);
            $table->decimal('discount_price', 8, 2)->nullable();
            $table->decimal('booking_price', 8, 2);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('entire_property_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('entire_property_id')->references('id')->on('entire_properties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_prices');
    }
};
