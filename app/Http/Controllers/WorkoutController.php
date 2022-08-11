<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Models\Block;
use App\Models\Excersise;
use App\Models\FavoriteWorkout;
use App\Models\Follow;
use App\Models\User;
use App\Models\WorkoutExcersises;
use App\Models\WorkoutReview;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
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
            $workouts = Workout::all(['id', 'name', 'description', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at']);
            foreach ($workouts as $workout) {
                $workout['workout_image_url'] = 'storage/image/workout/' . $workout['workout_image_url'];
                $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
            }
            //$workouts = (array) $workouts;
            return $this->success("Success", $workouts, 200);
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
            //return response($workout['user']);
            $workout['user']->prof_img_url = 'storage/images/users/' . $workout['user']->prof_img_url;
            $workout['review_count'] = (string) $workout['review_count'];
            $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
            $workout['description'] = "Hello";
            unset($workout['workout_excersise']);
            unset($workout['like_count']);
            unset($workout['workout_excersise']);
            return $this->success("Workout", $workout, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            //return $this->success("Success", $request , 200);
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
                    return $this->fail('Name already taken!', 400);
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
                    return $this->fail("Too many excersises added!", 400);
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
                return $this->success("Workout Created Successfully", $workout, 201);
            } else {
                return $this->fail("Permission Denied!", 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $user = User::find(Auth::id());
            if (in_array($user->role_id, [2, 4, 5])) {
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
                $workout = Workout::find($id);
                if (in_array($fields['name'], Excersise::where('name', '!=', $fields['name'])->get('name')->toArray())) {
                    return $this->fail("Name Already Exists", 400);
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
                    if ($excersise['isTime'] == true) $time = 1;
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
                            'length' => $excersise['value'],
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
                $workout->delete();
                return $this->success("Success", [], 200);
            }
            return $this->fail("Permission Denied", 400);
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
                return $this->success("Deleted form favorites", [], 200);
            } else {
                $favorite = FavoriteWorkout::create([
                    'user_id' => Auth::id(),
                    'workout_id' => $id
                ]);
                return $this->success("Added to favorites!", $favorite, 200);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function favorites()
    {
        try {
            $user_id = Auth::id();
            $favorites = User::find($user_id)->favoriteworkouts;
            foreach ($favorites as $favorite) {
                $workout = Workout::find($favorite->workout_id)->only(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id', 'workout_image_url']);
                $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                $workout['user_id'] = User::find($workout['user_id'])->only(['id', 'f_name', 'l_name', 'prof_img_url']);
                $result[] = $workout;
            }
            return $this->success("Favorites", array_values($favorites->paginate(3)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function user_workouts($id)
    {
        try {
            $workouts = Workout::where('user_id', $id)->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url']);
            foreach ($workouts as $workout) {
                $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                $workout['saved'] = false;
                if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
            }
            return $this->success("Success", array_values($workouts->paginate(3)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function my_workouts()
    {
        try {
            $workouts = Workout::where('user_id', Auth::id())->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url']);
            foreach ($workouts as $workout) {
                $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                $workout['saved'] = false;
                if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $workout->id])->exists()) $workout['saved'] = true;
            }
            return $this->success("Success", array_values($workouts->paginate(3)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function workouts_filter($filter_1, $filter_2 = null)
    {
        try {
            $difficulty = [1, 2, 3];
            if ($filter_2 != null) $difficulty = [$filter_2];
            if ($filter_1 == 1) //recommended
            {
                $followed_coach_workouts = Workout::query()
                    ->whereIn('user_id', Follow::where('follower_id', Auth::id())->get()->pluck('following'))
                    ->whereIn('difficulty', $difficulty)
                    ->orderBy('review_count', 'desc')
                    ->orderBy('difficulty', 'asc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'workout_image_url']);
                $workouts = Workout::query()
                    ->whereNotIn('user_id', array_merge(Block::where('user_id', Auth::id())->get()->pluck('blocked')->toArray(), Follow::where('follower_id', Auth::id())->get()->pluck('following')->toArray()))
                    ->whereIn('difficulty', $difficulty)
                    ->orderBy('review_count', 'desc')
                    ->orderBy('difficulty', 'asc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url']);
                //$workouts_result = $followed_coach_workouts->getCollection();
                $workouts_result = $followed_coach_workouts->merge($workouts);
                //return response($workouts_result);
                foreach ($workouts_result as $workout) {
                    $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                    $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                    $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                }
                return $this->success("Success", array_values($workouts_result->paginate(3)->getCollection()->toArray()), 200);
            } else if ($filter_1 == 2) //all
            {
                $workouts = Workout::query()
                    ->whereNotIn('user_id', Block::where('user_id', Auth::id())->get()->pluck('blocked'))
                    ->whereIn('difficulty', $difficulty)
                    ->orderBy('review_count', 'desc')
                    ->orderBy('difficulty', 'asc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url']);
                foreach ($workouts as $workout) {
                    $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                    $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                    $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                }
                return $this->success("Success", array_values($workouts->paginate(3)->getCollection()->toArray()), 200);
            } else {
                //return response($filter_2);
                $workouts = Workout::query()
                    ->whereNotIn('user_id', Block::where('user_id', Auth::id())->get()->pluck('blocked'))
                    ->whereIn('difficulty', $difficulty)
                    ->where('categorie_id', $filter_1)
                    ->orderBy('name', 'asc')
                    ->orderBy('difficulty', 'asc')
                    ->orderBy('review_count', 'desc')
                    ->get(['id', 'name', 'predicted_burnt_calories', 'length', 'excersise_count', 'equipment', 'difficulty', 'user_id as user', 'created_at', 'workout_image_url']);
                foreach ($workouts as $workout) {
                    $workout['workout_image_url'] = 'storage/images/workout/' . $workout['workout_image_url'];
                    $workout['user'] = User::where('id', $workout['user'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                    $workout['user']['prof_img_url'] = 'storage/images/users/' . $workout['user']['prof_img_url'];
                }
                return $this->success("Success", array_values($workouts->paginate(3)->getCollection()->toArray()), 200);
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
            if (WorkoutReview::where(['workokut_id' => $id, 'user_id' => Auth::id()])->exists()) {
                return $this->fail("You can't add more than one review!!", 400);
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
            return $this->success("Done", $workout, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function reviews($id)
    {
        try {
            $reviews = WorkoutReview::where('workout_id', $id)->get(['id', 'stars', 'description', 'user_id', 'created_at'])->each(function ($data) {
                $data['stars'] = round($data['stars'], 1);
                $data['user_id'] = User::where('id', $data['user_id'])->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                $data['user_id']->prof_img_url = 'storage/images/users/' . $data['user_id']->prof_img_url;
                return $data;
            });
            return $this->success("Success", array_values($reviews->paginate(3)->getCollection()->toArray()), 200);
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
                return $this->fail("Review Not Found", 400);
            }
            $review = WorkoutReview::find($id);
            if ($review->stars != $fields['stars']) {
                $workout = Workout::find($review->workout_id);
                $review_rate = $workout->review_count;
                $review_count = $workout->reviews->count();
                $review_rating = (float) ((($review_count) * $review_rate) - $review->stars + $fields['stars']) / ($review_count);
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
            return $this->success("Done", $review, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function delete_review($id)
    {
        try {
            $review = WorkoutReview::find($id);
            $user = User::find(Auth::id());
            if ($review->user_id == Auth::id() || in_array($user->role_id, [4, 5])) {
                $review->delete();
                return $this->success("Deleted Successfully", [], 200);
            }
            return $this->fail("Permission Denied", 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
