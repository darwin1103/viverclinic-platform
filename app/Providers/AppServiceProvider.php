<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Carbon::setLocale('es');

        $this->registerPolicies();
        Gate::before(function (User $user, $ability) {
            if ($user->hasRole('SUPER_ADMIN')) {
                return true;
            }
        });

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        };

        Paginator::useBootstrapFive();

    }
}
