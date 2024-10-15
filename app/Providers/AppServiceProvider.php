<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentSubscriptions\Facades\FilamentSubscriptions;

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
        FilamentSubscriptions::register(
            \TomatoPHP\FilamentSubscriptions\Services\Contracts\Subscriber::make('Subscriber')
                ->name('User')
                ->model(\App\Models\User::class)
        );
    }
}
