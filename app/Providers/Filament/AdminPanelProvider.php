<?php

namespace App\Providers\Filament;;
use App\Filament\Pages\Report;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\DocumentResource;
use App\Filament\Resources\ListTeamResource;
use App\Filament\Resources\SettingsResource;
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
use Filament\Support\Facades\FilamentColor;
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
use Filament\Navigation\NavigationBuilder;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\ProjectsResource;
use App\Filament\Resources\BattreiResource;
use App\Filament\Resources\DroneResource;
use App\Filament\Resources\EquidmentResource;
use App\Filament\Resources\FlighLocationResource;
use App\Filament\Resources\KitsResource;
use App\Filament\Resources\FlighResource;
use App\Filament\Resources\PlannedMissionResource;
use App\Filament\Resources\IncidentResource;
use App\Filament\Resources\MaintenceResource;
use App\Filament\Resources\MaintenanceBatteryResource;
use App\Filament\Resources\ReportResource;
use App\Filament\Resources\ContactResource;
use App\Filament\Resources\TeamsListResource;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use TomatoPHP\FilamentSubscriptions\Filament\Resources\PlanResource;
use TomatoPHP\FilamentSubscriptions\Filament\Resources\SubscriptionResource;
use Filament\Pages\Dashboard;
use App\Helpers\TranslationHelper;

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
            ->breadcrumbs(false)
            // ->spa()
            //color
            ->sidebarCollapsibleOnDesktop()
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
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        ...Dashboard::getNavigationItems(),
                    ])
                    ->groups([
                    NavigationGroup::make(TranslationHelper::translateIfNeeded(''))
                        ->items([
                            ...CustomerResource::getNavigationItems(),
                            ...UserResource::getNavigationItems(),
                            ...DocumentResource::getNavigationItems(),
                            ...ProjectsResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Inventory'))
                        ->icon('heroicon-o-archive-box')
                        ->collapsed()
                        ->items([
                            ...BattreiResource::getNavigationItems(),
                            ...DroneResource::getNavigationItems(),
                            ...EquidmentResource::getNavigationItems(),
                            ...FlighLocationResource::getNavigationItems(),
                            ...KitsResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Flight'))
                        ->icon('heroicon-o-rocket-launch')
                        ->collapsed()
                        ->items([
                            ...FlighResource::getNavigationItems(),
                            ...PlannedMissionResource::getNavigationItems(),
                            ...IncidentResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Maintenance'))
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->collapsed()
                        ->items([
                            ...MaintenceResource::getNavigationItems(),
                            ...MaintenanceBatteryResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Report'))
                        ->icon('heroicon-o-document-text')
                        ->collapsed()
                        ->items([
                            ...ReportResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Contact'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->collapsed()
                        ->items([
                            ...ContactResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Filament Shield'))
                        ->icon('heroicon-o-shield-check')
                        ->collapsed()
                        ->items([
                            ...RoleResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Teams List'))
                        ->icon('heroicon-o-list-bullet')
                        ->collapsed()
                        ->items([
                            ...ListTeamResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Settings'))
                        ->icon('heroicon-o-cog')
                        ->collapsed()
                        ->items([
                            ...SettingsResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(TranslationHelper::translateIfNeeded('Payment'))
                        ->icon('heroicon-o-credit-card')
                        ->collapsed()
                        ->items([
                            ...PlanResource::getNavigationItems(),
                            ...SubscriptionResource::getNavigationItems()
                        ]),
                ]);
            })
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
