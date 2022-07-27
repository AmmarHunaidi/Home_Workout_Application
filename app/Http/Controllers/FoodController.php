<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Requests\StoreFoodRequest;
use App\Http\Requests\UpdateFoodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;
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
        return Food::all(['id','name','calorie']);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->role_id == 3)
        {
            $fields = Validator::make($request->only('name','calories'), [
                'name' => 'required|string',
                'calories' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $fields['user_id'] = $request->user()->id;
            $food = Food::create($fields);
            $message = 'Food Created Successfully';
            return $this->success(_("messages." . $message), $food, 201);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFoodRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFoodRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function show(Food $food)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        if($request->user()->role_id == 3)
        {
            $fields = Validator::make($request->only('name','calories'),[
                'name' => 'string|nullable',
                'calories' => 'integer|nullable',
                'food_id' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $food = Food::find($fields->food_id);
            if($request->user()->id == $food->user_id){
                if($fields['name']!=null) $food->name = $fields['name'];
                if($fields['calories']!=null) $food->calories = $fields['calories'];
                $food->update();
                $message = 'Food Edited Successfully';
                return $this->success(_("message." . $message),$food,201);
            }
            $message = 'Permission Denied. Not the owner';
            return $this->fail(_('message.' . $message),400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFoodRequest  $request
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFoodRequest $request, Food $food)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->user()->role_id == 3)
        {
            $fields = Validator::make($request->only('food_id'),[
                'food_id' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $food = Food::find($fields->food_id);
            if($request->user()->id == $food->user_id){
                $food->delete();
                $message = 'Food Deleted Successfully';
                return $this->success(_("message." . $message),$food,201);
            }
            $message = 'Permission Denied. Not the owner';
            return $this->fail(_('message.' . $message),400);
        }
    }
}
