<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CustomerRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Customer Role if not exists
        $customerRole = Role::firstOrCreate([
            'name' => 'Customer'
        ]);

        // Create Customer User
        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'John Customer',
                'password' => bcrypt('123456')
            ]
        );

        // Assign Role
        $customer->assignRole($customerRole);
    }
}