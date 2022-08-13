<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FoodTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('food')->delete();
        
        \DB::table('food')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Boiled Eggs',
                'calories' => 125,
                'description' => 'Healthy Protein Packed Eggs',
                'food_image_url' => '1/usBHQtq8kHw3lRzHw7ykNZVtSDo2iFimage.jpeg',
                'user_id' => 8,
                'approval' => 1,
                'created_at' => '2022-08-12 03:55:57',
                'updated_at' => '2022-08-12 03:55:57',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Whole Wheat Toast.',
                'calories' => 150,
                'description' => 'Great Bread for calorie based diets.',
                'food_image_url' => '2/ui24i9PzOfZoNF94QwHgJXApF3vJ00image_picker1370185751935497259.jpg',
                'user_id' => 8,
                'approval' => 1,
                'created_at' => '2022-08-12 04:03:58',
                'updated_at' => '2022-08-12 04:09:14',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Grilled Chicken Breast',
                'calories' => 250,
                'description' => 'Best Chicken Recipe for Calorie based diets.',
                'food_image_url' => '3/omZFA6I6fRKnu0nV6GtMLKYUJNa04iimage_picker8536783474461180546.jpg',
                'user_id' => 8,
                'approval' => 1,
                'created_at' => '2022-08-12 04:06:35',
                'updated_at' => '2022-08-12 04:06:35',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'Rainbow Salad',
                'calories' => 150,
                'description' => 'Great salad full of vegetables and color.',
                'food_image_url' => '5/3kU4oxuzXxbpAJ0uOu1CU1GJ2xDuBrimage_picker4338611499472698724.jpg',
                'user_id' => 8,
                'approval' => 1,
                'created_at' => '2022-08-12 04:11:48',
                'updated_at' => '2022-08-12 04:11:48',
            ),
            4 => 
            array (
                'id' => 6,
                'name' => 'Tuna Salad',
                'calories' => 200,
                'description' => 'Great Dish to keep you energetic throughout the day',
                'food_image_url' => '6/iKmm7XLF14cFiPtZma76eQRJiPqsIYimage_picker157186687770229215.jpg',
                'user_id' => 8,
                'approval' => 1,
                'created_at' => '2022-08-12 04:16:21',
                'updated_at' => '2022-08-12 04:16:21',
            ),
            5 => 
            array (
                'id' => 7,
                'name' => 'Green Smoothie',
                'calories' => 175,
                'description' => 'Full of vegetables and fruits.',
                'food_image_url' => '7/auPGny3QERJdGi8s0IfDKMD0tYCSO6image_picker2596328198930314517.jpg',
                'user_id' => 8,
                'approval' => 1,
                'created_at' => '2022-08-12 04:20:45',
                'updated_at' => '2022-08-12 04:20:45',
            ),
        ));
        
        
    }
}