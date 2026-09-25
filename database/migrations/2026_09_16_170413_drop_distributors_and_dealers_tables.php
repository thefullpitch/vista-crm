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
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['dealer_id']);
            $table->dropColumn('dealer_id');
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
        });

        Schema::dropIfExists('dealers');
        Schema::dropIfExists('distributors');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-creating is omitted as it's a permanent architectural shift.
    }
};
