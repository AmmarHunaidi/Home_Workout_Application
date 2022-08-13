<?php

namespace App\Jobs;

use App\Traits\NotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubscribedDietDeleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,NotificationTrait;

    public $diet_name , $users;

    public function __construct($diet_name , $users_mobile_tokens)
    {
        $this->diet_name = $diet_name;
        $this->users = $users_mobile_tokens;
    }


    public function handle()
    {
        $this->sendNotification($this->users , "Subscribed Diet Deleted" , "We would like to inform you that the diet you are subscribed to has been deleted from our application and removed from your subscribed diet page.");
    }
}
