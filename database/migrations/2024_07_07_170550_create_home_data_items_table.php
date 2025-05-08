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
        Schema::create('home_data_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_data_id');
            $table->string('item_image')->nullable();
            $table->string('item_title');
            $table->text('item_des');
            $table->timestamps();
        
            $table->foreign('home_data_id')->references('id')->on('home_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_data_items');
    }
};
