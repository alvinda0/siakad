<?php

namespace App\Providers;

use App\Models\KandidatProfile;
use App\Models\User;
use App\Observers\KandidatProfileObserver;
use App\Observers\UserObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        Paginator::defaultView('vendor.pagination.tailwind');

        // Daftarkan observer untuk activity log
        User::observe(UserObserver::class);
        KandidatProfile::observe(KandidatProfileObserver::class);
    }
}
