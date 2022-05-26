<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\ForgotPasswordController;


Route::group(['middleware' => ['apikey', 'json', 'lang']], function () {
    Route::get('/testlang', function () {
        return response()->json(['message' => __('messages.somthing went wrong'), 'local' => App::currentLocale()], 200);
    });
});
//No token needed routes
Route::group(['middleware' => ['apikey', 'json', 'lang']], function () {
    //Forget Password
    Route::prefix('forgetpassword')->controller(ForgotPasswordController::class)->group(function () {
        Route::post('/', 'submitForgetPasswordForm');
        Route::post('/verify', 'verifytoken');
        Route::post('/reset', 'resetpassword');
    });
});
