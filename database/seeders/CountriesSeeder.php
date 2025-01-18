<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            ['name' => 'United States', 'flag' => 'US', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thailand', 'flag' => 'TH', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Japan', 'flag' => 'JP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'United Kingdom', 'flag' => 'UK', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Germany', 'flag' => 'DE', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
