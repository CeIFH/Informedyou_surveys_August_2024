<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'role_id' => 1,
                'name' => 'Wave Admin',
                'email' => 'admin@admin.com',
                'username' => 'admin',
                'avatar' => 'users/default.png',
                'password' => '$2y$10$L8MjmjVVOCbyLHbp7pq/9.1ZEEa5AqE67ZXLd2M4.res05a3Rz/G2',
                'remember_token' => '4oXDVo48Lm1pc4j7NkWI9cMO4hv5OIEJFMrqjSCKQsIwWMGRFYDvNpdioBfo',
                'settings' => NULL,
                'created_at' => '2017-11-21 16:07:22',
                'updated_at' => '2018-09-22 23:34:02',
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
                'verification_code' => NULL,
                'verified' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'role_id' => 3,
                'name' => 'Owner User',
                'email' => 'owner@example.com',
                'username' => 'owner',
                'avatar' => 'users/default.png',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'settings' => NULL,
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
                'verification_code' => NULL,
                'verified' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'role_id' => 4,
                'name' => 'Editor User',
                'email' => 'editor@example.com',
                'username' => 'editor',
                'avatar' => 'users/default.png',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'settings' => NULL,
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
                'verification_code' => NULL,
                'verified' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'role_id' => 5,
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'username' => 'user',
                'avatar' => 'users/default.png',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'settings' => NULL,
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
                'verification_code' => NULL,
                'verified' => 1,
            ),
        ));

        // Assign companies to non-admin users
        $nonAdminUsers = User::where('role_id', '!=', 1)->get();
        foreach ($nonAdminUsers as $user) {
            $companies = Company::inRandomOrder()->take(rand(1, 3))->get();
            $user->companies()->attach($companies);
        }
        
        
    }
}