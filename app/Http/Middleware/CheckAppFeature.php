<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AppController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\GeneralTrait;

class CheckAppFeature
{
    use GeneralTrait;
    public function handle(Request $request, Closure $next)
    {
        //
        if ($request->user() && ($request->user()->role_id == 4 || $request->user()->role_id == 5))
            return $next($request);
        if (AppController::where(['id' => 1, 'is_active' => 0])->first())
            return $this->fail(__("messages.App is temporarily down"));
        //
        if (
            AppController::where(['id' => 2, 'is_active' => 0])->first() &&
            (Str::substr((string)$request->path(), 0, 9) == 'api/posts'
                || ($request->path() == 'api/search' && $request->filter == 'posts'))
        )
            return $this->fail(__("messages.Posts feature is temporarily down"));
        //
        if (
            AppController::where(['id' => 3, 'is_active' => 0])->first() &&
            ($request->is(['api/posts', 'api/posts/poll']) && $request->method() == 'POST')
        )
            return $this->fail(__("messages.Creating posts is temporarily off"));
        //
        if (
            AppController::where(['id' => 4, 'is_active' => 0])->first() &&
            ($request->is(['api']) && $request->method() == 'POST')
        )
            return $this->fail(__("messages.Creating new account is temporarily off"));
        //
        if (
            AppController::where(['id' => 5, 'is_active' => 0])->first() &&
            ($request->is(['api/food/create', 'api/meal/create', 'api/diet/create']) && $request->method() == 'POST')
        )
            return $this->fail(__("messages.Creating new diet or food or meal is temporarily off"));
        //
        if (
            AppController::where(['id' => 6, 'is_active' => 0])->first() &&
            ($request->is(['api/workout_categorie/create', 'api/workout/create', 'api/excersise/create']) && $request->method() == 'POST')
        )
            return $this->fail(__("messages.Creating new workout_categorie or workout or excersise is temporarily off"));
        return $next($request);
    }
}
