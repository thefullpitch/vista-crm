<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Admin;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use App\Models\KycRecord;
use App\Models\Distributor;
use App\Models\Dealer;
use App\Models\Shop;
use App\Models\ShopMechanic;
use App\Models\Invoice;
use App\Models\Installation;
use App\Models\RewardCategory;
use App\Models\Reward;
use App\Models\RedemptionRequest;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_IN');

        // 1. Settings
        Setting::updateOrCreate(['key' => 'app_name'], ['value' => 'Vista CRM Pro', 'group' => 'general']);
        Setting::updateOrCreate(['key' => 'points_conversion_rate'], ['value' => '1', 'group' => 'points']);

        // 2. Locations
        $states = ['Maharashtra', 'Gujarat', 'Karnataka'];
        $districts = ['Mumbai Suburban', 'Pune', 'Ahmedabad', 'Surat', 'Bangalore Urban'];
        
        foreach ($states as $s) {
            $state = State::create(['name' => $s]);
            foreach (array_rand(array_flip($districts), 2) as $d) {
                $district = District::create(['state_id' => $state->id, 'name' => $d]);
                for ($i=0; $i<2; $i++) {
                    $city = City::create(['state_id' => $state->id, 'district_id' => $district->id, 'name' => $faker->city]);
                    for ($j=0; $j<2; $j++) {
                        Pincode::create(['state_id' => $state->id, 'district_id' => $district->id, 'city_id' => $city->id, 'pincode' => $faker->unique()->numerify('######')]);
                    }
                }
            }
        }

        // 3. Admin User (if missing, but we assume ID 1 exists)
        $admin = Admin::first();
        if (!$admin) {
            $admin = Admin::create([
                'name' => 'Super Admin',
                'email' => 'admin@vistacrm.com',
                'password' => bcrypt('password'),
                'status' => 1
            ]);
        }

        // 4. Mechanics (Users) & KYC
        $mechanics = [];
        $pincodes = Pincode::pluck('id')->toArray();
        for ($i = 0; $i < 20; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'mobile' => '9' . $faker->numerify('#########'),
                'password' => bcrypt('password123'),
                'pincode_id' => $faker->randomElement($pincodes),
                'wallet_balance' => $faker->randomFloat(2, 0, 5000),
                'status' => 1
            ]);
            $mechanics[] = $user;

            // Generate KYC
            KycRecord::create([
                'user_id' => $user->id,
                'document_type' => $faker->randomElement(['Aadhar Card', 'PAN Card', 'Driving License']),
                'document_number' => strtoupper(Str::random(10)),
                'document_file_front' => 'dummy/kyc.jpg',
                'status' => $faker->randomElement(['Pending', 'Approved', 'Rejected']),
                'verified_by' => $faker->boolean ? $admin->id : null,
            ]);
        }

        // 5. Business Network
        for ($i = 0; $i < 3; $i++) {
            $distributorOwner = $faker->randomElement($mechanics);
            $distributor = Distributor::create([
                'name' => $faker->company . ' Distributors',
                'owner_id' => $distributorOwner->id,
                'contact_number' => '8' . $faker->numerify('#########'),
                'address' => $faker->address,
                'status' => 'Active'
            ]);

            for ($j = 0; $j < 3; $j++) {
                $dealerOwner = $faker->randomElement($mechanics);
                $dealer = Dealer::create([
                    'distributor_id' => $distributor->id,
                    'name' => $faker->company . ' Auto Parts',
                    'owner_id' => $dealerOwner->id,
                    'contact_number' => '7' . $faker->numerify('#########'),
                    'address' => $faker->address,
                    'status' => 'Active'
                ]);

                for ($k = 0; $k < 5; $k++) {
                    $shopOwner = $faker->randomElement($mechanics);
                    $shop = Shop::create([
                        'dealer_id' => $dealer->id,
                        'name' => $faker->company . ' Garage',
                        'owner_id' => $shopOwner->id,
                        'contact_number' => '6' . $faker->numerify('#########'),
                        'address' => $faker->address,
                        'status' => 'Active'
                    ]);

                    // Map 1-3 random mechanics to this shop
                    $randomMechs = $faker->randomElements($mechanics, rand(1, 3));
                    foreach ($randomMechs as $mech) {
                        ShopMechanic::firstOrCreate([
                            'shop_id' => $shop->id,
                            'mechanic_id' => $mech->id
                        ]);
                    }
                }
            }
        }

        // 6. Transactions (Invoices & Installations)
        $shops = Shop::pluck('id')->toArray();
        foreach ($mechanics as $mech) {
            // Invoices
            for ($i = 0; $i < rand(1, 4); $i++) {
                $status = $faker->randomElement(['Pending', 'Verified', 'Rejected']);
                $points = $status == 'Verified' ? rand(50, 500) : 0;
                Invoice::create([
                    'user_id' => $mech->id,
                    'shop_id' => $faker->randomElement($shops),
                    'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
                    'invoice_date' => $faker->dateTimeThisYear()->format('Y-m-d'),
                    'amount' => $faker->randomFloat(2, 500, 5000),
                    'document_file' => 'dummy/invoice.jpg',
                    'points_earned' => $points,
                    'status' => $status,
                    'verified_by' => $status != 'Pending' ? $admin->id : null,
                    'verified_at' => $status != 'Pending' ? now() : null,
                ]);
            }
            
            // Installations
            for ($i = 0; $i < rand(1, 3); $i++) {
                $status = $faker->randomElement(['Pending', 'Verified', 'Rejected']);
                $points = $status == 'Verified' ? rand(20, 200) : 0;
                Installation::create([
                    'user_id' => $mech->id,
                    'customer_name' => $faker->name,
                    'customer_mobile' => '9' . $faker->numerify('#########'),
                    'product_serial_number' => 'SN-' . strtoupper(Str::random(8)),
                    'installation_photo' => 'dummy/installation.jpg',
                    'points_earned' => $points,
                    'status' => $status,
                    'verified_by' => $status != 'Pending' ? $admin->id : null,
                    'verified_at' => $status != 'Pending' ? now() : null,
                ]);
            }
            
            // Leads
            for ($i = 0; $i < rand(0, 2); $i++) {
                Lead::create([
                    'user_id' => $mech->id,
                    'customer_name' => $faker->name,
                    'customer_mobile' => '9' . $faker->numerify('#########'),
                    'product_interest' => $faker->randomElement(['Premium Battery', 'Brake Pads', 'Oil Filter']),
                    'remarks' => $faker->sentence,
                    'status' => $faker->randomElement(['New', 'In Progress', 'Converted', 'Closed']),
                    'assigned_to' => $faker->boolean ? $admin->id : null,
                ]);
            }
        }

        // 7. Rewards Engine
        $cats = ['Electronics', 'Gift Vouchers', 'Tools'];
        $rewardObjects = [];
        foreach ($cats as $cat) {
            $rc = RewardCategory::create(['name' => $cat, 'status' => 1]);
            for ($i = 0; $i < 3; $i++) {
                $rewardObjects[] = Reward::create([
                    'category_id' => $rc->id,
                    'name' => $faker->words(3, true),
                    'description' => $faker->paragraph,
                    'points_required' => rand(500, 5000),
                    'status' => 1
                ]);
            }
        }

        // 8. Redemption Requests
        foreach (array_slice($mechanics, 0, 5) as $mech) {
            $rew = $faker->randomElement($rewardObjects);
            $status = $faker->randomElement(['Pending', 'Approved', 'Shipped', 'Delivered']);
            RedemptionRequest::create([
                'user_id' => $mech->id,
                'reward_id' => $rew->id,
                'points_redeemed' => $rew->points_required,
                'shipping_address' => $faker->address,
                'status' => $status,
                'processed_by' => $status != 'Pending' ? $admin->id : null,
                'processed_at' => $status != 'Pending' ? now() : null,
            ]);
        }
    }
}
