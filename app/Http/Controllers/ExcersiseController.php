<?php

namespace App\Http\Controllers;

use App\Models\Excersise;
use App\Http\Requests\StoreExcersiseRequest;
use App\Http\Requests\UpdateExcersiseRequest;
use App\Models\ExcersiseMedia;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\JsonDecoder;

class ExcersiseController extends Controller
{
    use GeneralTrait;
    function index()
    {
        return $this->success("Success",Excersise::all(['id' , 'name']) , 200);
    }

    public function show(Request $request)
    {
        $fields = $request->validate([
            'id' => 'required|integer'
        ]);
        return Excersise::find($fields['id']);
    }

    public function create(Request $request)
    {
        if($request->user()->role_id == 4)
        {
            $fields = $request->validate([
            'name'=>'required|string',
            'burn_calories' => 'required|string',
            'excersise_media' => 'image|mimes:jpg,png,jpeg,gif,svg,bmp|max:4096'
        ]);
        $fields['burn_calories'] = (int) $fields['burn_calories'];
        $fields['user_id'] = $request->user()->id;
        $excersise = Excersise::create($fields);
        $original_path = 'public/images/excersises/' . $excersise->id;
        Storage::makeDirectory($original_path);
        $image = $request->file('excersise_media');
        $randomString = Str::random(30);
        $image_name = $randomString . $image->getClientOriginalName();
        $path = $image->storeAs($original_path,$image_name);
        $data = [
            'excersise_id' => $excersise->id,
            'excersies_media_url' => $image_name,
            'user_id' => $request->user()->id
        ];
        $excersise_media = ExcersiseMedia::create($data);
        return $this->success("Created Successfully" , $excersise , 201);
        }
        else
        {
            return response('Not a Coach!!');
        }
    }

    public function edit(Request $request)
    {
        if($request->user()->role_id == 2)
        {
            $fields = $request->validate([
                'excersise_id' => 'integer|required',
                'name' => 'string',
                'burn_calories',

            ]);
            $excersise = Excersise::find($fields['excersise_id']);
            if($excersise->user->id == $request->user()->id)
            {
                if($fields['name'] != null)
                    $excersise->name = $fields['name'];
                $excersise->save();
                return response($excersise);
            }
            return response('fail');
        }
    }

    public function destroy(Request $request)
    {
        if($request->user()->role_id == 2)
        {
            $fields = $request->validate([
                'excersise_id' => 'required|integer'
            ]);
            $excersise = Excersise::find($fields['excersise_id']);
            if($excersise->user->id == $request->user()->id)
            {
                $excersise->delete();
                return response('Success');
            }
            return response('Fail');
        }
    }
}
