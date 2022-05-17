<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait GeneralTrait
{
    protected function success($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function fail($message, $status = 422)
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
        ], $status);
    }
}

// use App\Traits\GeneralTrait; befor the controller class
// use GeneralTrait; inside the controller class
