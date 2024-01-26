<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
       // Middleware `oauth.providers` middleware defined on $routeMiddleware above
       Route::group(['middleware' => 'oauth.providers'], function () {
          Passport::routes(function ($router) {
             return $router->forAccessTokens();
          });
       });

       //
       // Implicitly grant "Admin" role all permissions
       // This works in the app by using gate-related functions like auth()->user->can() and @can()
       Gate::before(function ($user, $ability) {
          if ($user->hasRole('admin')) {
             return true;
          }
       });
    }
}
