<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'subcategories.view', 'subcategories.create', 'subcategories.edit', 'subcategories.delete',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $admin = \Spatie\Permission\Models\Role::where('name', 'Admin')->where('guard_name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        $manager = \Spatie\Permission\Models\Role::where('name', 'Manager')->where('guard_name', 'admin')->first();
        if ($manager) {
            $manager->givePermissionTo(['products.view', 'categories.view', 'subcategories.view']);
        }
    }
}
