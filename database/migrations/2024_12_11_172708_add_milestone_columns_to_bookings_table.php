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
            $table->integer('total_milestones')->nullable()->after('price_type');
            $table->decimal('milestone_amount', 10, 2)->nullable()->after('total_milestones');
            $table->json('milestone_breakdown')->nullable()->after('milestone_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['total_milestones', 'milestone_amount', 'milestone_breakdown']);
        });
    }
};
