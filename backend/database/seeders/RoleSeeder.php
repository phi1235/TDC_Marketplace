<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create permissions
        $permissions = [
            'listings.create',
            'listings.edit',
            'listings.delete',
            'listings.approve',
            'listings.reject',
            'offers.create',
            'offers.accept',
            'offers.reject',
            'wishlists.manage',
            'reports.create',
            'reports.handle',
            'users.manage',
            'admin.dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());
        $userRole->syncPermissions([
            'listings.create',
            'listings.edit',
            'listings.delete',
            'offers.create',
            'wishlists.manage',
            'reports.create',
        ]);
    }
}
