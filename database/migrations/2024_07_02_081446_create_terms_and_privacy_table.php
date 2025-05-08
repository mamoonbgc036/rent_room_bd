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
        Schema::create('terms_and_privacy', function (Blueprint $table) {
            $table->id();
            $table->string('terms_title')->nullable();
            $table->string('terms_link')->nullable();
            $table->string('privacy_title')->nullable();
            $table->string('privacy_link')->nullable();
            $table->string('rights_reserves_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_and_privacy');
    }
};
