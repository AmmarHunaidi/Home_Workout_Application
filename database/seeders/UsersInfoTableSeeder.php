<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersInfoTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users_info')->delete();
        
        \DB::table('users_info')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'height' => 180.0,
                'weight' => 70.0,
                'height_unit' => 'cm',
                'weight_unit' => 'kg',
                'changed_at' => NULL,
                'created_at' => '2022-08-09 20:13:38',
                'updated_at' => '2022-08-09 20:13:38',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 2,
                'height' => 180.0,
                'weight' => 70.0,
                'height_unit' => 'cm',
                'weight_unit' => 'kg',
                'changed_at' => NULL,
                'created_at' => '2022-08-09 20:13:38',
                'updated_at' => '2022-08-09 20:13:38',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 5,
                'height' => 180.0,
                'weight' => 70.0,
                'height_unit' => 'cm',
                'weight_unit' => 'kg',
                'changed_at' => NULL,
                'created_at' => '2022-08-09 20:13:38',
                'updated_at' => '2022-08-09 20:13:38',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 8,
                'height' => 180.0,
                'weight' => 70.0,
                'height_unit' => 'cm',
                'weight_unit' => 'kg',
                'changed_at' => NULL,
                'created_at' => '2022-08-09 20:13:38',
                'updated_at' => '2022-08-09 20:13:38',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 6,
                'height' => 180.0,
                'weight' => 70.0,
                'height_unit' => 'cm',
                'weight_unit' => 'kg',
                'changed_at' => NULL,
                'created_at' => '2022-08-09 20:13:38',
                'updated_at' => '2022-08-09 20:13:38',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 7,
                'height' => 180.0,
                'weight' => 70.0,
                'height_unit' => 'cm',
                'weight_unit' => 'kg',
                'changed_at' => NULL,
                'created_at' => '2022-08-09 20:13:38',
                'updated_at' => '2022-08-09 20:13:38',
            ),
        ));
        
        
    }
}