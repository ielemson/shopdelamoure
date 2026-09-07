<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear Spatie Permission Cache
        |--------------------------------------------------------------------------
        */
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $customerRole = Role::firstOrCreate([
            'name' => 'Customer',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            /*
            | Admin
            */
            'view admin dashboard',

            'view products',
            'create products',
            'edit products',
            'delete products',

            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            'view orders',
            'manage orders',

            'view customers',
            'manage customers',

            'manage website settings',

            /*
            | Customer
            */
            'view customer dashboard',
            'view own orders',
            'manage own addresses',
            'manage own profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Permissions
        |--------------------------------------------------------------------------
        | Admin receives all available permissions.
        */
        $adminRole->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        /*
        |--------------------------------------------------------------------------
        | Customer Permissions
        |--------------------------------------------------------------------------
        */
        $customerRole->syncPermissions([
            'view customer dashboard',
            'view own orders',
            'manage own addresses',
            'manage own profile',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Clear Cache Again
        |--------------------------------------------------------------------------
        */
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}
