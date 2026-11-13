<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\User;
use App\Observers\AccountSyncObserver;

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
        User::observe(AccountSyncObserver::class);

        $FWDEVPlayer = "FWDEVPlayer2.js";

        // Share the variable with all views
        View::share('FWDEVPlayer', $FWDEVPlayer);
    }
}
