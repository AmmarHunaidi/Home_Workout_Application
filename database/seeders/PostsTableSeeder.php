<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('posts')->delete();
        
        \DB::table('posts')->insert(array (
            0 => 
            array (
                'id' => 4,
                'user_id' => 1,
                'text' => 'We recommend 3 litres of water per day.',
                'is_accepted' => 1,
                'is_reviewed' => 0,
                'type' => 2,
                'created_at' => '2022-07-14 23:39:16',
                'updated_at' => '2022-07-14 23:39:16',
            ),
            1 => 
            array (
                'id' => 5,
                'user_id' => 1,
                'text' => 'What\'s about the next workout?!💪',
                'is_accepted' => 1,
                'is_reviewed' => 0,
                'type' => 3,
                'created_at' => '2022-07-14 23:42:12',
                'updated_at' => '2022-07-14 23:42:12',
            ),
        ));
        
        
    }
}