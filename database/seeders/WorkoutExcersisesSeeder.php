<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkoutExcersisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workout_excersises')->delete();

        DB::table('workout_excersises')->insert(array (
            0 =>
            array (
                'id' => 1,
                'excersise_id' => 2,
                'workout_id' => 1,
                'position' => 1,
                'count' => 1,
                'length' => NULL,
                'user_id' => 8,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            ),
            1 =>
            array (
                'id' => 2,
                'excersise_id' => 1,
                'workout_id' => 1,
                'position' => 2,
                'count' => NULL,
                'length' => 15,
                'user_id' => 8,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            ),
            3 =>
            array (
                'id' => 3,
                'excersise_id' => 2,
                'workout_id' => 1,
                'position' => 3,
                'count' => NULL,
                'length' => 16,
                'user_id' => 8,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            ),
            4 =>
            array (
                'id' => 4,
                'excersise_id' => 1,
                'workout_id' => 1,
                'position' => 4,
                'count' => NULL,
                'length' => 9,
                'user_id' => 8,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            )
        ));
    }
}
