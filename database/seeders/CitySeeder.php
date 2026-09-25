<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\State;
use App\Models\District;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::withoutVerifying()->get('https://raw.githubusercontent.com/nshntarora/Indian-Cities-JSON/master/cities.json');
        
        if (!$response->successful()) {
            $this->command->error("Failed to fetch JSON from URL.");
            return;
        }

        $data = $response->json();

        if (!is_array($data)) {
            $this->command->error("Failed to parse JSON data.");
            return;
        }

        foreach ($data as $cityData) {
            $stateName = $cityData['state'] ?? '';
            $cityName = $cityData['name'] ?? '';
            
            // Try to match the state
            $state = State::where('name', 'like', "%{$stateName}%")->first();
            
            if ($state) {
                // Try to find a district with the same name, or just use the first district in that state
                $district = District::where('state_id', $state->id)
                                    ->where('name', 'like', "%{$cityName}%")
                                    ->first();
                
                if (!$district) {
                    $district = District::where('state_id', $state->id)->first();
                }

                if ($district) {
                    City::firstOrCreate([
                        'state_id' => $state->id,
                        'district_id' => $district->id,
                        'name' => $cityName
                    ], ['status' => 1]);
                }
            }
        }
    }
}
