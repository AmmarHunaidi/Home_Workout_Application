<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageController extends Controller
{
    public function reccomendations(Request $request)
    {
        $user = User::find($request->user()->id);

        $last_practice = Practice::where('user_id',$user->id)->last();
        if($last_practice == null)
        {
            //default way or coach way
        }
        else
        {
            //go by the streak
        }
    }

    public function summary(Request $request)
    {
        //don't forget streak days workoed out back to back
        //get current month
        //summary this month only
        $user = User::find($request->user()->id);
        $current_month = Carbon::
        $practices = Practice::where('user_id',$user->id)
                             ->where('created_at')->;
        $calories_burnt_so_far = 0;
        foreach ($practices as $practice)
        {
            $calories_burnt_so_far += $practice->burnt_calories;
        }

    }

    public function monthly_summary()
    {

    }

    public function yearly_summary()
    {

    }
}
