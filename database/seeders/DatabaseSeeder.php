<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        DB::table('roles')->insert([
            [
                'name' => 'superadmin',
                'description' => 'A SuperAdmin User'
            ],
            [
                'name' => 'admin',
                'description' => 'A admin User'
            ],
            [
                'name' => 'user',
                'description' => 'A customer User'
            ]
        ]);

        DB::table('role_user')->insert([
            [
                'role_id' => 1,
                'user_id' => 1
            ],
            [
                'role_id' => 2,
                'user_id' => 2
            ],
            [
                'role_id' => 3,
                'user_id' => 3
            ],
            [
                'role_id' => 1,
                'user_id' => 4
            ],
        ]);
        DB::table('users')->insert([
            [
              'name' => 'superadmin',
              'email' => 'admin@email.com',
              'password' => bcrypt('qaz123456'),
              'code_user' => 'CV999999',
              'avatar' => '300-10.jpg',
              'email_verified_at' => '2020-08-01 14:27:38',
              'provider' => 'email',
              'phone' => '0912345678',
              'is_admin' => 1
            ],
            [
              'name' => 'administrator',
              'email' => 'administrator@email.com',
              'password' => bcrypt('qaz123456'),
              'code_user' => 'CV111111',
              'avatar' => '300-11.jpg',
              'email_verified_at' => '2020-08-01 14:27:38',
              'provider' => 'email',
              'phone' => '0812345678',
              'is_admin' => 1
            ],
            [
                'name' => 'kimkundad',
                'email' => 'kim.kundad@email.com',
                'password' => bcrypt('1429800'),
                'code_user' => 'UD000002',
                'avatar' => '300-23.jpg',
                'email_verified_at' => '2020-08-01 14:27:38',
                'provider' => 'email',
                'phone' => 'kimkundad',
                'is_admin' => 0
              ],
              [
                  'name' => 'Admin',
                  'email' => 'juthamart.so@titangroup.co.th',
                  'password' => bcrypt('Admin168##'),
                  'code_user' => 'UD000003',
                  'avatar' => '300-23.jpg',
                  'email_verified_at' => '2020-08-01 14:27:38',
                  'provider' => 'email',
                  'phone' => 'kimkundad',
                  'is_admin' => 1
                ]
        ]);

        DB::table('settings')->insert([
            [
            'name_website' => 'wealthangels',
            'facebook' => 'Wealthangels',
            'facebook_url' => 'https://www.facebook.com/wealthangels',
            'facebook' => 'Wealthangels',
            'facebook_image' => '1607528770.jpg',
            'phone' => '0958467417',
            'email' => 'thewealthangels@gmail.com'
            ]
        ]);



    }
}
