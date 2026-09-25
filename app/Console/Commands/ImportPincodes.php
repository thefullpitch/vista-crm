<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\State;
use App\Models\City;
use App\Models\Pincode;

class ImportPincodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pincodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all India pincodes, clears existing, and maps to states and cities.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $this->info('Starting Pincode Import...');

        // Step 1: Empty the pincodes table
        $this->info('Truncating pincodes table...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pincodes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Pincodes table truncated.');

        // Step 2: Download data
        $url = 'https://raw.githubusercontent.com/mithunsasidharan/India-Pincode-Lookup/master/pincodes.json';
        $this->info("Downloading data from {$url}...");
        
        try {
            $response = Http::withoutVerifying()->timeout(120)->get($url);
            if (!$response->successful()) {
                $this->error('Failed to download pincodes data. HTTP Status: ' . $response->status());
                return;
            }
            $data = $response->json();
        } catch (\Exception $e) {
            $this->error('Error downloading data: ' . $e->getMessage());
            return;
        }

        if (!is_array($data) || empty($data)) {
            $this->error('Downloaded data is empty or invalid format.');
            return;
        }
        
        $this->info('Downloaded ' . count($data) . ' records. Processing...');

        // Pre-load states to minimize DB queries
        $states = State::all()->mapWithKeys(function ($item) {
            return [strtolower(trim($item->name)) => $item->id];
        })->toArray();

        // Pre-load cities to minimize DB queries
        // Store as array of ['state_id_city_name' => city_id]
        $cities = City::all()->mapWithKeys(function ($item) {
            return [$item->state_id . '_' . strtolower(trim($item->name)) => $item->id];
        })->toArray();

        $pincodesToInsert = [];
        $uniquePincodes = []; // To avoid duplicate pincode entries
        
        // Progress bar
        $bar = $this->output->createProgressBar(count($data));
        
        $missingStates = [];

        foreach ($data as $row) {
            $bar->advance();

            $pincodeStr = $row['pincode'] ?? null;
            $stateName = $row['stateName'] ?? null;
            $cityName = $row['districtName'] ?? null; // using districtName as city
            
            if (!$pincodeStr || !$stateName || !$cityName) {
                continue;
            }

            // check if pincode already processed
            if (isset($uniquePincodes[$pincodeStr])) {
                continue;
            }
            $uniquePincodes[$pincodeStr] = true;

            $stateKey = strtolower(trim($stateName));
            if (!isset($states[$stateKey])) {
                // If state doesn't exist, we skip or you can choose to create it.
                // Assuming all 36 states exist in DB.
                if(!isset($missingStates[$stateKey])) {
                    $missingStates[$stateKey] = 1;
                }
                continue;
            }
            $stateId = $states[$stateKey];

            $cityKey = $stateId . '_' . strtolower(trim($cityName));
            
            // If city not found in our array, create it
            if (!isset($cities[$cityKey])) {
                $newCity = City::create([
                    'state_id' => $stateId,
                    'name' => trim($cityName),
                    'status' => 1
                ]);
                $cities[$cityKey] = $newCity->id;
            }
            $cityId = $cities[$cityKey];

            $pincodesToInsert[] = [
                'state_id' => $stateId,
                'city_id' => $cityId,
                'pincode' => $pincodeStr,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Batch insert every 1000 records
            if (count($pincodesToInsert) >= 1000) {
                Pincode::insert($pincodesToInsert);
                $pincodesToInsert = [];
            }
        }
        
        // Insert remaining
        if (count($pincodesToInsert) > 0) {
            Pincode::insert($pincodesToInsert);
        }

        $bar->finish();
        $this->newLine();
        
        if (!empty($missingStates)) {
            $this->warn('Some pincodes were skipped because their states were not found in the DB:');
            foreach(array_keys($missingStates) as $ms) {
                $this->line("- $ms");
            }
        }

        $this->info('Pincodes import completed successfully!');
    }
}
