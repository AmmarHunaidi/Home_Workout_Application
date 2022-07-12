<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AbilitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('abilities')->delete();
        
        \DB::table('abilities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'user',
                'description' => 'No Description',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}