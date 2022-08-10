<?php

namespace App\Http\Controllers;

use App\Models\Diet;
use App\Http\Requests\StoreDietRequest;
use App\Http\Requests\UpdateDietRequest;
use App\Models\DietMeal;
use App\Models\DietReview;
use App\Models\DietSubscribe;
use App\Models\FavoriteDiet;
use App\Models\Food;
use App\Models\Meal;
use App\Models\MealFood;
use App\Models\User;
use App\Policies\DietMealPolicy;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\JsonDecoder;

class DietController extends Controller
{
    use GeneralTrait;
    function getFoodList($food_ids)
    {
        try {
            $food_list = [];
            foreach ($food_ids as $food_id) {
                $food = Food::find($food_id)->first();
                //return response($food);
                $food->food_image_url = 'storage/images/food/' . $food->food_image_url;
                if ($food->description == null) {
                    $food->description = "";
                }
                $food_list[] = $food;
            }
            return $food_list;
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
    public function index()
    {
        try {
            $diets = Diet::all(['id', 'name', 'review_count', 'created_by', 'created_at'])->each(function ($data) {
                $food = [];
                $mealcount = DietMeal::where('diet_id', $data->id)->count();
                $data['review_count'] = round($data['review_count'], 1);
                $data['meal_count'] = $mealcount;
                $data['created_by'] = User::where('id', $data->created_by)->get(['id', 'f_name', 'l_name', 'prof_img_url'])->first();
                $data['created_by']['prof_img_url'] = 'storage/images/users/' . $data['created_by']['prof_img_url'];
                $data['saved'] = false;
                DietReview::where(['diet_id' => $data->id, 'user_id' => Auth::id()])->exists() == true ? $data['is_reviewed'] = true : $data['is_reviewed'] = false;
                if (FavoriteDiet::where(['user_id' => Auth::id(), 'diet_id' => $data->id])->exists()) $data['saved'] = true;
            });
            return $this->success("Success", array_values($diets->paginate(3)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function my_diets()
    {
        try {
            $diets = Diet::where('created_by', Auth::id())->get(['id', 'name', 'created_by', 'created_at']);
            foreach ($diets as $diet) {
                $food = [];
                $mealcount = DietMeal::where('diet_id', $diet->id)->count();
                $diet['meal_count'] = $mealcount;
                $diet['created_by'] = User::find($diet->created_by)->only(['id', 'f_name', 'l_name', 'prof_img_url']);
                $diet['saved'] = false;

                if (FavoriteDiet::where(['user_id' => Auth::id(), 'diet_id' => $diet->id])->exists()) $diet['saved'] = true;
            }
            return $this->success("Success", array_values($diets->paginate(3)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function user_diets($id)
    {
        try {
            $diets = Diet::where('created_by', $id)->get(['id', 'name', 'created_by', 'created_at']);
            foreach ($diets as $diet) {
                $food = [];
                $mealcount = DietMeal::where('diet_id', $diet->id)->count();
                $diet['meal_count'] = $mealcount;
                $diet['created_by'] = User::find($diet->created_by)->only(['id', 'f_name', 'l_name', 'prof_img_url']);
                $diet['saved'] = false;
                if (FavoriteDiet::where(['user_id' => Auth::id(), 'diet_id' => $diet->id])->exists()) $diet['saved'] = true;
            }
            return $this->success("Success", array_values($diets->paginate(3)->getCollection()->toArray()), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if (in_array($request->user()->role_id, [3, 4, 5])) {
                $fields = Validator::make($request->only('name', 'meals'), [
                    'name' => 'required|string',
                    'meals' => 'required|string'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                $fields['created_by'] = $request->user()->id;
                $days = json_decode($fields['meals']);
                unset($fields['meals']);
                $diet = Diet::create($fields);
                $message = 'Diet Created Successfully';
                $i = 0;
                $result = [];
                foreach ($days as $daymeals) {
                    $i++;
                    $fullmeals = [];
                    foreach ($daymeals as $meal) {
                        $data = [
                            'meal_id' => $meal,
                            'diet_id' => $diet->id,
                            'day' => $i
                        ];
                        $dietmeal = DietMeal::create($data);
                        $fullmeals[] = $dietmeal->meal;
                    }
                    $result[] = [
                        'day' => $i,
                        'meals' => $fullmeals
                    ];
                }
                $diet = [
                    'id' => $diet->id,
                    'name' => $diet->name,
                    'created_by' => $request->user(),
                    'schedule' => $result
                ];
                return $this->success(_($message), $diet, 201);
            } else {
                return $this->fail("Permission Denied", 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }


    public function show($id)
    {
        try {
            $diet = Diet::find($id);
            $dietdays = DietMeal::where('diet_id', $diet->id)->orderBy('day')->orderBy('meal_id')->get(['meal_id', 'day'])->groupBy('day');
            //return response($dietdays);
            $result = array();
            $day_count = 1;
            foreach ($dietdays as $day) {
                $day_meals = [];
                //$day = $day->sort();
                foreach ($day as $day_meal) {
                    $meal = Meal::find($day_meal->meal_id);
                    $meal_food = MealFood::where('meal_id', $meal->id)->get();
                    $meal['food_list'] = $this->getFoodList($meal_food);
                    $day_meals[] = $meal;
                }
                $result[] = [
                    'day' => $day_count,
                    'meal_list' => $day_meals
                ];
                $day_count++;
            }
            $day_count++;
            $mealcount = DietMeal::where('diet_id', $diet->id)->count();
            $diet['meal_count'] = $mealcount;
            $diet['schedule'] = $result;
            $diet['created_by'] = User::find($diet['created_by'])->first(['id', 'f_name', 'l_name', 'prof_img_url']);
            $diet['created_by']->prof_img_url = 'storage/images/users/' . $diet['created_by']->prof_img_url;
            $diet['subscribed'] = false;
            if (DietSubscribe::where(['user_id' => Auth::id(), 'diet_id' => $id])->exists()) $diet['subscribed'] = true;
            return $this->success("Success", $diet, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    function get_daymeals($days)
    {
        try {
            $old_meals = [];
            foreach ($days as $day) {
                $meals = [];
                foreach ($day as $meal) {
                    $meals[] = $meal->meal_id;
                }
                $old_meals[] = $meals;
            }
            return $old_meals;
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->role_id == 4 || $request->user()->role_id == 5 || $request->user()->id == 3) {
                $fields = Validator::make($request->only('name', 'meals'), [
                    'name' => 'required|string',
                    'meals' => 'required|string'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                $diet = Diet::find($id);
                if ($fields['name'] != $diet->name) $diet->name = $fields['name'];
                $meal_list = json_decode($fields['meals']);
                $dietdays = DietMeal::where('diet_id', $diet->id)->orderBy('day')->orderBy('meal_id')->get(['meal_id', 'day'])->groupBy('day');
                $original_days = $dietdays->count();
                error_log($original_days);
                $old_meals = $this->get_daymeals($dietdays);
                //return response($meal_list);
                // return response($dietdays);
                //return response($old_meals);
                // $day = 0;
                // //return response($meal_list);
                // foreach ($meal_list as $day_meals) {
                //     //error_log((json_decode($day_meals)));
                //     $delete_meals = [];
                //     $new_meals = [];
                //     if (array_key_exists($day, $old_meals)) {
                //         $new_meals = array_diff($day_meals, $old_meals[$day]);
                //         $delete_meals = array_diff($old_meals[$day], $day_meals);
                //     } else {
                //         $new_meals = $day_meals;
                //     }
                //     error_log((json_encode($delete_meals)));
                //     //return response($new_meals);
                //     $day++;
                //     //error_log(json_encode($delete_meals));
                //     foreach ($delete_meals as $meal) {
                //         //return response($meal);
                //         $dietmeal = DietMeal::where(['diet_id' => $diet->id, 'meal_id' => $meal, 'day' => $day])->first()->delete();
                //     }
                //     foreach ($new_meals as $meal) {
                //         $dietmeal = DietMeal::create(['user_id' => Auth::id(), 'meal_id' => $meal, 'diet_id' => $diet->id, 'day' => $day]);
                //     }
                // }
                // for(; $day<=$original_days ; $day++)
                // {
                //     $dietmeal = DietMeal::where(['diet_id' => $diet->id , 'day' => $day])->delete();
                // }
                $dietmeals = Diet::find($id)->dietmeal()->delete();
                $days = json_decode($fields['meals']);
                $i = 0;
                $result = [];
                foreach ($days as $daymeals) {
                    $i++;
                    $fullmeals = [];
                    foreach ($daymeals as $meal) {
                        $data = [
                            'meal_id' => $meal,
                            'diet_id' => $diet->id,
                            'day' => $i
                        ];
                        $dietmeal = DietMeal::create($data);
                        $fullmeals[] = $dietmeal->meal;
                    }
                    $result[] = [
                        'day' => $i,
                        'meals' => $fullmeals
                    ];
                }
                $diet->update();
                return $this->success(_("Edited Successfully"), $this->show($diet->id), 200);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());
            $diet = Diet::find($id);
            if (in_array($user->id, [4, 5]) || $user->id = $diet->created_by) {
                $diet->dietmeal()->delete();
                $diet->favorites()->delete();
                $diet->delete();
                $message = 'Diet Deleted Successfully';
                return $this->success(_($message), $diet, 200);
                return $this->fail(_("Permission Denied. Not the owner"), 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function favorite($id)
    {
        try {
            $favorite = FavoriteDiet::where(['user_id' => Auth::id(), 'diet_id' => $id])->exists();
            if ($favorite) {
                $favorite = FavoriteDiet::where(['user_id' => Auth::id(), 'diet_id' => $id])->delete();
                return $this->success("Deleted form favorites", [], 200);
            } else {
                $favorite = FavoriteDiet::create([
                    'user_id' => Auth::id(),
                    'diet_id' => $id
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
            $diets =Diet::whereIn('id' , FavoriteDiet::where('user_id' , Auth::id())->pluck('diet_id'))->get(['id', 'name', 'created_by', 'created_at'])->each(function ($data) {
                $diet['meal_count'] = DietMeal::where('diet_id', $data['id'])->count();
                $diet['created_by'] = User::find($diet['created_by'])->only(['id', 'f_name', 'l_name', 'prof_img_url']);
            }) ;
            return $this->success("Favorites", array_values($diets->paginate(15)->getCollection()->toArray()), 200);
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
            if (DietReview::where(['diet_id' => $id, 'user_id' => Auth::id()])->exists()) {
                return $this->fail("You can't add more than one review!!", 400);
            }
            if(Diet::find($id)->user_id == Auth::id())
            {
                return $this->fail('Cant add a review to your own diet!');
            }
            $fields['user_id'] = $request->user()->id;
            $fields['diet_id'] = $id;
            $review = DietReview::create($fields);
            $diet = Diet::find($fields['diet_id']);
            $review_rate = $diet->review_count;
            $review_count = $diet->reviews->count();
            $review_rating = (float) ((($review_count - 1) * $review_rate) + $fields['stars']) / ($review_count);
            $diet->review_count = $review_rating;
            $diet->update();
            return $this->success("Done", $diet, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function reviews($id)
    {
        try {
            $reviews = DietReview::where('diet_id', $id)->get(['id', 'stars', 'description', 'user_id', 'created_at'])->each(function ($data) {
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
            if (!DietReview::where('id', $id)->exists()) {
                return $this->fail("Review Not Found", 400);
            }
            $review = DietReview::find($id);
            if ($review->stars != $fields['stars']) {
                $diet = Diet::find($review->diet_id);
                $review_rate = $diet->review_count;
                $review_count = $diet->reviews->count();
                $review_rating = (float) ((($review_count) * $review_rate) - $review->stars + $fields['stars']) / ($review_count);
                $review->stars = $fields['stars'];
                $diet->review_count = $review_rating;
                $diet->update();
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
            $review = DietReview::find($id);
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

    //add subscribed to summary
    public function subscribe($id)
    {
        try {
            $diet = Diet::find($id);
            $user = User::find(Auth::id());
            if (DietSubscribe::where('user_id', $user->id)->exists()) {
                $subscribe = DietSubscribe::where('user_id', $user->id)->first();
                if ($subscribe->diet_id == $id) {
                    $subscribe->delete();
                    return $this->success("Unsubscribed", [], 200);
                }
                $subscribe->diet_id = $id;
                $subscribe->update();
                return $this->success("Done!", $subscribe, 200);
            }
            $subscribe = DietSubscribe::create(['user_id' => $user->id, 'diet_id' => $diet->id]);
            return $this->success("Created", $subscribe, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
