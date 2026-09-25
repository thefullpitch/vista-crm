<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'locations.view',
            'zones.view', 'zones.create', 'zones.edit', 'zones.delete',
            'states.view', 'states.create', 'states.edit', 'states.delete',
            'cities.view', 'cities.create', 'cities.edit', 'cities.delete',
            'pincodes.view', 'pincodes.create', 'pincodes.edit', 'pincodes.delete',
            'shops.view', 'shops.create', 'shops.edit', 'shops.delete',
            'installations.view', 'installations.edit', 'installations.delete',
            'leads.view', 'leads.edit', 'leads.delete',
            'wallets.view', 'wallets.edit', 'wallets.delete',
            'redemptions.view', 'redemptions.edit', 'redemptions.delete',
            'notifications.view', 'notifications.create', 'notifications.delete',
            'cms.view', 'cms.edit'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $superAdmin = Role::where('name', 'Super Admin')->where('guard_name', 'admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }
}
