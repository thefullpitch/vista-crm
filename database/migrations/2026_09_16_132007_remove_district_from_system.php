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
        $tables = ['cities', 'pincodes', 'users', 'shops', 'dealers', 'distributors'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableSchema) {
                if (\Schema::hasColumn($tableSchema->getTable(), 'district_id')) {
                    $tableSchema->dropColumn('district_id');
                }
            });
        }
        
        Schema::dropIfExists('districts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Down migration omitted for simplicity, as restoring implies recreating the districts table and re-linking IDs which is non-trivial without data backups.
    }
};
