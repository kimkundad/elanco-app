<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'description' => 'Administrator with full access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Editor', 'description' => 'Can edit and update content', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Viewer', 'description' => 'Can only view content', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
