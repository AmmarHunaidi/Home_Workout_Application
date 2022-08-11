<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExcersiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('excersises')->delete();

        DB::table('excersises')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Sit Up',
                'description' => 'Sit Up Description',
                'burn_Calories' => 10,
                'excersise_media_url' => '14/DP9kCk6yZ97muO2nkVn77k1eOXQ2w55dd0dbf0-015e-11ea-a5c6-614c29c2a639.jpg',
                'user_id' => 7,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Jumping Jack',
                'description' => 'Jumping Jack Description',
                'burn_Calories' => 25,
                'excersise_media_url' => '18/GICS1x7fB4sRwXHCia7yDKtDGBinS3main-qimg-da9b068fcbd171db72a89ba0d2bf82d0-lq.jpeg',
                'user_id' => 7,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            )
        ));
    }
}
