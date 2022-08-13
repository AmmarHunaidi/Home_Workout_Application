<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ExcersisesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('excersises')->delete();
        
        \DB::table('excersises')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Jump Rope',
                'description' => 'Jump Rope',
                'burn_calories' => 200,
                'excersise_media_url' => '1/nYfoIEziMHUQRxZOX7HRN4y6388kUVimage_picker4438407266250710065.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:29:00',
                'updated_at' => '2022-08-12 13:19:05',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'High Knees',
                'description' => 'Good Cardio Exercise',
                'burn_calories' => 35,
                'excersise_media_url' => '2/JlVuTV3EdrzeRqCHvVpVq4IIPgGvycimage_picker392825790176701106.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:31:00',
                'updated_at' => '2022-08-12 03:31:35',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Burpees',
                'description' => 'Great Cardio Excercise',
                'burn_calories' => 50,
                'excersise_media_url' => '3/at1NSznBZ7lfhfmhpROmWnlPVdnfxvimage_picker5594076019084910172.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:33:00',
                'updated_at' => '2022-08-12 03:33:16',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Alternating Superman',
                'description' => 'Very Good Excercise.',
                'burn_calories' => 45,
                'excersise_media_url' => '4/5TcOsFHfPjVHleJU7Wny77WNqBY9mwimage_picker6533043465434985057.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:34:00',
                'updated_at' => '2022-08-12 03:34:48',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Inchworm',
                'description' => 'Amazing Excercise',
                'burn_calories' => 50,
                'excersise_media_url' => '5/vM64CmJ0c3pCVHGfH968nodLHcTTrQimage_picker754297646629500589.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:36:00',
                'updated_at' => '2022-08-12 03:36:03',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Tricep Dip',
                'description' => 'Great Tricep Excercise',
                'burn_calories' => 35,
                'excersise_media_url' => '6/urqH0UtJRCQqGI2Bc655xrGdzoEjpmimage_picker6485379217634726081.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:38:00',
                'updated_at' => '2022-08-12 03:38:00',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Up & Down Plank',
                'description' => 'Great Excercise',
                'burn_calories' => 50,
                'excersise_media_url' => '7/lYqOMx6LbYO9W2RjGRI1Vd3LpGhm7qimage_picker8292362399712466584.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 06:39:00',
                'updated_at' => '2022-08-12 03:39:47',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'test',
                'description' => 'hsha',
                'burn_calories' => 464,
                'excersise_media_url' => '8/gcCQiExi9PSxhg8U1DG2LYCtrd64Qnimage_picker4307231235491451282.gif',
                'user_id' => 8,
                'created_at' => '2022-08-12 16:06:00',
                'updated_at' => '2022-08-12 13:06:47',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => '180 Jump Squat',
                'description' => 'Great Excercise',
                'burn_calories' => 35,
                'excersise_media_url' => '9/EA04GAJOqIo5PDTk5wx5KIYyapA6mDef8407de52e17a5cce5edf5506908bb1.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:20:00',
                'updated_at' => '2022-08-13 07:20:20',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Step Up With Knee Raise',
                'description' => 'Good Excercise',
                'burn_calories' => 40,
                'excersise_media_url' => '10/MDHeQhDruCLAurm2R41QYTo1zS7hJ452d8efd6027fd752bde4e2f4dc5fe069.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:21:00',
                'updated_at' => '2022-08-13 07:21:08',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Lunge Punch',
                'description' => 'Good Excercise',
                'burn_calories' => 50,
                'excersise_media_url' => '11/e08hsXEKbn5W1rOnB9acKBoLkPvj5p49e247d529e46aee55ffb5b3b28eacac.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:22:00',
                'updated_at' => '2022-08-13 07:22:31',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Squat Curl',
                'description' => 'Good Excercise',
                'burn_calories' => 25,
                'excersise_media_url' => '12/Bq7FOegYlGEbhQRrvHLvcJjJe3g6Qf7ed6d7c74d9befc91815a3afe73d6ec7.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:22:00',
                'updated_at' => '2022-08-13 07:22:59',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Split Squat Press',
                'description' => 'Good Excercise',
                'burn_calories' => 50,
                'excersise_media_url' => '13/N8WaKUFYzJaqsgtHKIWkKg8vTAmZjtd0541bb653319a56a5d876e3eabb319a.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:23:00',
                'updated_at' => '2022-08-13 07:23:37',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Squat With Overhead Tricep Extension',
                'description' => 'Good Excercise',
                'burn_calories' => 30,
                'excersise_media_url' => '14/zt1ZbruKTRGJMGF9qmkf23CADAANcVa03c462fb38362de275a03eb6ecec075.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:24:00',
                'updated_at' => '2022-08-13 07:24:38',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Big Arms Circles',
                'description' => 'Good Excercise',
                'burn_calories' => 15,
                'excersise_media_url' => '15/X6FmF2M2wNP35A26iojIo8dt7odgWS8e6aef4e247dcfa16ca4416122b9f5d0.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:25:00',
                'updated_at' => '2022-08-13 07:25:00',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Scissor Kicks',
                'description' => 'Good Excercise',
                'burn_calories' => 25,
                'excersise_media_url' => '16/HZ9EtITss8wiWhSZBVgnZ9Vij0EmHR8e8345fa2a85dcc47b05200614903bf0.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:25:00',
                'updated_at' => '2022-08-13 07:25:41',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Plank Hip Dips',
                'description' => 'Good Excercise',
                'burn_calories' => 45,
                'excersise_media_url' => '17/UL9m4nHJce8j3XKyJrWqLrEyKPDCIB82acf346b871a0ce8f97c0de7af6fcae.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:26:00',
                'updated_at' => '2022-08-13 07:26:03',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Squat Side Kick',
                'description' => 'Good Excercise',
                'burn_calories' => 45,
                'excersise_media_url' => '18/xqB47SGwa7tj9hHtB0Vv29OTij6JiB66e9163fa904927345693bc36f885a3f.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:26:00',
                'updated_at' => '2022-08-13 07:26:21',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Side Plank Hip Lifts',
                'description' => 'Good Excercise',
                'burn_calories' => 30,
                'excersise_media_url' => '19/bVGQZtbXUFlwIIVFOaALjLODlc6Zduaf7f569f481e3458cebb80ab625dfbd2.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:26:00',
                'updated_at' => '2022-08-13 07:26:51',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Dumbbell Lateral Raise',
                'description' => 'Good Excercise',
                'burn_calories' => 45,
                'excersise_media_url' => '20/OC6w5tcZEb2i6XM9IpTanuWZlebW0Caff1b7bf1dea681c9f494cbeb95b1234.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:27:00',
                'updated_at' => '2022-08-13 07:27:20',
            ),
            20 => 
            array (
                'id' => 22,
                'name' => 'Run In Place',
                'description' => 'Good Excercise',
                'burn_calories' => 30,
                'excersise_media_url' => '22/hU0DSnWpwmb01yHvuV5dUPFvTkiQaNb38da20664cbd52a10f93c349a8542b2.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 07:29:00',
                'updated_at' => '2022-08-13 07:29:47',
            ),
            21 => 
            array (
                'id' => 23,
                'name' => 'Bicycle Crunches',
                'description' => 'Good Excercise',
                'burn_calories' => 30,
                'excersise_media_url' => '23/MLjJWZdB2hPBgz4oTOidDzYtHdhJUJ1b036f9aa0fdeaecbac70152faa9d6ca.gif',
                'user_id' => 8,
                'created_at' => '2022-08-13 08:09:00',
                'updated_at' => '2022-08-13 08:09:04',
            ),
        ));
        
        
    }
}