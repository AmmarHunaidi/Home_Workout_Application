<?php

namespace App\Jobs;

use App\Models\Practice;
use App\Traits\EmailTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MonthlySummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,EmailTrait;

    public $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->users as $user)
        {
            $practices = Practice::query()
                ->where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth()->toDateString())
                ->get();
            $calories = 0;
            foreach ($practices as $practice) {
                $calories += $practice->summary_calories;
            }
            $workout_count = $practices->count();
            $this->sendMonthlySummary($user->f_name , $calories , $user->email , $workout_count);
        }
    }
}
