<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Diet;
use App\Models\DietSubscribe;
use App\Models\Follow;
use App\Models\Practice;
use App\Models\User;
use App\Models\Workout;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Callback;


class HomePageController extends Controller
{
    use GeneralTrait;
    public $default_sequence = ['Chest', 'Arms', 'Rest', 'Back', 'Legs', 'Rest', 'Stomach'];
    public function summary()
    {
        try {
            $burnt_calories = 0;
            $workouts_played = 0;
            $user = User::find(Auth::id());
            $weight = 0;
            $height = 0;
            $user->info->last()->height_unit == 'ft' ? $height = $user->info->last()->height * 30.48 : $height = $user->info->last()->height;
            $user->info->last()->weight_unit == 'lb' ? $weight = $user->info->last()->weight / 2.205 : $weight = $user->info->last()->weight;
            $bmi = 10000 * $weight / ($height * $height);
            if ($bmi < 18.5) $bmi = 'Underweight';
            else if ($bmi >= 18.5 && $bmi <= 24.9) $bmi = 'Normal Weight';
            else if ($bmi >= 25 && $bmi <= 29.9) $bmi = 'Overweight';
            else $bmi = 'Obesity';
            $fromDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
            $tillDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
            $practices = Practice::query()
                ->where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->startOfMonth()->subMonth()->toDateString())
                ->get();
            foreach ($practices as $practice) {
                $burnt_calories += $practice->summary_calories;
            }
            $workouts_played = Practice::where('created_at', '>=', Carbon::now()->startOfMonth()->subMonth()->toDateString())->where('user_id', Auth::id())->count();
            $current_diet = DietSubscribe::where('user_id', $user->id)->first();
            if($current_diet != null)
            {
                $current_diet = Diet::find($current_diet->diet_id)->only(['id','name']);
            }
            else
            {
                $current_diet = [
                    'id' => 0,
                    'name' => ''
                ];
            }
            //return response($current_diet);
            $data = [
                'BMI' => $bmi,
                'Workouts Played' => $workouts_played,
                'Calories Burnt' => $burnt_calories,
                'Current Diet' => $current_diet,
                'Weight' => (string) $weight
            ];
            return $this->success("Summary", $data, 200);
        } catch (Exception $exception) {
            return $this->fail($exception->getMessage(), 500);
        }
    }
}
