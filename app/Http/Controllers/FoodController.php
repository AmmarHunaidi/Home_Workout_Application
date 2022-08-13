<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Requests\StoreFoodRequest;
use App\Http\Requests\UpdateFoodRequest;
use App\Models\MealFood;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FoodController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        try {
            return $this->success(__("messages.Food List Returned Successfully"), Food::where('approval', 1)->get(['id', 'name', 'description', 'calories', 'food_image_url'])->map(function ($data) {
                if (!$data->description) {
                    $data->description = '';
                }
                $data->food_image_url = 'storage/images/food/' . $data->food_image_url;
                return $data;
            }), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            if ($request->user()->role_id == 3 || $request->user()->role_id == 4 || $request->user()->role_id == 5) {
                $fields = Validator::make($request->only(['name', 'calories', 'description', 'food_image']), [
                    'name' => 'required|string',
                    'calories' => 'required|integer',
                    'description' => 'nullable|string',
                    'food_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp'
                ]);
                if ($fields->fails()) {
                    error_log($fields->errors());
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                $fields['user_id'] = $request->user()->id;
                $food = Food::create($fields);
                if ($request->hasFile('food_image')) {
                    $destination_path = 'public/images/food';
                    $image = $request->file('food_image');
                    $randomString = Str::random(30);
                    $image_name = $food->id . '/' . $randomString . $image->getClientOriginalName();
                    $path = $image->storeAs($destination_path, $image_name);
                    $food->food_image_url = $image_name;
                }
                $food->update();
                return $this->success(__('messages.Food Created Successfully'), $food, 201);
            } else {
                return $this->fail(__("messages.Permision Denied!"), 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
    public function show($id)
    {
        try {
            $food = Food::find($id);
            return $this->success(__("messages.Food Returned Successfully"), $food, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
                $fields = Validator::make($request->only('name', 'calories', 'food_image', 'description'), [
                    'name' => 'string',
                    'calories' => 'integer',
                    'description' => 'string',
                    'food_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                $food = Food::find($id);
                $user = User::find(Auth::id());
                if ($user->id == $food->user_id || in_array($user->id , [4,5])) {
                    if ($fields['name'] != $food->name) $food->name = $fields['name'];
                    if ($fields['calories'] != $food->calories) $food->calories = $fields['calories'];
                    if ($fields['description'] != $food->description) $food->description = $fields['description'];
                    if ($request->hasFile('food_image')) {
                        if ($food->food_image_url != "Default/default.jpg") {
                            Storage::delete('public/images/food/' . $food->food_image_url);
                        }
                        $destination_path = 'public/images/food';
                        $image = $request->file('food_image');
                        $randomString = Str::random(30);
                        $image_name = $food->id . '/' . $randomString . $image->getClientOriginalName();
                        $path = $image->storeAs($destination_path, $image_name);
                        $food->food_image_url = $image_name;
                    }
                    $food->update();
                    return $this->success(__("messages.Food Edited Successfully"), $food, 200);
                }
                return $this->fail(__('messages.Permission Denied!'), 401);
            } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());
            $food = Food::find($id);
            if (in_array($user->role_id, [4, 5])) {
                if(MealFood::where('food_id',$food->id)->exists() == true)
                {
                    return $this->fail(__("messages.Can't delete food due to it being assigned too one or more meals!"));
                }
                $food->delete();
                return $this->success(__('messages.Food Deleted Successfully'), $food, 200);
            }
            return $this->fail(__('messages.Permission Denied'), 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
