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
        Schema::create('footer_section_threes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('footer_id');
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();

            $table->foreign('footer_id')->references('id')->on('footers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_section_threes');
    }
};
