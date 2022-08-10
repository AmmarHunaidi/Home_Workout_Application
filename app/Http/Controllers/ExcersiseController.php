<?php

namespace App\Http\Controllers;

use App\Models\Excersise;
use App\Http\Requests\StoreExcersiseRequest;
use App\Http\Requests\UpdateExcersiseRequest;
use App\Models\ExcersiseMedia;
use App\Models\User;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpParser\JsonDecoder;

class ExcersiseController extends Controller
{
    use GeneralTrait;
    function index()
    {
        try {
            $result = [];
            $excersises = Excersise::all(['id', 'name', 'excersise_media_url', 'user_id', 'description']);
            foreach ($excersises as $excersise) {
                $excersise->user_id = User::find($excersise->user_id)->only(['id', 'f_name', 'l_name', 'prof_img_url']);
                $excersise->excersise_media_url = 'storage/images/excersise/' . $excersise->excersise_media_url;
                $result[] = $excersise;
            }
            return $this->success("Success", $result, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            return $this->success("Excersise", Excersise::find($id), 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            if (in_array($request->user()->role_id, [2, 4, 5])) {
                $fields = Validator::make($request->only(['name', 'burn_calories', 'excersise_media', 'description']), [
                    'name' => 'required|string',
                    'burn_calories' => 'required|string',
                    'description' => 'required|string',
                    'excersise_media' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,bmp'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                $fields = $fields->safe()->all();
                if (Excersise::where('name', $fields['name'])->exists()) {
                    return $this->fail('Name already taken!', 400);
                }
                $fields['burn_calories'] = (int) $fields['burn_calories'];
                $fields['user_id'] = $request->user()->id;
                $excersise = Excersise::create($fields);
                if ($request->hasFile('excersise_media')) {
                    $destination_path = 'public/images/excersise';
                    $image = $request->file('excersise_media');
                    $randomString = Str::random(30);
                    $image_name = $excersise->id . '/' . $randomString . $image->getClientOriginalName();
                    $path = $image->storeAs($destination_path, $image_name);
                    $excersise->excersise_media_url = $image_name;
                }
                $excersise->created_at = (string)Carbon::parse($excersise->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                $excersise->update();
                return $this->success("Created Successfully", $excersise, 201);
            } else {
                return $this->fail("Permission not allowed!", 400);
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
                $fields = Validator::make($request->only(['name', 'burn_calories', 'excersise_media', 'description']), [
                    'name' => 'string|required',
                    'burn_calories' => 'integer|required',
                    'description' => 'required|string',
                    'excersise_media' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp'
                ]);
                if ($fields->fails()) {
                    return $this->fail($fields->errors()->first(), 400);
                }
                if (!Excersise::where('id', $id)->exists()) {
                    return $this->fail("Not Found", 400);
                }
                $excersise = Excersise::find($id);
                $fields = $fields->safe()->all();;
                if ($excersise->user->id == $user->id || in_array(User::find(Auth::id())->role_id, [4, 5])) {
                    if (in_array($fields['name'], Excersise::where('name', '!=', $excersise->name)->get('name')->toArray())) {
                        return $this->fail("Name Already Exists", 400);
                    }
                    if ($fields['name'] != $excersise->name)
                        $excersise->name = $fields['name'];
                    if ($fields['burn_calories'] != $excersise->burn_calories)
                        $excersise->burn_calories = $fields['burn_calories'];
                    if ($fields['description'] != $excersise->description)
                        $excersise->description = $fields['description'];
                    if ($request->hasFile('excersise_media')) {
                        if ($excersise->excersise_media_url != "Default/1.jpg") {
                            Storage::delete('public/images/excersise/' . $excersise->excersise_media_url);
                        }
                        $destination_path = 'public/images/excersise';
                        $image = $request->file('excersise_media');
                        $randomString = Str::random(30);
                        $image_name = $excersise->id . '/' . $randomString . $image->getClientOriginalName();
                        $path = $image->storeAs($destination_path, $image_name);
                        $excersise->excersise_media_url = $image_name;
                    }
                    $excersise->updated_at = (string)Carbon::parse($excersise->created_at)->utcOffset((int)config('app.timeoffset'))->format('Y/m/d g:i A');
                    $excersise->update();
                    return $this->success("Successfully", $excersise, 200);
                }
                return $this->fail("Failed", 400);
            }
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());
            $excersise = Excersise::find($id);
            if (in_array($user->role_id, [4, 5]) || $excersise->user_id == Auth::id()) {
                Storage::delete('public/images/excersise/' . $excersise->excersise_media_url);
                $excersise->delete();
                return $this->success("Success", [], 200);
            }
            return $this->fail("Permission not allowed!", 400);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
