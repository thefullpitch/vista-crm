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
        Schema::table('installations', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('amount');
            $table->decimal('invoice_amount', 10, 2)->nullable()->after('invoice_number');
            $table->string('invoice_image')->nullable()->after('invoice_amount');
            $table->json('products_data')->nullable()->after('installation_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_amount', 'invoice_image', 'products_data']);
        });
    }
};
