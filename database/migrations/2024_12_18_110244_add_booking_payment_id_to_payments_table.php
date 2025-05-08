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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_payment_id')->nullable()->after('booking_id');

            // Add a foreign key constraint to link to the booking_payments table
            $table->foreign('booking_payment_id')
                  ->references('id')
                  ->on('booking_payments')
                  ->onDelete('set null'); // Set to null if the related booking_payment is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_payment_id']);
            $table->dropColumn('booking_payment_id');
        });
    }
};
