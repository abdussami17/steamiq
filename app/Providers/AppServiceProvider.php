<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Player;
use App\Observers\EventObserver;
use App\Observers\OrganizationObserver;
use App\Observers\PlayerObserver;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);
        Player::observe(PlayerObserver::class);
        Organization::observe(OrganizationObserver::class);
        Event::observe(EventObserver::class);
        
    }
}
