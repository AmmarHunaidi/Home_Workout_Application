<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use App\Http\Requests\StorePracticeRequest;
use App\Http\Requests\UpdatePracticeRequest;
use App\Models\Excersise;
use App\Models\Workout;
use App\Traits\GeneralTrait;
use Composer\Pcre\Preg;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PracticeController extends Controller
{
    use GeneralTrait;
    // public function initiate($id)
    // {
    //     try {
    //         $practice = Practice::create(['user_id' => Auth::id(), 'workout_id' => $id]);
    //         return $this->success("Start Practicing!", $practice->only(['id']), 200);
    //     } catch (Exception $exception) {
    //         return $this->fail($exception->getMessage(), 500);
    //     }
    // }

    // public function practice(Request $request, $id)
    // {
    //     try {
    //         $fields = Validator::make($request->only('excersise_id', 'practice_id', 'length'), [
    //             'excersise_id' => 'required|integer',
    //             'length' => 'requried|integer',
    //         ]);
    //         if ($fields->fails()) {
    //             return $this->fail($fields->errors()->first(), 401);
    //         }
    //         $fields = $fields->safe()->all();
    //         $practice = Practice::find($id);
    //         $excersise = Excersise::find($fields['excersise_id']);
    //         $practice->summary_calories += $excersise->burn_calories;
    //         $practice->summary_time += $fields['length'];
    //         $excersises_played = json_decode($practice->excersises_played);
    //         $excersises_played[] = $fields['excersise_id'];
    //         $practice->excersises_played = json_encode($excersises_played);
    //         $practice->update();
    //         return $this->success("Next Excersise!", $practice, 200);
    //     } catch (Exception $exception) {
    //         return $this->fail($exception->getMessage(), 500);
    //     }
    // }

    public function summary(Request $request)
    {
        try {
            $fields= Validator::make($request->only('totalTime' , 'excersises_played' , 'workout_id') , [
                'totalTime' => 'required|integer',
                'excersises_played' => 'required|string',
                'workout_id' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $fields = $fields->safe()->all();
            //return response("Hi");
            $excersises = json_decode($fields['excersises_played']);

            $excersises_played = count($excersises);
            $workout = Workout::find($fields['workout_id']);
            if($excersises_played < 0.2 * $workout->workout_excersise()->count())
            {
                return $this->success("Practice not trained suffeciently less than 20 percent of excersise practiced.", ['summary_calories' => "",
                'summary_time' =>  "",
                'excersises_played' =>  ""] , 200);
            }
            $calorie_summary = 0;
            foreach($excersises as $excersise)
            {
                $excer = Excersise::find($excersise);
                $calorie_summary += $excer->burn_calories;
            }
            $practice = Practice::create([
                'user_id' => Auth::id(),
                'summary_calories' => (string)$calorie_summary,
                'summary_time' =>  (string)$fields['totalTime'],
                'excersises_played' =>  (string)$excersises_played,
                'workout_id' => $fields['workout_id']
            ]);
            $result = $practice->only(['summary_time','summary_calories','excersises_played']);
            return $this->success("Well Done!" , $practice->only(['summary_time','summary_calories','excersises_played']) , 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
