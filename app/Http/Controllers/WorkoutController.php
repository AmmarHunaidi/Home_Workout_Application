<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Models\Excersise;
use Error;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkoutController extends Controller
{
    function index()
    {
        return Workout::all();
    }

    public function show(Request $request)
    {
        $fields = $request->validate([
            'id' => 'required|integer'
        ]);
        return Workout::find($fields['id']);
    }

    public function create(Request $request)
    {
        if($request->user()->role_id == 2)
        {
        $fields = $request->validate([
            'name'=>'required|string|min:5|max:50',
            'categorie_id' => 'required|integer',
            'equipment' => 'required|string:required,not required, recommended',
            'workout_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096|nullable'
        ]);
        $predicted_calories_burn = 0;
        $fields['user_id'] = $request->user()->id;
        $workout = Workout::create($fields);
        if($request->hasFile('workout_image'))
        {
            error_log("Hello");
            $original_path = 'public/images/workouts/' . $workout->id;
            $storage = Storage::makeDirectory($original_path);
            $image = $request->file('workout_image');
            $randomString = Str::random(30);
            $image_name = $randomString . $image->getClientOriginalName();
            $path = $image->storeAs($original_path,$image_name);
            $workout->workout_image_url = $image_name;
            $workout->update();
        }
        return response($workout);
        }
        else
        {
            return response('Not a Coach!!');
        }
    }

    public function edit(Request $request)
    {
        if($request->user()->role_id == 2)
        {
            $fields = $request->validate([
                'workout_id' => 'integer|required',
                'name' => 'string',
                'workout_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096|nullable'
            ]);
            $workout = Workout::find($fields['workout_id']);
            error_log("Hello");
            if (! Gate::allows('Edit_Workout', $workout)) {
                abort(403);
            }
            error_log("Hello");
            if($workout->coach->id == $request->user()->id)
            {
                if($fields['name'] != null)
                    $workout->name = $fields['name'];
                if($fields['workout_image'] != null)
                {
                    $original_path = 'public/images/workouts' . $workout->id;
                    if(!file_exists($original_path))
                    {
                        Storage::makeDirectory($original_path);
                    }
                    if($workout->workout_image_url != 'default')
                    {
                        $old_image = $workout->workout_image_url;
                        Storage::delete($original_path . $old_image);
                        $image = $request->file('workout_image');
                        $randomString = Str::random(30);
                        $image_name = $randomString . $image->getClientOriginalName();
                        $path = $image->storeAs($original_path,$image_name);
                        $workout->workout_image_url = $image_name;
                    }
                    else
                    {
                        $image = $request->file('workout_image');
                        $randomString = Str::random(30);
                        $image_name = $randomString . $image->getClientOriginalName();
                        $path = $image->storeAs($original_path,$image_name);
                        $workout->workout_image_url = $image_name;
                    }
                    $workout->update();
                }
                return response($workout);
            }
            return response('fail');
        }
    }

    public function destroy(Request $request)
    {
        if($request->user()->role_id == 2)
        {
            $fields = $request->validate([
                'workout_id' => 'required|integer'
            ]);
            $workout = Workout::find($fields['workout_id']);
            if($workout->coach->id == $request->user()->id)
            {
                $workout->delete();
                return response('Success');
            }
            return response('Fail');
        }
    }
}
