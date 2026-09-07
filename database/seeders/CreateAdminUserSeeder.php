<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@shopdelamoure.com',
            ],
            [
                'name' => 'Delamoure Administrator',
                'password' => bcrypt('Abc123456'),
            ]
        );

        // Create the role only if it does not already exist
        $role = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        // Get all permissions
        $permissions = Permission::pluck('name')->all();

        // Assign all permissions to Admin role
        $role->syncPermissions($permissions);

        // Assign Admin role to the user
        $user->syncRoles([$role]);
    }
}
