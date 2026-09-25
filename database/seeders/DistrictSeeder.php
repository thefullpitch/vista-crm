<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Facades\Http;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::withoutVerifying()->get('https://raw.githubusercontent.com/sab99r/Indian-States-And-Districts/master/states-and-districts.json');
        
        if (!$response->successful()) {
            $this->command->error("Failed to fetch JSON from URL.");
            return;
        }

        $data = $response->json();

        if (!$data || !isset($data['states'])) {
            $this->command->error("Failed to parse JSON data.");
            return;
        }

        foreach ($data['states'] as $stateData) {
            $stateName = $stateData['state'];
            $state = State::where('name', $stateName)->first();
            
            if ($state) {
                foreach ($stateData['districts'] as $districtName) {
                    District::firstOrCreate([
                        'state_id' => $state->id,
                        'name' => $districtName
                    ], ['status' => 1]);
                }
            } else {
                $this->command->warn("State not found: $stateName");
            }
        }
    }
}
