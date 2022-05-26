<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

trait EmailTrait
{
    protected function sendForgetPassword($token, $name, $email)
    {
        Mail::send('emails.' . App::currentLocale() . '.forgetPassword_Email', ['token' => $token, 'name' => $name], function ($msg) use ($email) {
            $msg->to($email);
            $msg->subject(__('messages.Reset Password Notification') . config('app.name'));
        });
    }
    protected function sendResetPasswordConfirm($user)
    {
        Mail::send('emails.' . App::currentLocale() . '.resetPasswordConfirm_Email', ['name' => $user->name, 'time' => Carbon::now()->format('Y-m-d H:i:s')], function ($msg) use ($user) {
            $msg->to($user->email);
            $msg->subject(__('messages.Reset Password confirmation') . config('app.name'));
        });
    }
}
// use App\Traits\EmailTrait; befor the controller class
// use EmailTrait; inside the controller class