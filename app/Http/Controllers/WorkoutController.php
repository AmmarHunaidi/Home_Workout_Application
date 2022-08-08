<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Models\Excersise;
use App\Models\FavoriteDiet;
use App\Models\FavoriteWorkout;
use App\Models\User;
use App\Models\WorkoutExcersises;
use App\Models\WorkoutReview;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
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
        //blocked users and folloed users
        $workouts = Workout::all(['id','name','predicted_burnt_calories','length','excersise_count','equipment','difficulty' , 'user_id']);
        foreach($workouts as $workout)
        {
            $workout['workout_image_url'] = 'storage/image/workout/' . $workout['workout_image_url'];
            $workout['user_id'] = User::where('id', $workout['user_id'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
        }
        return $this->success("Success", $workouts, 200);
    }

    public function show($id)
    {
        //blocked user
        $workout = Workout::find($id);
        $workout_excersises = $workout->workout_excersise->orderBy('position');
        $excersise_list = [];
        foreach ($workout_excersises as $workout_excersise) {
            $o = 0;
            $excersise = Excersise::where('id', $workout_excersise->excersise_id)->get(['id', 'name', 'excersise_media']);
            if ($workout_excersise->contains('count')) {
                $o = 1;
            }
            if ($o == 0) {
                $excersise_list[] = [
                    "position" => $workout_excersise->position,
                    "length" => $workout_excersise->length,
                    "excersise" => $excersise
                ];
            } else {
                $excersise_list[] = [
                    "position" => $workout_excersise->position,
                    "count" => $workout_excersise->count,
                    "excersise" => $excersise
                ];
            }
        }
        $workout['excersises'] = $excersise_list;
        $workout['user'] = User::find(Auth::id())->get(['id', 'f_name', 'l_name', 'prof_img_url']);
        return $this->success("Workout", $workout, 200);
    }

    public function create(Request $request)
    {
        //return $this->success("Success", $request , 200);
        $user = User::find(Auth::id());
        if (in_array($user->role_id, [2, 4, 5])) {
            $fields = Validator::make($request->only('name', 'categorie_id', 'equipment', 'difficulty', 'workout_image', 'excersises'), [
                'name' => 'required|string',
                'categorie_id' => 'required|integer',
                'equipment' => 'required|string|in:Required,Not Required,Recommended',
                'difficulty' => 'required|integer|in:1,2,3',
                'workout_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096',
                'excersises' => 'required|string'
            ]);
            if ($fields->fails()) {
                return $this->fail($fields->errors()->first(), 400);
            }
            $fields = $fields->safe()->all();
            $fields['name'] = strtolower($fields['name']);
            $predicted_calories_burn = 0;
            if (Excersise::where('name', $fields['name'])->exists()) {
                return $this->fail('Name already taken!', 400);
            }
            $fields['user_id'] = $request->user()->id;
            $workout = Workout::create($fields);
            if ($request->hasFile('workout_image')) {
                $destination_path = 'public/images/workout';
                $image = $request->file('workout_image');
                $randomString = Str::random(30);
                $image_name = $workout->id . '/' . $randomString . $image->getClientOriginalName();
                $path = $image->storeAs($destination_path, $image_name);
                $workout->workout_image_url = $image_name;
            }
            $excersise_list = json_decode($fields['excersises']);
            if (count($excersise_list) > 30) {
                return $this->fail("Too many excersises added!", 400);
            }
            $i = 0;
            $position = 0;
            foreach ($excersise_list as $excersise) {
                $excersise = (array)$excersise;
                $time = 0;
                $position = ++$i;
                if($excersise['isTime'] == true) $time = 1;
                if ($time == 0) {
                    WorkoutExcersises::create([
                        'user_id' => Auth::id(),
                        'excersise_id' => $excersise['id'],
                        'workout_id' => $workout->id,
                        'count' => $excersise['value'],
                        'position' => $position
                    ]);
                    $workout->length += $excersise['value'];
                    $calories = Excersise::find($excersise['id'])->burn_calories;
                    $predicted_calories_burn += $calories;
                } else {
                    WorkoutExcersises::create([
                        'user_id' => Auth::id(),
                        'excersise_id' => $excersise['id'],
                        'workout_id' => $workout->id,
                        'length' => $excersise['count'],
                        'position' => $position
                    ]);
                    $workout->length += $excersise['value'];
                    $calories = Excersise::find($excersise['id'])->burn_calories;
                    $predicted_calories_burn += $calories;
                }
            }
            $workout->predicted_burnt_calories = $predicted_calories_burn;
            $workout->created_at = (string)Carbon::parse($workout->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
            $workout->update();
            return $this->success("Workout Created Successfully", $workout, 201);
        } else {
            return $this->fail("Permission Denied!", 400);
        }
    }

    public function edit(Request $request, $id)
    {
        $user = User::find(Auth::id());
        if (in_array($user->role_id, [2, 4, 5])) {
            $fields = Validator::make($request->only('name', 'categorie_id', 'equipment', 'difficulty', 'workout_image', 'excersises'), [
                'name' => 'required|string',
                'categorie_id' => 'required|integer',
                'equipment' => 'required|string|in:Required,Not Required,Recommended',
                'difficulty' => 'required|integer|in:1,2,3',
                'workout_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096',
                'excersises' => 'required|string'
            ]);
            if ($fields->fails()) {
                return $this->fail($fields->errors()->first(), 400);
            }
            $fields = $fields->safe()->all();
            $fields['name'] = strtolower($fields['name']);
            $workout = Workout::find($id);
            if (in_array($fields['name'], Excersise::where('name', '!=', $fields['name'])->get('name')->toArray())) {
                return $this->fail("Name Already Exists", 400);
            }
            if ($workout->name != $fields['name']) $workout->name = $fields['name'];
            if ($workout->categorie_id != $fields['categorie_id']) $workout->categorie_id = $fields['categorie_id'];
            if ($workout->equipment != $fields['equipment']) $workout->equipment = $fields['equipment'];
            if ($workout->difficulty != $fields['difficulty']) $workout->difficulty = $fields['difficulty'];
            if ($request->hasFile('workout_image')) {
                $original_path = 'public/images/workout';
                if (Storage::exists($original_path . '/' . $workout->workout_image_url)) {
                    Storage::delete($original_path . '/' . $workout->workout_image_url);
                }
                $image = $request->file('workout_image');
                $randomString = Str::random(30);
                $image_name = $workout->id . '/' . $randomString . $image->getClientOriginalName();
                $path = $image->storeAs($original_path, $image_name);
                $workout->workout_image_url = $image_name;
            }
            $predicted_calories_burn = 0;
            $workout->length = 0;
            $workout_predicted_burnt_calories = 0;
            $excersise_list = json_decode($fields['excersises']);
            if (count($excersise_list) > 30) {
                return $this->fail("Too many excersises added!", 400);
            }
            $workout->workout_excersise->each(function ($data) {
                $data->delete();
            });
            $i = 0;
            $position = 0;
            foreach ($excersise_list as $excersise) {
                $excersise = (array)$excersise;
                $time = 0;
                $position = ++$i;
                if($excersise['isTime'] == true) $time = 1;
                if ($time == 0) {
                    WorkoutExcersises::create([
                        'user_id' => Auth::id(),
                        'excersise_id' => $excersise['id'],
                        'workout_id' => $workout->id,
                        'count' => $excersise['value'],
                        'position' => $position
                    ]);
                    $workout->length += $excersise['value'];
                    $calories = Excersise::find($excersise['id'])->burn_calories;
                    $predicted_calories_burn += $calories;
                } else {
                    WorkoutExcersises::create([
                        'user_id' => Auth::id(),
                        'excersise_id' => $excersise['id'],
                        'workout_id' => $workout->id,
                        'length' => $excersise['count'],
                        'position' => $position
                    ]);
                    $workout->length += $excersise['value'];
                    $calories = Excersise::find($excersise['id'])->burn_calories;
                    $predicted_calories_burn += $calories;
                }
            }
            $workout->predicted_burnt_calories = $predicted_calories_burn;
            $workout->updated_at = (string)Carbon::parse($workout->updated_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
            $workout->update();
            return $this->success("Success", [], 200);
        }
        return $this->fail("Permission Denied", 400);
    }

    public function destroy($id)
    {
        $user = User::find(Auth::id());
        $workout = Workout::find($id);
        if ($workout->coach->id == Auth::id() || in_Array($user->role_id, [4, 5])) {
            $workout_excersises = $workout->workout_excersise->each(function ($data) {
                $data->delete();
            });
            $workout->delete();
            return $this->success("Success", [], 200);
        }
        return $this->fail("Permission Denied", 400);
    }

    public function favorite($id)
    {
        $favorite = FavoriteWorkout::where(['user_id' => Auth::id() , 'workout_id' => $id])->exists();
        if ($favorite) {
            $favorite = FavoriteWorkout::where(['user_id' => Auth::id() , 'workout_id' => $id])->delete();
            return $this->success("Deleted form favorites", [], 200);
        } else {
            $favorite = FavoriteWorkout::create([
                'user_id' => Auth::id(),
                'workout_id' => $id
            ]);
            return $this->success("Added to favorites!", $favorite, 200);
        }
    }

    public function favorites()
    {
        $user_id = Auth::id();
        $favorites = User::find($user_id)->favoriteworkouts;
        $result = [];
        foreach($favorites as $favorite)
        {
            $workout = Workout::find($favorite->workout_id)->only(['id','name','user_id','created_at']);
            $workout['user_id'] = User::find($workout['user_id'])->only(['id','f_name','l_name','prof_img_url']);
            $result[] = $workout;
        }
        return $this->success("Favorites" , $result , 200);
    }

    public function review(Request $request , $id)
    {
        $fields = Validator::make($request->only('description','stars') , [
            'description' => 'required|string',
            'stars' => 'required|integer|between:1,5'
        ]);
        if($fields->fails())
        {
            return $this->fail($fields->errors()->first(),400);
        }
        $fields = $fields->safe()->all();
        if(WorkoutReview::where(['workout_id' => $id , 'user_id' => Auth::id()])->exists())
        {
            return $this->fail("You can't add more than one review!!" , 400);
        }
        $fields['user_id'] = $request->user()->id;
        $fields['workout_id'] = $id;
        $review = WorkoutReview::create($fields);
        $workout = Workout::find($id);
        $review_rate = $workout->review_count;
        $review_count = $workout->reviews->count();
        $review_rating = (double) ((($review_count - 1) * $review_rate) + $fields['stars']) / ($review_count);
        $workout->review_count = $review_rating;
        $workout->update();
        return $this->success("Done" , $workout , 200);
    }

    public function reviews($id)
    {

        $reviews = Workout::find($id)->reviews->each(function ($data) {
            $data['user_id'] = User::where('id' , $data['user_id'])->get(['id' , 'f_name' , 'l_name' , 'prof_img_url']);
            return $data;
        });
        return $this->success("Success" , $reviews , 200);
    }

    public function user_workouts($id)
    {
        $result = [];
        $workouts = Workout::where('user_id' , $id)->get(['id','name','user_id','created_at']);
        foreach($workouts as $workout)
        {
            $workout['user_id'] = User::find($workout->user_id)->only(['id','f_name','l_name','prof_img_url']);
            $workout['saved'] = false;
            if(FavoriteWorkout::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
            $result[] = $workout;
        }
        return $this->success("Success" , $result , 200);
    }

    public function my_workouts()
    {
        $result = [];
        $workouts = Workout::where('user_id' , Auth::id())->get(['id','name','user_id','created_at']);
        foreach($workouts as $workout)
        {
            $workout['user_id'] = User::find($workout->user_id)->only(['id','f_name','l_name','prof_img_url']);
            $workout['saved'] = false;
            if(FavoriteWorkout::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
            $result[] = $workout;
        }
        return $this->success("Success" , $result , 200);
    }


}
