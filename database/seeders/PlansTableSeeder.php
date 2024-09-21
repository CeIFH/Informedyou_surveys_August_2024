<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('plans')->delete();

        \DB::table('plans')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Sign up for the User Plan to access basic features.',
                'features' => 'Create surveys, Collect responses, Basic analytics',
                'plan_id' => '1',
                'role_id' => 5,
                'default' => 1,
                'price' => '5',
                'trial_days' => 0,
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Sign up for the Editor Plan to access advanced editing features.',
                'features' => 'All User features, Advanced question types, Custom branding, Export data',
                'plan_id' => '2',
                'role_id' => 4,
                'default' => 0,
                'price' => '15',
                'trial_days' => 7,
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Get full control with our Owner Plan, including team management.',
                'features' => 'All Editor features, Team collaboration, White-label surveys, API access',
                'plan_id' => '3',
                'role_id' => 3,
                'default' => 0,
                'price' => '30',
                'trial_days' => 14,
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
            ),
        ));


    }
}
