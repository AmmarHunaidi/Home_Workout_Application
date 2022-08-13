<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WorkoutCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('workout_categories')->delete();
        
        \DB::table('workout_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'recommended',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:20:53',
                'updated_at' => '2022-08-12 10:20:53',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'all',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:20:57',
                'updated_at' => '2022-08-12 10:20:57',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'full body',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:21:03',
                'updated_at' => '2022-08-12 10:21:03',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'abs',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:21:08',
                'updated_at' => '2022-08-12 10:21:08',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'back',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:21:13',
                'updated_at' => '2022-08-12 10:21:13',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'chest',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:21:18',
                'updated_at' => '2022-08-12 10:21:18',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'shoulders',
                'user_id' => 8,
                'created_at' => '2022-08-12 10:21:24',
                'updated_at' => '2022-08-12 10:21:24',
            ),
        ));
        
        
    }
}