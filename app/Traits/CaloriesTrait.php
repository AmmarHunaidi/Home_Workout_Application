<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;

trait CaloriesTrait
{
    protected function step($ca = 0, $user)
    {
        $info = $user->info()->get()->last();
        if ($user->gender == 'female')
            $ca = 0.87 * $ca;
        $weight = $info->weight;
        if ($info->weight_unit == 'lb') {
            $weight = $weight * 0.453;
        }
        if ($weight > 70) {
            $factor = (($weight - 60) / 10);
            $ca = $ca + $factor * (($ca * 17 / 100));
        }
        if ($weight < 45) {
            $factor = (($weight) / 10);
            $ca = $ca - $factor * (($ca * 17 / 100));
        }
        $age = Carbon::parse($user->birth_date)->age;
        if ($age > 50) {
            $ca = 0.947 * $ca;
        }
        return round($ca, 1);
    }

    protected function time($time = 0, $ca = 0, $user)
    {
        $info = $user->info()->get()->last();
        $age = Carbon::parse($user->birth_date)->age;
        $height = $info->height;
        if ($info->height_unit == 'ft') {
            $height = $height * 30.48;
        }
        $weight = $info->weight;
        if ($info->weight_unit == 'lb') {
            $weight = $weight * 0.453;
        }
        if ($user->gender == 'male') {
            $mfr = 66 + (13.7 * $weight) + (5 * $height) - (6.8 * $age);
        } elseif ($user->gender == 'female') {
            $mfr = 665 + (9.6 * $weight) + (1.8 * $height) - (4.7 * $age);
        }
        $ca = $mfr * ($ca / 2) / 24 * ($time / 60);
        if ($weight > 70) {
            $factor = (($weight - 60) / 10);
            $ca = $ca + $factor * (($ca * 17 / 100));
        }
        if ($weight < 45) {
            $factor = (($weight) / 10);
            $ca = $ca - $factor * (($ca * 17 / 100));
        }
        return round($ca, 1);
    }
}

// use App\Traits\CaloriesTrait; befor the controller class
// use CaloriesTrait; inside the controller class
