<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

trait NotificationTrait
{
    protected function sendNotification($tokens = [], $title, $body, $page)
    {
        $SERVER_API_KEY = 'AAAAXSQRj3o:APA91bE53VhoxMn8WVSF4Vdbs3JPDXctWaT0IgJ2u40iMzzBSlXwpDCGdftymoM2IGwcNU_zbVnV2By1NzqSHQwrRECmgJCFvG7h5bXAjdAk3KjeZxylfbXKM8z0jbtIyTcBJOQZnHGl';
        foreach ($tokens as $token) {
            $data = [

                "registration_ids" => [
                    $token
                ],

                "notification" => [
                    // "csrf-token" => csrf_token(),
                    "title" => $title,

                    "body" => $body,

                    "page" => $page,

                    "sound" => "default"

                ],

            ];
            $dataString = json_encode($data);

            $headers = [
                'X-CSRF-TOKEN' => csrf_token(),

                'Authorization: key=' . $SERVER_API_KEY,

                'Content-Type: application/json',

            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
        }
        return true;
    }
}
