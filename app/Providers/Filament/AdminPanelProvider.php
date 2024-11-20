<?php

namespace App\Providers\Filament;;
use App\Filament\Pages\Report;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\DocumentResource;
use App\Filament\Widgets\AStatsOverview;
use App\Filament\Widgets\FlightChart;
use App\Filament\Widgets\FlightDurationChart;
use App\Filament\Widgets\HeaderDasboard;
use App\Filament\Widgets\InventoryOverview;
use App\Filament\Widgets\InventoryStats;
use App\Models\Team;
use Auth;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Pages\Tenancy\EditTeamProfil;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Filament\Navigation\MenuItem;
use App\Livewire\MyCustomComponent;
use TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsProvider;
use TomatoPHP\FilamentSubscriptions\Filament\Pages\Billing;
use Filament\Facades\Filament;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            // ->spa()

            ->favicon(asset('asset/favicon.png'))
            ->tenantRoutePrefix('team')

            //subscribe
            ->plugin(\TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsPlugin::make())
            ->pages([
                Billing::class
            ])
            ->tenantBillingProvider(new FilamentSubscriptionsProvider())
            ->requiresTenantSubscription()

            //shield
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                
            ])
            ->plugin(
                BreezyCore::make()
                ->myProfile(
                    shouldRegisterUserMenu: true,
                    shouldRegisterNavigation: false,
                    navigationGroup: 'Settings',
                    hasAvatars: true,
                    slug: 'my-profile'
                    
    
                )
                ->myProfileComponents([MyCustomComponent::class])
            )
            //navigation Group
            ->navigationGroups([
                'master',
                'Inventory',
                'flight',
                'Maintenance',
                'Report',
                'Contact',
                'Filament Shield',
                'Teams List',
                'Payment',
            ])
            //end Navigation Group
            ->tenant(Team::class, ownershipRelationship: 'team')
            ->tenantRegistration(RegisterTeam::class)
            ->tenantProfile(EditTeamProfil::class)
            ->tenantMenuItems([
                'register' => MenuItem::make()->label('Register new team')->hidden(),
                'profile' => MenuItem::make()->label('Edit Team Profile')->visible(function () {
                    $roles = Auth::user()->roles()->pluck('name');
                    return $roles->contains('super_admin') || $roles->contains('panel_user');
                    }),
                // ...
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // HeaderDasboard::class,
                // AStatsOverview::class,
                // InventoryOverview::class,
                // InventoryStats::class,
                
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

            
            
    }
}
