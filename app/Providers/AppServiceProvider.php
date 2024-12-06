<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentSubscriptions\Facades\FilamentSubscriptions;
use Illuminate\Support\Facades\Gate;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;
use TomatoPHP\FilamentSubscriptions\Filament\Resources\PlanResource;
use TomatoPHP\FilamentSubscriptions\Filament\Resources\SubscriptionResource;
use Filament\Support\Colors\Color;

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
        Gate::policy(\Spatie\Permission\Models\Role::class, \App\Policies\RolePolicy::class);

        //test env
        $appName = "DroneLogBook";
        if ($appName) {
            config(['app.name' => $appName]);
        }
        //end test untuk merubah nama aplikasi (khusus untuk Super admin

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar','en','fr', 'id', 'ko', 'ja', 'es'])
                ->visible(false)
                ->outsidePanelPlacement(Placement::TopRight)
                ->displayLocale('en')
                ->renderHook('panels::global-search.before');
        });
    }
}
