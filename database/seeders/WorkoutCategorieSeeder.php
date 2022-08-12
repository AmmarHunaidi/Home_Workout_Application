<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkoutCategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workout_categories')->delete();
        DB::table('workout_categories')->insert([
            'id' => 1,
            'name' => 'Recommended',
            'user_id' => 1
        ],
        [
            'id' => 2,
            'name' => 'Chest',
            'user_id' => 1
        ],
        [
            'id' => 3,
            'name' => 'Stomach',
            'user_id' => 1
        ],
        [
            'id' => 4,
            'name' => 'Legs',
            'user_id' => 1
        ]);
    }
}
