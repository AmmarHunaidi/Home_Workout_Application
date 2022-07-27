<?php

namespace App\Http\Controllers;

use App\Models\Diet;
use App\Http\Requests\StoreDietRequest;
use App\Http\Requests\UpdateDietRequest;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DietController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Diet::all(['id','name','user_id','created_by']);
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
            $fields = Validator::make($request->only('name','user_id'), [
                'name' => 'required|string',
                'user_id' => 'required|integer'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $fields['created_by'] = $request->user()->id;
            $diet = Diet::create($fields);
            $message = 'MealFood Created Successfully';
            return $this->success(_("messages." . $message), $diet, 201);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDietRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDietRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Diet  $diet
     * @return \Illuminate\Http\Response
     */
    public function show(Diet $diet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Diet  $diet
     * @return \Illuminate\Http\Response
     */
    //check if user exists
    public function edit(Request $request)
    {
        if($request->user()->role_id == 3)
        {
            $fields = Validator::make($request->only('diet_id','name','user_id'),[
                'diet_id' => 'required|integer',
                'user_id' => 'integer|nullable',
                'name' => 'nullable|string'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $diet = Diet::find($fields->diet_id);
            if($request->user()->id == $diet->user_id){
                if($fields['name']!=null) $diet->name = $fields['name'];
                if($fields['user_id']!=null)
                {
                    if(User::find($fields['user_id'])!=null)
                    {
                        $diet->user_id = $fields['user_id'];
                    }
                    $message = "Deignated User doesn't exist";
                    return $this->fail(_('message.' . $message),401);
                }
                $diet->update();
                $message = 'Diet Edited Successfully';
                return $this->success(_("message." . $message),$diet,201);
            }
            $message = 'Permission Denied. Not the owner';
            return $this->fail(_('message.' . $message),400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDietRequest  $request
     * @param  \App\Models\Diet  $diet
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDietRequest $request, Diet $diet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Diet  $diet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Diet $diet)
    {
        //
    }
}
