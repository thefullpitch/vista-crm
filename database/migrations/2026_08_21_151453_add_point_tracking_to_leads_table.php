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
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('points_earned')->default(0)->after('status');
            $table->boolean('awarded_progress_points')->default(false)->after('points_earned');
            $table->boolean('awarded_converted_points')->default(false)->after('awarded_progress_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['points_earned', 'awarded_progress_points', 'awarded_converted_points']);
        });
    }
};
