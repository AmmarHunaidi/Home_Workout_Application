<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use App\Http\Requests\StorePracticeRequest;
use App\Http\Requests\UpdatePracticeRequest;
use App\Models\Excersise;
use App\Models\Workout;
use App\Traits\GeneralTrait;
use Composer\Pcre\Preg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PracticeController extends Controller
{
    use GeneralTrait;
    public function initiate($id)
    {
        $practice = Practice::create(['user_id' => Auth::id() , 'workout_id' => $id]);
        return $this->success("Start Practicing!",$practice->only(['id']),200);
    }

    public function practice(Request $request,$id)
    {
        $fields = Validator::make($request->only('excersise_id','practice_id','length'),[
            'excersise_id' => 'required|integer',
            'length' => 'requried|integer',
        ]);
        if($fields->fails())
        {
            return $this->fail($fields->errors()->first(),401);
        }
        $fields = $fields->safe()->all();
        $practice = Practice::find($id);
        $excersise = Excersise::find($fields['excersise_id']);
        $practice->summary_calories += $excersise->burn_calories;
        $practice->summary_time += $fields['length'];
        $excersises_played = json_decode($practice->excersises_played);
        $excersises_played[] = $fields['excersise_id'];
        $practice->excersises_played = json_encode($excersises_played);
        $practice->update();
        return $this->success("Next Excersise!" , $practice , 200);
    }

    public function summary(Request $request)
    {
        $fields = Validator::make($request->only('practice_id'),[
            'practice_id' => 'required|integer'
        ]);
        if($fields->fails())
        {
            return $this->fail($fields->errors()->first(),401);
        }
        $fields = $fields->safe()->all();
        $practice = Practice::find($fields['practice_id']);
        $workout_excersises = Workout::where('id' , $practice->workout_id)->first();
        $workout_excersises = $workout_excersises->excersise_count;
        if(count(json_decode($practice->excersises_played)) == 0)
        {
            $practice->query()
                     ->where('id',$fields['practice_id'])
                     ->delete();
            return $this->success("Not Practiced!", [] , 200);
        }
        else if(count(json_decode($practice->excersises_played)) < 0.2 * $workout_excersises)
        {
            $practice->query()
                     ->where('id',$fields['practice_id'])
                     ->delete();
            return $this->success("Practice not trained suffeciently less than 20 percent of excersise practiced." , [] , 200);
        }
        else
        {
            return $this->success('Workout Summary' , $practice , 200);
        }
    }
}
