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
                'name' => 'user',
                'description' => 'Normal users',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'coach',
                'description' => 'creator of workouts',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'dietitian',
                'description' => 'creator of Diets ',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'manager',
                'description' => 'Administrator',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'zuper admin',
                'description' => 'app owner',
            ),
        ));
        
        
    }
}