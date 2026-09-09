<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            /*
            |--------------------------------------------------------------------------
            | Roles & Permissions
            |--------------------------------------------------------------------------
            */
            RolePermissionSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            */
            CreateAdminUserSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */
            CountrySeeder::class,
            StateSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Catalogue
            |--------------------------------------------------------------------------
            */
            CategorySeeder::class,
            ProductSeeder::class,
            // Shipping Rate Seeder
            ShippingRateSeeder::class,

        ]);
    }
}
