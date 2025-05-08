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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('duration_type')->nullable()->after('booking_type'); // Adjust `after` as needed
            $table->enum('payment_status', ['Pending', 'Paid'])->default('Pending')->after('duration_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('duration_type');
            $table->dropColumn('payment_status');
        });
    }
};
