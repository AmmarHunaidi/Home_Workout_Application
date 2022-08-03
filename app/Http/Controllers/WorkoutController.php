<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Models\Excersise;
use App\Models\FavoriteWorkout;
use App\Models\User;
use App\Models\WorkoutReview;
use App\Traits\GeneralTrait;
use Error;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WorkoutController extends Controller
{
    use GeneralTrait;
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
            'difficulty' => 'required|integer:1,2,3',
            'workout_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096|nullable'
        ]);
        $predicted_calories_burn = 0;
        $fields['user_id'] = $request->user()->id;
        $workout = Workout::create($fields);
        if($request->hasFile('workout_image'))
        {
            $original_path = 'public/images/workouts/' . $workout->id;
            $storage = Storage::makeDirectory($original_path);
            $image = $request->file('workout_image');
            $randomString = Str::random(30);
            $image_name = $randomString . $image->getClientOriginalName();
            $path = $image->storeAs($original_path,$image_name);
            $workout->workout_image_url = $image_name;
            $workout->update();
        }
        return $this->success("Workout Created Successfully. Awaiting Approval",$workout , 201);
        }
        else
        {
            return $this->fail(_("Not a Coach!"),400);
        }
    }

    public function edit(Request $request)
    {
        if($request->user()->role_id == 2)
        {
            $fields = $request->validate([
                'workout_id' => 'integer|required',
                'name' => 'string',
                'equipment' => 'string',
                'difficulty' => 'integer',
                'workout_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096|nullable'
            ]);
            $workout = Workout::find($fields['workout_id']);
            if (!Gate::allows('Edit_Workout', $workout)) {
                abort(403);
            }
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

    public function favorite(Request $request)
    {
        $fields = Validator::make($request->only('workout_id'), [
            'workout_id' => 'required|integer'
        ]);
        if($fields->fails())
        {
            return $this->fail($fields->errors()->first(),400);
        }
        $fields = $fields->safe()->all();
        $fields['user_id'] = $request->user()->id;
        $favorite = FavoriteWorkout::create($fields);
        return $this->success("Added to favorites!" , $favorite , 200);
    }

    public function unfavorite(Request $request)
    {
        $fields = Validator::make($request->only('workout_id'), [
            'workout_id' => 'required|integer'
        ]);
        if($fields->fails())
        {
            return $this->fail($fields->errors()->first(),400);
        }
        $fields = $fields->safe()->all();
        $favorite = FavoriteWorkout::where('user_id', $request->user()->id)
                                   ->where('workout_id' ,$fields['workout_id']);
        $favorite->delete();
        return $this->success("Deleted from favorites!" , $favorite , 200);
    }

    public function favorites()
    {
        $user_id = Auth::id();
        $favorites = User::find($user_id)->favorites->workout;
        return $this->success("Success" , $favorites , 200);
    }

    public function review(Request $request)
    {
        $fields = Validator::make($request->only('workout_id','description','stars') , [
            'workout_id' => 'required|integer',
            'description' => 'required|string',
            'stars' => 'required|integer:1,2,3,4,5'
        ]);
        if($fields->fails())
        {
            return $this->fail($fields->errors()->first(),400);
        }
        $fields = $fields->safe()->all();
        $fields['user_id'] = $request->user()->id;
        $review = WorkoutReview::create($fields);
        $workout = Workout::find($fields['workout_id']);
        $review_count = $workout->review->count();
        $review_rating = (float)(($workout->review_count * ($review_count-1)) + $fields['stars'] )/($review_count);
        $workout->review_count = $review_rating;
        $workout->update();
        return $this->success("Done" , $workout , 200);
    }
}
