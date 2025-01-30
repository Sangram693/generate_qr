<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sangram Roygupta',
            'email' => 'sneider.ele@gmail.com',
            'phone' => '6296878926',
            'user_name' => 'sneider123',
            'password' => 'Sneider@143#', 
            'role' => 'super_admin',
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'phone' => '1234567890',
            'user_name' => 'superadmin',
            'password' => 'Sneider@143#', 
            'role' => 'super_admin',
        ]);

    }
}
