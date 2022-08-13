<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Block;
use App\Models\Excersise;
use App\Models\FavoriteWorkout;
use App\Models\Follow;
use App\Models\Practice;
use App\Models\PracticeWorkout;
use App\Models\User;
use App\Models\WorkoutCategorie;
use App\Models\WorkoutExcersises;
use App\Models\WorkoutReview;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use DateTime;
use Error;
use Exception;
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
        try {
            $workouts = Workout::get(['id', 'name', 'description', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at as created_at','review_count']);
            // return response($workouts);
            $results = [];
            foreach ($workouts as $workout) {
                $result['workout_image_url'] = 'storage/image/workout/' . $workout['workout_image_url'];
                $result['review_count'] = round($workout['review_count'], 1);
                $result['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                str_starts_with($workout['user']['prof_img_url'],'https') ? : $result['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                $result['saved'] = false;
                if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $result['saved'] = true;
                // $workout['is_reviewed'] = false;
                WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists() == true ? $result['is_reviewed'] = true : $result['is_reviewed'] = false;

                // $p = strval(Carbon::parse($workout['created_at'])->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A'));
                // unset($workout['created_at']);
                $result['created_at'] = strval(Carbon::parse($workout['created_at'])->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A'));
                // return response($workout['created_at']);
                $results[] = $result;
            }
            //$workouts = (array) $workouts;
            return $this->success(__("messages.All Workouts Returned Successfully"), collect($results), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $workout = Workout::find($id);
            $workout_excersises = $workout->workout_excersise;
            $excersise_list = [];
            foreach ($workout_excersises as $workout_excersise) {
                $o = 0;
                $excersise = Excersise::where('id', $workout_excersise->excersise_id)->first(['id', 'name', 'description', 'excersise_media_url']);
                $excersise['excersise_media_url'] = 'storage/images/excersise/' . $excersise['excersise_media_url'];
                if ($workout_excersise->count != null) {
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
            $workout['user'] = User::where('id', Auth::id())->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
            str_starts_with($workout['user']->prof_img_url,'https') ? : $workout['user']->prof_img_url = 'storage/images/users/' . $workout['user']->prof_img_url;
            $workout['review_count'] = (string) $workout['review_count'];
            $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
            $workout['saved'] = false;
            if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
            return $this->success(__("messages.Workout Returned Successfully"), $workout, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if (in_array($user->role_id, [2, 4, 5])) {
                $fields = Validator::make($request->only('name', 'categorie_id','description', 'equipment', 'difficulty', 'workout_image', 'excersises'), [
                    'name' => 'required|string',
                    'description' => 'required|string',
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
                    return $this->fail(__('messages.Name already taken!'), 400);
                }
                //return response($fields['description']);
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
                    return $this->fail(__("messages.Too many excercises added!"), 400);
                }
                $i = 0;
                $position = 0;
                foreach ($excersise_list as $excersise) {
                    $excersise = (array)$excersise;
                    $time = 0;
                    $position = ++$i;
                    if ($excersise['isTime'] == true) $time = 1;
                    if ($time == 0) {
                        WorkoutExcersises::create([
                            'user_id' => Auth::id(),
                            'excersise_id' => $excersise['id'],
                            'workout_id' => $workout->id,
                            'count' => $excersise['value'],
                            'position' => $position
                        ]);
                        $workout->excersise_count++;
                        $workout->length += $excersise['value'];
                        $calories = Excersise::find($excersise['id'])->burn_calories;
                        $predicted_calories_burn += $calories;
                    } else {
                        WorkoutExcersises::create([
                            'user_id' => Auth::id(),
                            'excersise_id' => $excersise['id'],
                            'workout_id' => $workout->id,
                            'length' => $excersise['value'],
                            'position' => $position
                        ]);
                        $workout->excersise_count++;
                        $workout->length += $excersise['value'];
                        $calories = Excersise::find($excersise['id'])->burn_calories;
                        $predicted_calories_burn += $calories;
                    }
                }
                $workout->predicted_burnt_calories = $predicted_calories_burn;
                $workout->created_at = (string)Carbon::parse($workout->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                $workout->update();
                return $this->success(__("messages.Workout Created Successfully"), $workout, 201);
            } else {
                return $this->fail(__("messages.Permission Denied!"), 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $workout = Workout::find($id);
            $user = User::find(Auth::id());
            if (in_array($user->role_id, [4, 5]) || $workout->user_id == Auth::id()) {
                $fields = Validator::make($request->only('name', 'categorie_id','description', 'equipment', 'difficulty', 'workout_image', 'excersises'), [
                    'name' => 'required|string',
                    'description' =>'required|string',
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
                if (in_array($fields['name'], Excersise::where('name', '!=', $fields['name'])->get('name')->toArray())) {
                    return $this->fail(__("messages.Name Already Exists"), 400);
                }
                if ($workout->name != $fields['name']) $workout->name = $fields['name'];
                if ($workout->description != $fields['description']) $workout->description = $fields['description'];
                if ($workout->categorie_id != $fields['categorie_id']) $workout->categorie_id = $fields['categorie_id'];
                if ($workout->equipment != $fields['equipment']) $workout->equipment = $fields['equipment'];
                if ($workout->difficulty != $fields['difficulty']) $workout->difficulty = $fields['difficulty'];
                if ($request->hasFile('workout_image')) {
                    $original_path = 'public/images/workout';
                    if (Storage::exists($original_path . '/' . $workout->workout_image_url)) {
                        Storage::delete($original_path . '/' . $workout->workout_image_url);
                    }
                    $destination_path = 'public/images/workout';
                    $image = $request->file('workout_image');
                    $randomString = Str::random(30);
                    $image_name = $workout->id . '/' . $randomString . $image->getClientOriginalName();
                    $path = $image->storeAs($destination_path, $image_name);
                    $workout->workout_image_url = $image_name;
                }
                $predicted_calories_burn = 0;
                $workout->length = 0;
                $workout_predicted_burnt_calories = 0;
                $excersise_list = json_decode($fields['excersises']);
                if (count($excersise_list) > 30) {
                    return $this->fail(__("messages.Too many excercises added!"), 400);
                }
                $workout->workout_excersise->each(function ($data) {
                    $data->delete();
                });
                $i = 0;
                $position = 0;
                $workout->excersise_count = 0;
                foreach ($excersise_list as $excersise) {
                    $excersise = (array)$excersise;
                    $time = 0;
                    $position = ++$i;
                    if ($excersise['isTime'] == true) $time = 1;
                    if ($time == 0) {
                        WorkoutExcersises::create([
                            'user_id' => Auth::id(),
                            'excersise_id' => $excersise['id'],
                            'workout_id' => $workout->id,
                            'count' => $excersise['value'],
                            'position' => $position
                        ]);
                        $workout->excersise_count++;
                        $workout->length += $excersise['value'];
                        $calories = Excersise::find($excersise['id'])->burn_calories;
                        $predicted_calories_burn += $calories;
                    } else {
                        WorkoutExcersises::create([
                            'user_id' => Auth::id(),
                            'excersise_id' => $excersise['id'],
                            'workout_id' => $workout->id,
                            'length' => $excersise['value'],
                            'position' => $position
                        ]);
                        $workout->excersise_count++;
                        $workout->length += $excersise['value'];
                        $calories = Excersise::find($excersise['id'])->burn_calories;
                        $predicted_calories_burn += $calories;
                    }
                }
                $workout->predicted_burnt_calories = $predicted_calories_burn;
                $workout->updated_at = (string)Carbon::parse($workout->updated_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                $workout->update();
                return $this->success(__("messages.Workout Edited Successfully"), [], 200);
            }
            return $this->fail(__("messages.Permission Denied!"), 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());
            $workout = Workout::find($id);
            if ($workout->coach->id == Auth::id() || in_Array($user->role_id, [4, 5])) {
                $workout_excersises = $workout->workout_excersise->each(function ($data) {
                    $data->delete();
                });
                $favorites = FavoriteWorkout::where('workout_id',$workout->id)->delete();
                $practices = PracticeWorkout::where('workout_id' , $workout->id)->delete();
                $workout->delete();
                return $this->success("Success", [], 200);
            }
            return $this->fail(__("messages.Permission Denied!"), 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function favorite($id)
    {
        try {
            $favorite = FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $id])->exists();
            if ($favorite) {
                $favorite = FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $id])->delete();
                return $this->success(__("messages.Workout deleted from favorites!"), [], 200);
            } else {
                $favorite = FavoriteWorkout::create([
                    'user_id' => Auth::id(),
                    'workout_id' => $id
                ]);
                return $this->success(__("messages.Workout added to favorites!"), $favorite, 200);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function favorites()
    {
        try {
            $user_id = Auth::id();
            $favorites = Workout::whereIn('id' , FavoriteWorkout::where('user_id' , Auth::id())->pluck('workout_id'))->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url','description','categorie_id as categorie_name']);
            foreach($favorites as $favorite)
            {
                $favorite['workout_image_url'] = 'storage/images/workout/' . $favorite['workout_image_url'];
                WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $favorite->id])->exists() == true ? $favorite['is_reviewed'] = true : $favorite['is_reviewed'] = false;
                $favorite['user'] = User::where('id',$favorite['user'])->first(['id', 'f_name', 'l_name', 'prof_img_url']);
                str_starts_with($favorite['user']['prof_img_url'],'https') ? : $favorite['user']['prof_img_url'] = 'storage/images/users/' . $favorite['user']['prof_img_url'];
                $favorite['categorie_name'] = WorkoutCategorie::find($favorite['categorie_name'])->only('name');
            }
            return $this->success(__("messages.Favorite Workouts Returned Successfully"), array_values($favorites->paginate(15)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function user_workouts($id)
    {
        try {
            $workouts = Workout::where('user_id', $id)->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url','description','categorie_id as categorie_name','review_count']);
            foreach ($workouts as $workout) {
                $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                str_starts_with($workout['user_id']['prof_img_url'],'https') ? : $workout['user_id']['prof_img_url'] = 'storage/images/users/' . $workout['user_id']['prof_img_url'];
                $workout['saved'] = false;
                $workout['categorie_name'] = WorkoutCategorie::find($workout['categorie_name'])->only('name');
                if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
                WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists() == true ? $workout['is_reviewed'] = true : $workout['is_reviewed'] = false;
            }
            return $this->success(__("messages.User Workouts Returned Successfully"), array_values($workouts->paginate(15)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function my_workouts()
    {
        try {
            $workouts = Workout::where('user_id', Auth::id())->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url','description','categorie_id as categorie_name','review_count']);
            foreach ($workouts as $workout) {
                $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                str_starts_with($workout['user']['prof_img_url'],'https') ? : $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                $workout['saved'] = false;
                $workout['categorie_name'] = WorkoutCategorie::find($workout['categorie_name'])->only('name');
                if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
                WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists() == true ? $workout['is_reviewed'] = true : $workout['is_reviewed'] = false;
            }
            return $this->success(__("messages.My Workouts Returned Successfully"), array_values($workouts->paginate(15)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
    //Schedule
    //Chest
    //Back
    //Arms
    //Abs
    //Shoulders
    //Legs
    //Abs
    public function workouts_filter($filter_1, $filter_2 = null)
    {
        try {
            //$workout_schedule = [ 'Chest' , 'Back' , 'Arms' , 'Abs' , 'Shoulders' , 'Legs' , 'Abs' ];
            $difficulty = [1, 2, 3];
            if ($filter_2 < 4) $difficulty = [$filter_2];
            $following = Follow::where('follower_id' , Auth::id())->get()->pluck('following');
            if ($filter_1 == 1) //recommended
            {
                // $last_workout_practiced = Workout::find(Practice::where('user_id' , Auth::id())->last()->workout_id);
                // $last_categorie = WorkoutCategorie::find($last_workout_practiced->categorie_id)->name;
                // $next_workout_categorie_per_schedule = WorkoutCategorie::where('name' , $last_practice->workout_id);
                // $workout_schedule_recommendation = Workout::query()
                                                        //   ->whereIn('user_id' , $following)
                                                        //   ->whereIn('difficulty' , $difficulty)
                                                        //   ->orderBy('review_count', 'desc')
                                                        //   ->orderBy('difficulty', 'asc')
                                                        //   ->orderBy('predicted_burnt_calories' ,'asc')
                                                        //   ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url','description','categorie_id as categorie_name'])->each(function ($data) {
                                                        //       $data->created_at =  (string)Carbon::parse($data->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                                                        //   });
                $followed_coach_workouts = Workout::query()
                    ->whereIn('user_id', $following)
                    ->whereIn('difficulty', $difficulty)
                    ->orderBy('review_count', 'desc')
                    ->orderBy('difficulty', 'asc')
                    ->orderBy('predicted_burnt_calories' ,'asc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url','description','categorie_id as categorie_name'])->each(function ($data) {
                        $data->created_at =  (string)Carbon::parse($data->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                    });
                $workouts = Workout::query()
                    ->whereNotIn('user_id', array_merge(Block::where('user_id', Auth::id())->get()->pluck('blocked')->toArray(), Follow::where('follower_id', Auth::id())->get()->pluck('following')->toArray()))
                    ->whereIn('difficulty', $difficulty)
                    ->orderBy('review_count', 'desc')
                    ->orderBy('difficulty', 'asc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url','description','categorie_id as categorie_name','review_count'])->each(function ($data) {
                        $data->created_at =  (string)Carbon::parse($data->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                    });                //$workouts_result = $followed_coach_workouts->getCollection();
                $workouts_result = $followed_coach_workouts->merge($workouts);
                //return response($workouts_result);
                foreach ($workouts_result as $workout) {
                    $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];

                    $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                    str_starts_with($workout['user']['prof_img_url'],'https') ? : $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                    $workout['saved'] = false;
                    $workout['categorie_name'] = WorkoutCategorie::find($workout['categorie_name'])->only('name');
                    if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
                    $workout['created_at'] = (string)Carbon::parse($workout['created_at'])->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                    WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists() == true ? $workout['is_reviewed'] = true : $workout['is_reviewed'] = false;
                }
                return $this->success(__("messages.Filtered Workouts Returned Successfully"), array_values($workouts_result->paginate(15)->getCollection()->toArray()), 200);
            } else if ($filter_1 == 2) //all
            {
                $workouts = Workout::query()
                    ->whereNotIn('user_id', Block::where('user_id', Auth::id())->get()->pluck('blocked'))
                    ->whereIn('difficulty', $difficulty)
                    ->orderBy('review_count', 'desc')
                    ->orderBy('difficulty', 'asc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url','description','categorie_id as categorie_name','review_count'])->each(function ($data) {
                        $data->created_at =  (string)Carbon::parse($data->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                    });
                foreach ($workouts as $workout) {
                    $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                    $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                    str_starts_with($workout['user']['prof_img_url'],'https') ? : $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                    $workout['saved'] = false;
                    $workout['categorie_name'] = WorkoutCategorie::find($workout['categorie_name'])->only('name');
                    if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
                    $workout['created_at'] =$workout['created_at']->format('Y-m-d H:i:s');
                    WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists() == true ? $workout['is_reviewed'] = true : $workout['is_reviewed'] = false;
                }
                return $this->success(__("messages.Filtered Workouts Returned Successfully"), array_values($workouts->paginate(15)->getCollection()->toArray()), 200);
            } else {
                //return response($filter_2);
                $workouts = Workout::query()
                    ->whereNotIn('user_id', Block::where('user_id', Auth::id())->get()->pluck('blocked'))
                    ->whereIn('difficulty', $difficulty)
                    ->where('categorie_id', $filter_1)
                    ->orderBy('name', 'asc')
                    ->orderBy('difficulty', 'asc')
                    ->orderBy('review_count', 'desc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url','description','categorie_id as categorie_name','review_count'])->each(function ($data) {
                        $data->created_at =  (string)Carbon::parse($data->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                    });                foreach ($workouts as $workout) {
                    $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                    $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                    str_starts_with($workout['user']['prof_img_url'],'https') ? : $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                    $workout['saved'] = false;
                    $workout['categorie_name'] = WorkoutCategorie::find($workout['categorie_name'])->only('name');
                    if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
                    $workout['created_at'] = $workout['created_at']->format('Y-m-d H:i:s');
                    WorkoutReview::where(['user_id' => Auth::id() , 'workout_id' => $workout->id])->exists() == true ? $workout['is_reviewed'] = true : $workout['is_reviewed'] = false;
                }
                return $this->success(__("messages.Filtered Workouts Returned Successfully"), array_values($workouts->paginate(15)->getCollection()->toArray()), 200);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function review(Request $request, $id)
    {
        try {
            $fields = Validator::make($request->only('description', 'stars'), [
                'description' => 'string',
                'stars' => 'required|numeric|between:0.1,5'
            ]);
            if ($fields->fails()) {
                return $this->fail($fields->errors()->first(), 400);
            }
            $fields = $fields->safe()->all();
            if (WorkoutReview::where(['workout_id' => $id, 'user_id' => Auth::id()])->exists()) {
                return $this->fail("You can't add more than one review!!", 400);
            }
            if(!Workout::where('id',$id)->exists())
            {
                return $this->fail('Workout Not Found');
            }
            if(Workout::find($id)->user_id == Auth::id())
            {
                return $this->fail(__('messages.Cant add a review to your own diet!'));
            }
            $fields['user_id'] = $request->user()->id;
            $fields['workout_id'] = $id;
            $review = WorkoutReview::create($fields);
            $workout = Workout::find($fields['workout_id']);
            $review_rate = $workout->review_count;
            $review_count = $workout->reviews->count();
            $review_rating = (float) ((($review_count - 1) * $review_rate) + $fields['stars']) / ($review_count);
            $workout->review_count = $review_rating;
            $workout->update();
            return $this->success("Review Submitted Successfully", $workout, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function reviews($id)
    {
        try {
            $reviews = WorkoutReview::where('workout_id', $id)->get(['id', 'stars', 'description', 'user_id'])->each(function ($data) {
                $data['stars'] = round($data['stars'], 1);
                $data['user_id'] = User::where('id', $data['user_id'])->first(['id', 'f_name', 'l_name', 'prof_img_url']);
                str_starts_with($data['user_id']['prof_img_url'],'https') ? : $data['user_id']['prof_img_url'] = 'storage/images/users/' . $data['user_id']['prof_img_url'];
                return $data;
            });
            return $this->success(__("messages.Workout Reviews Returned Successfully"), array_values($reviews->paginate(15)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function edit_review(Request $request, $id)
    {
        try {
            $fields = Validator::make($request->only('description', 'stars'), [
                'description' => 'string',
                'stars' => 'required|numeric|between:0.1,5'
            ]);
            if ($fields->fails()) {
                return $this->fail($fields->errors()->first(), 400);
            }
            $fields = $fields->safe()->all();
            if (!WorkoutReview::where('id', $id)->exists()) {
                return $this->fail(__("messages.Review Not Found"), 400);
            }
            $review = WorkoutReview::find($id);
            if ($review->stars != $fields['stars'] || $review->description != $fields['description']) {
                $workout = Workout::find($review->workout_id);
                $review_rate = $workout->review_count;
                $review_count = $workout->reviews->count();
                $review_rating = (double) ((((($review_count) * $review_rate) - $review->stars) + $fields['stars'])) / ($review_count);
                $review->stars = $fields['stars'];
                $workout->review_count = $review_rating;
                $workout->update();
            }
            if ($request->has('description')) {
                if ($fields['description'] != $review->description) {
                    $review->description = $fields['description'];
                }
            } else {
                $review->description = '';
            }
            $review->update();
            return $this->success(__("messages.Review Edited Successfully"), WorkoutReview::find($id), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function delete_review($id)
    {
        try {
            $review = WorkoutReview::find($id);
            $workout = Workout::find($review->workout_id);
            $user = User::find(Auth::id());
            if ($review->user_id == Auth::id() || in_array($user->role_id, [4, 5])) {
                if($workout->reviews->count() == 1)
                {
                    $workout->review_count = 0;
                    $workout->update();
                }
                else
                {
                    $review_rate = $workout->review_count;
                $review_count = $workout->reviews->count();
                $review_rating = (float) ((($review_count) * $review_rate) - $review->stars) / ($review_count - 1);
                $workout->review_count = $review_rating;
                $workout->update();
                }
                $review->delete();
                return $this->success(__("messages.Review Deleted Successfully"), [], 200);
            }
            return $this->fail("Permission Denied!", 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
