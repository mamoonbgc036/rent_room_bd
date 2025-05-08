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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('auto_renewal')->default(false);
            $table->integer('renewal_period_days')->nullable();
            $table->timestamp('next_renewal_date')->nullable();
            $table->timestamp('last_renewal_date')->nullable(); // Fixed column definition
            $table->string('renewal_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'auto_renewal',
                'renewal_period_days',
                'next_renewal_date',
                'last_renewal_date',
                'renewal_status',
            ]);
        });
    }
};
