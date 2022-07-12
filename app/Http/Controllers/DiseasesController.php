<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;

class DiseasesController extends Controller
{
    use GeneralTrait;
    public function index()
    {
        return $this->success('ok', Disease::all(['id', 'name']));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only('name'), [
            'name' => ['required', 'min:2', 'max:50', 'string'],
        ]);
        if ($validator->fails())
            return $this->fail($validator->errors()->first(), 400);
    }

    public function show(Disease $disease)
    {
        //
    }

    public function update(Request $request, Disease $disease)
    {
        //
    }

    public function destroy(Disease $disease)
    {
        //
    }
}
