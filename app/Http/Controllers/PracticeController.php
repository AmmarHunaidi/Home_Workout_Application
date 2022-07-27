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
use Illuminate\Support\Facades\Validator;

class PracticeController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fields = Validator::make($request->only('workout_id','excersises_played'),[
            'workout_id' => 'required|integer',
            'excersises_played' => 'required|array',
            'time_practiced' => 'required|integer'
        ]);
        if(count($fields['excersises_played']) == 0)
        {
            return $this->fail(_('Not Played any excersise.'),401);
        }
        $practice_calories = 0;
        foreach($fields['excersises_played'] as $excersise_id)
        {
            $excersise = Excersise::find($excersise_id);
            $practice_calories += $excersise->burn_calories;
        }
        $workout = Workout::find($fields['workout_id']);
        $excersises_count = $workout->excersise->count();

        $data = [
            'workout_id' => $workout->id,
            'summary_calories' => $practice_calories,
            'summary_time' => $fields['time_practiced'],
            'user_id' => $request->user()->primarykey
        ];
        $practice = Practice::create($data);
        return $this->success('Practice Done!',$practice,201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePracticeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePracticeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function show(Practice $practice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function edit(Practice $practice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePracticeRequest  $request
     * @param  \App\Models\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePracticeRequest $request, Practice $practice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Practice $practice)
    {
        //
    }
}
