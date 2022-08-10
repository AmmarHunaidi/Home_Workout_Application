<?php

namespace App\Http\Controllers;

use App\Models\WorkoutCategorie;
use App\Http\Requests\StoreWorkoutCategorieRequest;
use App\Http\Requests\UpdateWorkoutCategorieRequest;
use App\Models\User;
use App\Traits\GeneralTrait;
use Database\Factories\WorkoutCategorieFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkoutCategorieController extends Controller
{
    use GeneralTrait;
    function index()
    {
        return $this->success("Success" , WorkoutCategorie::all(['id','name']) , 200);
    }

    function categories()
    {
        return $this->success("Success" , WorkoutCategorie::whereNotIn('name' , ['Recommended' , 'All'])->get(['id' , 'name']) , 200);
    }

    public function show($id)
    {
        return $this->success("Success" , WorkoutCategorie::find($id) , 200);
    }

    public function create(Request $request)
    {
        $user = User::find(Auth::id());
        if(in_array($user->role_id, [2,4,5])){
            $fields = Validator::make($request->only('name') ,[
                'name' => 'required|string'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $fields = $fields->safe()->all();
            $fields['name'] = strtolower($fields['name']);
            if (WorkoutCategorie::where('name',$fields['name'])->exists()) {
                return $this->fail("Name Already Exists", 400);
            }
            $fields['user_id'] = $request->user()->id;
            $categorie = WorkoutCategorie::create($fields);
            return $this->success("Success" , $categorie , 201);
        }
        return $this->fail("Permission Denied" , 400);
    }

    public function edit(Request $request,$id)
    {
        $user = User::find(Auth::id());
        $categorie = WorkoutCategorie::find($id);
        if(in_array($user->role_id, [4,5]) || $categorie->user_id == Auth::id()){
            $fields = Validator::make($request->only('name') ,[
                'name' => 'required|string'
            ]);
            if($fields->fails())
            {
                return $this->fail($fields->errors()->first(),400);
            }
            $fields = $fields->safe()->all();
            $fields['name'] = strtolower($fields['name']);
            //return response(WorkoutCategorie::where('name', '!=', $categorie->name)->pluck('name')->all());
            if (in_array($fields['name'], WorkoutCategorie::where('name', '!=', $categorie->name)->pluck('name')->all())) {
                return $this->fail("Name Already Exists", 400);
            }
            if($fields['name'] != $categorie->name) $categorie->name = $fields['name'];
            $categorie->update();
            return $this->success("Success" , $categorie , 201);
        }
        return $this->fail("Permission Denied" , 400);
    }

    public function destroy($id)
    {
        $user = User::find(Auth::id());
        $categorie = WorkoutCategorie::find($id);
        if(in_array($user->role_id, [4,5]) || $categorie->user_id == Auth::id()){
            //dont forget delte all children and children children
            $categorie->delete();
        }
    }
}
