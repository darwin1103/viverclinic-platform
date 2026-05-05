<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
            if ($user->hasRole(['SUPER_ADMIN', 'OWNER'])) {
                return true;
            }
        });

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        };

        Paginator::useBootstrapFive();

        User::observe(\App\Observers\UserObserver::class);
        \App\Models\ContractedTreatment::observe(\App\Observers\ContractedTreatmentObserver::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') ?: $request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        \Illuminate\Support\Facades\View::composer('components.admin.dashboard.header', function ($view) {
            if (!array_key_exists('branches', $view->getData())) {
                $view->with('branches', \App\Models\Branch::all());
            }
            if (!array_key_exists('selectedBranchID', $view->getData())) {
                $view->with('selectedBranchID', session('selected_branch_id', ''));
            }
        });
    }
}
