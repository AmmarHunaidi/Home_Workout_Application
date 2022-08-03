<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AppFeatureTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('app_feature')->delete();
        
        \DB::table('app_feature')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'All features',
                'is_active' => 1,
                'created_at' => '2022-08-02 19:49:00',
                'updated_at' => '2022-08-02 19:49:00',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Posts',
                'is_active' => 1,
                'created_at' => '2022-08-02 19:49:44',
                'updated_at' => '2022-08-02 19:49:44',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Post creation',
                'is_active' => 1,
                'created_at' => '2022-08-02 19:52:00',
                'updated_at' => '2022-08-02 19:52:00',
            ),
        ));
        
        
    }
}