<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTimeZone
{
    public function handle(Request $request, Closure $next)
    {
        config(['app.timezone' => 'UTC']);  //default value
        if (($request->header("timeZone"))) {
            config(['app.timezone' => $request->header('timeZone')]); // if request have timeZone
        }
        return $next($request);
    }
}
