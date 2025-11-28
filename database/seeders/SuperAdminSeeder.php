<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the role exists
        $role = Role::firstOrCreate(["name" => "Super Admin"]);

        // 2. Create or update the superadmin user
        $user = User::updateOrCreate([
            "email" => "superadmin@kudsrpskivez.com",
            "name" => "Super Admin", // or any name you like
            "password" => Hash::make("poi098QWE!@#"),
        ]);

        // 3. Assign the role
        $user->assignRole($role);
    }
}
