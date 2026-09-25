<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely convert existing single paths to JSON arrays
        $installations = DB::table('installations')->whereNotNull('installation_photo')->get();
        foreach ($installations as $inst) {
            $photo = $inst->installation_photo;
            if (!Str::startsWith(trim($photo), '[')) {
                DB::table('installations')
                    ->where('id', $inst->id)
                    ->update(['installation_photo' => json_encode([$photo])]);
            }
        }

        Schema::table('installations', function (Blueprint $table) {
            $table->text('installation_photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->string('installation_photo', 255)->nullable()->change();
        });
    }
};
