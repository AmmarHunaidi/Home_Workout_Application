<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workouts')->delete();

        DB::table('workouts')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'hiit',
                'length' => 1,
                'description' => "Hello",
                'excersise_count' => 0,
                'predicted_burnt_calories' => 10,
                'review_count' => 4,
                'equipment' => 'Required',
                'difficulty' => 1,
                'user_id' => 8,
                'categorie_id' => 3,
                'workout_image_url' => 'Default/618BDFLRn3L._AC_SY450_.jpg',
                'approval' => 1,
                'created_at' => '2022-08-08 13:11:00',
                'updated_at' => '2022-08-08 13:11:00',
            )
        ));
    }
}
