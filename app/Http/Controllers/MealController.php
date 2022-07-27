<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Models\Food;
use App\Models\MealFood;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MealController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Meal::all(['id','type']);
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
            $fields = Validator::make($request->only('type','diet_id'), [
                'type' => 'required|string:breakfast,lunch,dinner,snack',
                'diet_id' => 'required|integer',
                'food_ids' => 'array|required',
                'day' => 'requried|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $fields['user_id'] = $request->user()->id;
            $meal = Meal::create($fields);
            foreach($fields['food_ids'] as $food_id)
            {
                $food = Food::where('id',$food_id);
                $data = [
                    'meal_id' => $meal->id,
                    'food_id' => $food->id,
                    'user_id' => $request->user()->primarykey
                ];
                MealFood::create($data);
            }
            $message = 'Food Created Successfully';
            return $this->success(_("messages." . $message), $meal, 201);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMealRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMealRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function show(Meal $meal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        if($request->user()->role_id == 3)
        {
            $fields = Validator::make($request->only('type','meal_id'),[
                'type' => 'string|nullable',
                'meal_id' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $meal = Meal::find($fields->meal_id);
            if($request->user()->id == $meal->user_id){
                if($fields['type']!=null) $meal->name = $fields['type'];
                $meal->update();
                $message = 'Meal Edited Successfully';
                return $this->success(_("message." . $message),$meal,201);
            }
            $message = 'Permission Denied. Not the owner';
            return $this->fail(_('message.' . $message),400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMealRequest  $request
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Meal  $meal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->user()->role_id == 3)
        {
            $fields = Validator::make($request->only('meal_id'),[
                'meal_id' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $meal = Meal::find($fields->meal_id);
            if($request->user()->id == $meal->user_id){
                $meal->delete();
                $message = 'Food Deleted Successfully';
                return $this->success(_("message." . $message),$meal,201);
            }
            $message = 'Permission Denied. Not the owner';
            return $this->fail(_('message.' . $message),400);
        }
    }
}
