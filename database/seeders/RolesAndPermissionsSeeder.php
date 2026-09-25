<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions list
        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.approve', 'users.reject', 'users.suspend',
            'kyc.view', 'kyc.verify', 'kyc.approve', 'kyc.reject', 'kyc.reupload',
            'invoice.view', 'invoice.verify', 'invoice.approve', 'invoice.reject', 'invoice.bulk_approve',
            'rewards.view', 'rewards.create', 'rewards.edit', 'rewards.delete', 'rewards.approve',
            'reports.view', 'reports.export',
            'settings.view', 'settings.update',
            'audit_logs.view',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'admin_users.view', 'admin_users.create', 'admin_users.edit', 'admin_users.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'subcategories.view', 'subcategories.create', 'subcategories.edit', 'subcategories.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }

        // Roles
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin']);

        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'admin']);
        $manager->givePermissionTo([
            'users.view', 'users.edit', 'users.approve', 'users.reject',
            'kyc.view', 'kyc.verify', 'kyc.approve', 'kyc.reject',
            'invoice.view', 'invoice.verify', 'invoice.approve', 'invoice.reject',
            'rewards.view', 'rewards.approve',
            'reports.view', 'reports.export'
        ]);

        $verificationOfficer = Role::create(['name' => 'Verification Officer', 'guard_name' => 'admin']);
        $verificationOfficer->givePermissionTo([
            'kyc.view', 'kyc.verify', 'kyc.approve', 'kyc.reject', 'kyc.reupload',
            'invoice.view', 'invoice.verify', 'invoice.approve', 'invoice.reject'
        ]);

        $supportExecutive = Role::create(['name' => 'Support Executive', 'guard_name' => 'admin']);
        $supportExecutive->givePermissionTo([
            'users.view', 'kyc.view', 'invoice.view', 'rewards.view'
        ]);
    }
}
