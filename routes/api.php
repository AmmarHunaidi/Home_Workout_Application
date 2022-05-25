<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

Route::group(['middleware' => ['apikey', 'json', 'lang']], function () {
    Route::get('/testlang', function () {
        return response()->json(['message' => __('messages.somthing went wrong'), 'local' => App::currentLocale()], 200);
    });
});
