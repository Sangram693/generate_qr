<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'MBCB',
                'code' => 'MB001',
                'description' => 'Metal Beam Crash Barrier',
                'category' => 'Safety Equipment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HM',
                'code' => 'HM001',
                'description' => 'High Mast',
                'category' => 'Lighting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'POLE',
                'code' => 'PL001',
                'description' => 'Standard Pole',
                'category' => 'Infrastructure',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
