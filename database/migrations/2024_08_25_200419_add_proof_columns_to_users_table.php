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
        Schema::table('users', function (Blueprint $table) {
            $table->string('proof_type_1')->nullable();
            $table->string('proof_path_1')->nullable();
            $table->string('proof_type_2')->nullable();
            $table->string('proof_path_2')->nullable();
            $table->string('proof_type_3')->nullable();
            $table->string('proof_path_3')->nullable();
            $table->string('proof_type_4')->nullable();
            $table->string('proof_path_4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('proof_type_1');
            $table->dropColumn('proof_path_1');
            $table->dropColumn('proof_type_2');
            $table->dropColumn('proof_path_2');
            $table->dropColumn('proof_type_3');
            $table->dropColumn('proof_path_3');
            $table->dropColumn('proof_type_4');
            $table->dropColumn('proof_path_4');
        });
    }
};
