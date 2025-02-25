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
            'password' => 'Utkarsh@2025#', 
            'role' => 'super_admin',
        ]);

        $admin1 = User::create([
            'name' => 'Admin1',
            'email' => 'admin1@example.com',
            'phone' => '1234567891',
            'user_name' => 'admin1',
            'password' => 'Utkarsh@2025#', 
            'role' => 'admin',
            'origin' => 'JNP'
        ]);

        $admin2 = User::create([
            'name' => 'Admin2',
            'email' => 'admin2@example.com',
            'phone' => '1234567892',
            'user_name' => 'admin2',
            'password' => 'Utkarsh@2025#', 
            'role' => 'admin',
            'origin' => 'GRP'
        ]);

        User::create([
            'name' => 'User11',
            'email' => 'user11@example.com',
            'phone' => '1234567893',
            'user_name' => 'user11',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin1->origin,
            'admin_id' => $admin1->id
        ]);

        User::create([
            'name' => 'User12',
            'email' => 'user12@example.com',
            'phone' => '1234567894',
            'user_name' => 'user12',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin1->origin,
            'admin_id' => $admin1->id
        ]);

        User::create([
            'name' => 'User13',
            'email' => 'user13@example.com',
            'phone' => '1234567895',
            'user_name' => 'user13',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin1->origin,
            'admin_id' => $admin1->id
        ]);
        
        User::create([
            'name' => 'User14',
            'email' => 'user14@example.com',
            'phone' => '1234567896',
            'user_name' => 'user14',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin1->origin,
            'admin_id' => $admin1->id
        ]);

        User::create([
            'name' => 'User15',
            'email' => 'user15@example.com',
            'phone' => '1234567897',
            'user_name' => 'user15',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin1->origin,
            'admin_id' => $admin1->id
        ]);

        User::create([
            'name' => 'User21',
            'email' => 'user21@example.com',
            'phone' => '2234567893',
            'user_name' => 'user21',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin2->origin,
            'admin_id' => $admin2->id
        ]);

        User::create([
            'name' => 'User22',
            'email' => 'user22@example.com',
            'phone' => '2234567894',
            'user_name' => 'user22',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin2->origin,
            'admin_id' => $admin2->id
        ]);

        User::create([
            'name' => 'User23',
            'email' => 'user23@example.com',
            'phone' => '2234567895',
            'user_name' => 'user23',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin2->origin,
            'admin_id' => $admin2->id
        ]);
        
        User::create([
            'name' => 'User24',
            'email' => 'user24@example.com',
            'phone' => '2234567896',
            'user_name' => 'user24',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin2->origin,
            'admin_id' => $admin2->id
        ]);

        User::create([
            'name' => 'User25',
            'email' => 'user25@example.com',
            'phone' => '2234567897',
            'user_name' => 'user25',
            'password' => 'Utkarsh@2025#', 
            'role' => 'user',
            'origin' => $admin2->origin,
            'admin_id' => $admin2->id
        ]);
    }
}
