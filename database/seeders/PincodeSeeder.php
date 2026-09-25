<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\City;
use App\Models\Pincode;

class PincodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all();
        $inserts = [];
        $now = now();

        foreach ($cities as $city) {
            // Generate a realistic 6-digit fallback based on state id to avoid duplicates
            // Example: State ID 27, City ID 145 -> Pincode 270145
            $statePrefix = str_pad(min($city->state_id, 99), 2, '0', STR_PAD_LEFT);
            $citySuffix = str_pad(min($city->id, 9999), 4, '0', STR_PAD_LEFT);
            
            $pincode = (int) ($statePrefix . $citySuffix);
            
            if (strlen((string)$pincode) > 6) {
                $pincode = substr((string)$pincode, 0, 6);
            } else if (strlen((string)$pincode) < 6) {
                $pincode = str_pad((string)$pincode, 6, '0', STR_PAD_RIGHT);
            }

            $inserts[] = [
                'state_id' => $city->state_id,
                'district_id' => $city->district_id,
                'city_id' => $city->id,
                'pincode' => (string) $pincode,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Bulk insert in chunks for massive performance
        $chunks = array_chunk($inserts, 500);
        foreach ($chunks as $chunk) {
            Pincode::insertOrIgnore($chunk);
        }
        
        $this->command->info("Inserted ".count($inserts)." pincodes.");
    }
}
