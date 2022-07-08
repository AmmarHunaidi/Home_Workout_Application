<?php

namespace App\Providers;

use App\Models\Follow;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addDays(7));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();

        //Gates
        Gate::define('Follow-Protection', function (User $user, User $following) {
            return $following->role_id === 2 || $following->role_id === 3;
        });
        Gate::define('Coach-Dietitian-Protection', function (User $user) {
            return $user->role_id === 2 || $user->role_id === 3;
        });
    }
}
