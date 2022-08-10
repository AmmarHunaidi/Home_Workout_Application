<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Requests\StoreFoodRequest;
use App\Http\Requests\UpdateFoodRequest;
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->success("Success", Food::where('approval', 1)->get(['id', 'name', 'description', 'calories', 'food_image_url'])->map(function ($data) {
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if ($request->user()->role_id == 3 || $request->user()->role_id == 4 || $request->user()->role_id == 5) {
                $fields = Validator::make($request->only(['name', 'calories', 'description', 'food_image']), [
                    'name' => 'required|string',
                    'calories' => 'required|integer',
                    'description' => 'nullable|string',
                    'food_image' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096'
                ]);
                if ($fields->fails()) {
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
                if ($request->user()->role_id == 4 || $request->user()->role_id == 5) {
                    $food->approval = 1;
                    $food->update();
                    return $this->success(_("Created Successfully"), $food, 201);
                }
                $food->update();
                $message = 'Food Created Successfully . Awaiting Approval';
                return $this->success(_("messages." . $message), $food, 201);
            } else {
                return $this->fail("Not a dietitian!", 401);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
    public function show($id)
    {
        try {
            $food = Food::find($id);
            return $this->success("Success", $food, 201);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->role_id == 4 || $request->user()->role_id == 5) {
                $fields = Validator::make($request->only('name', 'calories', 'food_image', 'description'), [
                    'name' => 'string|nullable',
                    'calories' => 'integer|nullable',
                    'description' => 'string|nullable',
                    'food_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                $food = Food::find($fields['food_id']);
                if ($request->user()->id == $food->user_id) {
                    if ($fields['name'] != null) $food->name = $fields['name'];
                    if ($fields['calories'] != null) $food->calories = $fields['calories'];
                    if ($fields['description'] != null) $food->description = $fields['description'];
                    if ($request->hasFile('food_image')) {
                        if ($food->food_image_url != "Default/2560px-Pipeline_OpenGL.svg.png") {
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
                    $message = 'Food Edited Successfully';
                    return $this->success(_("message." . $message), $food, 200);
                }
                $message = 'Permission Denied. Not the owner';
                return $this->fail(_('message.' . $message), 401);
            } else {
                return $this->fail("Not a dietitian!", 401);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());
            if (in_array($user->role_id, [4, 5])) {
                $food = Food::find($id);
                $food->delete();
                $message = 'Food Deleted Successfully';
                return $this->success(_("message." . $message), $food, 200);
            }
            return $this->fail(_('Permission Denied. Not the owner'), 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
