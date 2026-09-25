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
        Schema::table('redemption_requests', function (Blueprint $table) {
            $table->dropForeign(['reward_id']);
            $table->dropColumn('reward_id');
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::dropIfExists('rewards');
        Schema::dropIfExists('reward_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Omitted
    }
};
