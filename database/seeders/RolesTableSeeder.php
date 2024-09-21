<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'admin',
                'display_name' => 'Admin User',
                'created_at' => '2017-11-21 16:23:22',
                'updated_at' => '2017-11-21 16:23:22',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'trial',
                'display_name' => 'Free Trial',
                'created_at' => '2017-11-21 16:23:22',
                'updated_at' => '2017-11-21 16:23:22',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'owner',
                'display_name' => 'Owner',
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'editor',
                'display_name' => 'Editor',
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'user',
                'display_name' => 'User',
                'created_at' => '2023-08-01 00:00:00',
                'updated_at' => '2023-08-01 00:00:00',
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'cancelled',
                'display_name' => 'Cancelled User',
                'created_at' => '2018-07-03 16:28:42',
                'updated_at' => '2018-07-03 17:28:32',
            ),
        ));


    }
}
