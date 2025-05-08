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
            $table->enum('stay_status', ['staying', 'want_to'])->nullable();
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('stay_status');
            $table->dropColumn('package_id');
        });
    }
};
