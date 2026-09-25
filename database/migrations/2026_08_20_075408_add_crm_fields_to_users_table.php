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
            $table->string('mobile')->nullable()->unique();
            $table->string('user_type')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pincode_id')->nullable()->constrained('pincodes')->nullOnDelete();
            $table->string('profile_photo')->nullable();
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('kyc_status')->default('Pending');
            $table->timestamp('registration_date')->useCurrent();
            $table->timestamp('last_login')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['pincode_id']);
            $table->dropColumn([
                'mobile', 'user_type', 'gender', 'dob', 'address', 'state_id', 
                'district_id', 'city_id', 'pincode_id', 'profile_photo', 
                'status', 'kyc_status', 'registration_date', 'last_login'
            ]);
        });
    }
};
