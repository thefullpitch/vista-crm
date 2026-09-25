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
            $table->string('aadhar_number')->nullable()->after('wallet_balance');
            $table->string('pan_number')->nullable()->after('aadhar_number');
            $table->string('bank_account_number')->nullable()->after('pan_number');
            $table->string('aadhar_file')->nullable()->after('bank_account_number');
            $table->string('pan_file')->nullable()->after('aadhar_file');
            $table->string('bank_passbook_file')->nullable()->after('pan_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'aadhar_number',
                'pan_number',
                'bank_account_number',
                'aadhar_file',
                'pan_file',
                'bank_passbook_file'
            ]);
        });
    }
};
