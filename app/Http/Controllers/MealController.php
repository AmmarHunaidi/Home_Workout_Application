<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Models\DietMeal;
use App\Models\Food;
use App\Models\MealFood;
use App\Models\User;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MealController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        try {
            $result = [];
            $meals = Meal::all(['id', 'type', 'description', 'calorie_count']);
            foreach ($meals as $meal) {
                $food = [];
                $mealfood = MealFood::where('meal_id', $meal['id'])->get();
                foreach ($mealfood as $mf) {
                    $fd = Food::find($mf->food_id);
                    $fd->food_image_url = 'storage/images/food/' . $fd->food_image_url;
                    if ($fd->approval == 1) $food[] = $fd;
                }
                $meal['food_list'] = $food;
                $result[] = $meal;
            }
            return $this->success("Success", $result, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            if ($request->user()->role_id == 3 || $request->user()->role_id == 4 || $request->user()->role_id == 5) {
                $fields = Validator::make($request->only(['type', 'description', 'food_ids', 'day']), [
                    'type' => 'required|string|in:Breakfast,Dinner,Snack,Lunch',
                    'description' => 'required|string',
                    'food_ids' => 'string|required'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 401);
                }
                $fields = $fields->safe()->all();
                $fields['user_id'] = $request->user()->id;
                $meal = Meal::create($fields);
                $food_result = [];
                $food_list = json_decode($fields['food_ids']);
                foreach ($food_list as $food_id) {
                    $food = Food::find($food_id);
                    $food_result[] = $food;
                    $data = [
                        'meal_id' => $meal->id,
                        'food_id' => $food->id,
                        'user_id' => $request->user()->id
                    ];
                    MealFood::create($data);
                    $meal->calorie_count += $food->calories;
                }
                if ($request->user()->role_id == 4 || $request->user()->role_id == 5) {
                    $meal->approval = 1;
                    $meal->update();
                    return $this->success(_("Created Successfully"), $meal, 200);
                }
                $meal->update();
                $message = 'Meal Created Successfully . Awaiting Approval';
                $data = [
                    'meal' => $meal,
                    'food_list' => $food_result
                ];
                return $this->success(_($message), $meal, 200);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    // public function show(Request $request)
    // {
    //     $fields = Validator::make($request->only(['meal_id']) , [
    //         'meal_id' => 'required|integer'
    //     ]);
    //     if($fields->fails())
    //     {
    //         return $this->fail($fields->errors()->first(),401);
    //     }
    //     $fields = $fields->safe()->all();
    //     $meal = Meal::find($fields['meal_id']);
    //     return $this->success("Success" , $meal , 200);
    // }

    public function edit(Request $request,$id)
    {
        try {
            $user = User::find(Auth::id());
            $meal = Meal::find($id);
            if ($user->id == $meal->created_by || in_array($user->role_id , [4,5])) {
                $fields = Validator::make($request->only('type', 'description', 'food_ids'), [
                    'type' => 'string|nullable|in:Breakfast,Dinner,Snack,Lunch',
                    'food_ids' => 'required|string',
                    'description' => 'required|string'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                if ($meal->type != $fields['type']) {
                    $meal->type = $fields['type'];
                }
                if ($meal->description != $fields['description']) {
                    $meal->description = $fields['description'];
                }
                $food_list = json_decode($fields['food_ids']);
                $pre_food = [];
                $mealfood = $meal->mealfood;
                foreach ($food_list as $food_id) {
                    $food = Food::find($food_id);
                    $food_result[] = $food;
                    $mf = MealFood::query()
                        ->where('food_id', $food->id)
                        ->where('meal_id', $meal->id)
                        ->exists();
                    if ($mf == 0) {
                        MealFood::create([
                            'user_id' => Auth::id(),
                            'meal_id' => $meal->id,
                            'food_id' => $food->id
                        ]);
                        $meal->calorie_count += $food->calories;
                    }
                }
                foreach ($mealfood as $food) {
                    $pre_food[] = $food->food_id;
                }
                $delete_food = array_diff($pre_food, $food_list);
                foreach ($delete_food as $food_id) {
                    $mealfood = MealFood::query()
                        ->where('meal_id', $meal->id)
                        ->where('food_id', $food_id);
                    $mealfood->delete();
                    $food = Food::find($food_id);
                    $meal->calorie_count -= $food->calories;
                }
                $meal->update();
                $meal = Meal::find($meal->id);
                $message = 'Meal Edited Successfully';
                $data = [
                    'meal' => $meal,
                    'food_list' => $food_result
                ];
                return $this->success(_($message), $data, 200);
            } else {
                $message = 'Permission Denied. Not the owner';
                return $this->fail(_($message), 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }


    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());
            $meal = Meal::find($id);
            if (DietMeal::where('meal_id', $meal->id)->exists()) {
                return $this->fail("Can't Delete Meal Due to it being assigned to a/many Diets!", 400);
            }
            if (in_array($user->role_id, [4, 5]) || $user->id == $meal->user_id) {
                $meal->mealfood()->delete();
                $meal->delete();
                return $this->success('Meal Deleted Successfully', $meal, 200);
            }
            return $this->fail('Permission Denied. Not the owner', 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
