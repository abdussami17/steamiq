<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Organization;

use App\Models\Student;
use App\Observers\EventObserver;
use App\Observers\OrganizationObserver;

use App\Observers\StudentObserver;
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
        Student::observe(StudentObserver::class);
        Organization::observe(OrganizationObserver::class);
        Event::observe(EventObserver::class);
        
    }
}
