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
            $table->boolean('is_aadhar_verified')->default(false);
            $table->boolean('is_pan_verified')->default(false);
            $table->boolean('is_bank_verified')->default(false);
            $table->boolean('is_invoice_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_aadhar_verified',
                'is_pan_verified',
                'is_bank_verified',
                'is_invoice_verified'
            ]);
        });
    }
};
