<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DailyTipsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('daily_tips')->delete();
        
        \DB::table('daily_tips')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tip' => 'tip 1',
                'created_at' => '2022-07-14 16:20:44',
                'updated_at' => '2022-07-14 16:20:44',
            ),
            1 => 
            array (
                'id' => 2,
                'tip' => 'tip2',
                'created_at' => '2022-07-14 16:20:44',
                'updated_at' => '2022-07-14 16:20:44',
            ),
        ));
        
        
    }
}