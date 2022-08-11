<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ChallengesExercisesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('challenges_exercises')->delete();
        
        \DB::table('challenges_exercises')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Run',
                'desc' => 'Run as fast as you can ',
                'img_path' => '1/RwZ5AmlIzk7lZ2Cve5j0sO3f2mZ8N1running.gif',
                'ca' => '0.032',
                'created_at' => '2022-07-30 02:00:45',
                'updated_at' => '2022-07-30 02:00:45',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Walking',
                'desc' => 'Move by advancing the feet alternately so that there is always one foot on the ground in bipedal locomotion and two or more feet on the ground in quadrupedal locomotion.',
                'img_path' => '2/7dEg7YhJPDJwlHpoNK9lvg2y0W81wbWalk.gif',
                'ca' => '0.028',
                'created_at' => '2022-08-10 11:45:59',
                'updated_at' => '2022-08-10 11:45:59',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Push UP',
                'desc' => 'Your arms should be shoulder height and shoulder-width apart. Inhale as you bend your elbows and slowly move your upper body toward the wall while keeping your feet flat on the ground. Hold this position for a second or two. Exhale and use your arms to push your body slowly back to your starting position.',
                'img_path' => '3/jIJHyLeET69iJJs43Yh248kqXbAxr9PushUP.gif',
                'ca' => '0.45',
                'created_at' => '2022-08-10 11:49:12',
                'updated_at' => '2022-08-10 11:49:12',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Squat',
                'desc' => 'A squat is a strength exercise in which the trainee lowers their hips from a standing position and then stands back up. During the descent of a squat, the hip and knee joints flex while the ankle joint dorsiflexes; conversely the hip and knee joints extend and the ankle joint plantarflexes when standing up.',
                'img_path' => '4/VuHIzZV9gHMoqjEY4MiZOytRJoeEYLsquats.gif',
                'ca' => '0.32',
                'created_at' => '2022-08-10 12:06:25',
                'updated_at' => '2022-08-10 12:06:25',
            ),
            4 => 
            array (
                'id' => 5,
            'name' => 'Triceps Dip (Chair)',
                'desc' => 'Press into your palms to lift your body and slide forward just far enough that your behind clears the edge of the chair.
Lower yourself until your elbows are bent between 45 and 90 degrees. Control the movement throughout the range of motion.
Push yourself back up slowly until your arms are almost straight and repeat.',
                'img_path' => '5/TuXuILnIYZLYobokDmhSbqCZihkHlJTriceps.gif',
                'ca' => '0.3',
                'created_at' => '2022-08-10 13:14:01',
                'updated_at' => '2022-08-10 13:14:01',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Legs Raises',
                'desc' => 'Lie on your back, legs straight and together. 2. Keep your legs straight and lift them all the way up to the ceiling until your butt comes off the floor. 3. Slowly lower your legs back down till they\'re just above the floor.',
                'img_path' => '6/TwhVVjPebkl2CnjGLM3TEhEKSIeeqklegs_Raises2.gif',
                'ca' => '0.4',
                'created_at' => '2022-08-10 14:10:43',
                'updated_at' => '2022-08-10 14:10:43',
            ),
        ));
        
        
    }
}