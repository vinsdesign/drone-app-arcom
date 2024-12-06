<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlighLocationResource\Pages;
use App\Filament\Resources\FlighLocationResource\RelationManagers;
use App\Models\customer;
use App\Models\fligh_location;
use App\Models\Projects;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Dotswan\MapPicker\Fields\Map;
use Filament\Support\Colors\Color;
use App\Helpers\TranslationHelper;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\View as InfolistView;
use Filament\Forms\Components\Section as FormSection;

class FlighLocationResource extends Resource
{
    protected static ?string $model = fligh_location::class;
    
    protected static ?string $navigationIcon = 'heroicon-s-map-pin';
    // protected static?string $navigationGroup = 'Inventory';
    // protected static ?string $navigationLabel = 'Location';
    // protected static ?string $modelLabel = 'Locations';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $tenantRelationshipName = 'fligh_location';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->where('status_visible', '!=', 'archived')->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Locations');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Locations');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                //untuk tenancy
                Forms\Components\Hidden::make('teams_id')
                ->default(auth()->user()->teams()->first()->id ?? null),
                //end untuk tenancy
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Locations Overview'))
                    ->Schema([
                        Forms\Components\TextInput::make('name')
                        ->label(TranslationHelper::translateIfNeeded('Name')),
                        Forms\Components\Select::make('projects_id')
                        ->label(TranslationHelper::translateIfNeeded('Projects'))
                        // ->relationship('projects','case', function (Builder $query){
                        //     $currentTeamId = auth()->user()->teams()->first()->id;
                        //     $query->where('teams_id', $currentTeamId);
                        // }),  
                        ->options(function (callable $get) use ($currentTeamId) {
                            return projects::where('teams_id', $currentTeamId)
                            ->where('status_visible', '!=', 'archived')
                            ->pluck('case', 'id');
                        })
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $project = Projects::find($state);
                                $set('customers_id', $project ? $project->customers_id : null);
                                $set('customers_name', $project && $project->customers ? $project->customers->name : null);
                            } else {
                                $set('customers_id', null);
                                $set('customers_name', null);
                            }
                        })
                        ->reactive()
                        ->searchable(),  
                        // Forms\Components\Select::make('customers_id')
                        // // ->relationship('customers','name', function (Builder $query){
                        // //     $currentTeamId = auth()->user()->teams()->first()->id;
                        // //     $query->where('teams_id', $currentTeamId);
                        // // })  
                        // ->options(function (callable $get) use ($currentTeamId) {
                        //     return customer::where('teams_id', $currentTeamId)->pluck('name', 'id');
                        // })  
                        // ->label(TranslationHelper::translateIfNeeded('Customers'))
                        // ->searchable()
                        // ->columnSpanFull(),
                        Forms\Components\Hidden::make('customers_id') 
                            ->required(),
                        Forms\Components\TextInput::make('customers_name')
                            ->label(TranslationHelper::translateIfNeeded('Customers Name'))    
                            ->disabled()
                            ->afterStateHydrated(function ($state, $component, $record) {
                                if ($record) {
                                    $customerId = \DB::table('fligh_locations')
                                        ->where('id', $record->id)
                                        ->value('customers_id'); 

                                    if ($customerId) {
                                        $customerName = \DB::table('customers')
                                            ->where('id', $customerId)
                                            ->value('name'); 
                        
                                        $component->state($customerName);
                                    }
                                }
                            })
                            ->default(function (){
                                $currentTeam = auth()->user()->teams()->first();
                                return $currentTeam ? $currentTeam->id_customers  : null;
                            })
                            ->columnSpanFull(),
                        Forms\Components\TextArea::make('description')
                        ->label(TranslationHelper::translateIfNeeded('Descriptions'))
                        ->columnSpanFull(),
                    ])->columns(2),
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Locations Address'))
                    ->Schema([
                        Forms\Components\TextInput::make('address')
                        ->label(TranslationHelper::translateIfNeeded('Address'))    
                        ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                        ->label(TranslationHelper::translateIfNeeded('City')),
                        Forms\Components\TextInput::make('pos_code')
                        ->label(TranslationHelper::translateIfNeeded('Postal Code'))
                        ->numeric(),
                        Forms\Components\TextInput::make('state')
                        ->label(TranslationHelper::translateIfNeeded('State')),
                        Forms\Components\TextInput::make('country')
                        ->label(TranslationHelper::translateIfNeeded('Country')),

                        //map picker
                        FormSection::make()
                        ->description('')
                        ->schema([
                            //maps
                            Map::make('locationMaps')
                                ->label('Maps')
                                ->columnSpanFull()
                                ->defaultLocation(latitude: -8.592113191530379 , longitude: 115.27542114257814)
                                ->afterStateHydrated(function ($state, $record, callable $set) {
                                    if ($record) {
                                        $set('locationMaps', [
                                            'lat' => $record->latitude,
                                            'lng' => $record->longitude,
                                        ]);
                                    }
                                })
                                ->afterStateUpdated(function ($state, callable $set): void {
                                    $set('latitude',  $state['lat']);
                                    $set('longitude', $state['lng']);
                                    // $set('geojson',   json_encode($state['geojson']));
                                })
                                // ->afterStateHydrated(function ($state, $record, $set): void {
                                //     if ($record) {
                                //         $set('location', [
                                //             'lat'     => $record->latitude,
                                //             'lng'     => $record->longitude,
                                //             // 'geojson' => $record->description ? json_decode(strip_tags($record->description), true) : null,
                                //         ]);
                                //     }
                                // })
                                ->extraStyles([
                                    'min-height: 50vh',
                                    'border-radius: 20px'
                                ])
                                ->liveLocation(true, true, 5000)
                                ->showMarker()
                                ->showFullscreenControl()
                                ->showZoomControl()
                                ->draggable()
                                ->rangeSelectField('altitude'),

                            Forms\Components\TextInput::make('latitude')
                            ->label(TranslationHelper::translateIfNeeded('Latitude'))->numeric(),
                            Forms\Components\TextInput::make('longitude')
                            ->label(TranslationHelper::translateIfNeeded('Longitude'))->numeric(),
                            Forms\Components\TextInput::make('altitude')
                            ->label(TranslationHelper::translateIfNeeded('Altitude'))
                            ->numeric()
                            ->columnSpan('1'),
                        ])


                        // Map::make('location')
                        // ->liveLocation(true, true, 5000)
                        // ->showMarker()
                        // ->markerColor("#22c55eff")
                        // ->showFullscreenControl()
                        // ->showZoomControl()
                        // ->draggable()
                        // ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                        // ->zoom(15)
                        // ->detectRetina()
                        // ->showMyLocationButton()
                        // ->geoMan(true)
                        // ->geoManEditable(true)
                        // ->geoManPosition('topleft')
                        // ->drawCircleMarker()
                        // ->rotateMode()
                        // ->drawMarker()
                        // ->drawPolygon()
                        // ->drawPolyline()
                        // ->drawCircle()
                        // ->dragMode()
                        // ->cutPolygon()
                        // ->editPolygon()
                        // ->deleteLayer()
                        // ->setColor('#3388ff')
                        // ->setFilledColor('#cad9ec')
                        
                    ])->columns(2)
                ])->columnSpanFull()

            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
                    //edit query untuk action shared un-shared
            ->modifyQueryUsing(function (Builder $query) {
                $userId = auth()->user()->id;
        
                if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
                    return $query;
                }else{
                    $query->where(function ($query) use ($userId) {
                        $query->where('shared', 1);
                    });
                    return $query;
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Location Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('projects.case')
                ->label(TranslationHelper::translateIfNeeded('Project Case'))
                ->url(fn($record)  =>  $record->projects_id ? route('filament.admin.resources.projects.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->projects_id,
                ]) : null)->color(Color::Blue)
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.name')
                ->label(TranslationHelper::translateIfNeeded('Customers Name'))
                ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->customers_id,
                ]) : null)->color(Color::Blue)
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_flights')
                    ->label(TranslationHelper::translateIfNeeded('Total Flights'))    
                        ->getStateUsing(function ($record) {
                            $totalFlights = $record->fligh()->count();
                            $TranslateText = TranslationHelper::translateIfNeeded('Flights');
                            return "{$totalFlights} {$TranslateText}";
                        })
                        ->html(),
                Tables\Columns\TextColumn::make('address')
                    ->label(TranslationHelper::translateIfNeeded('Address'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                ->label(TranslationHelper::translateIfNeeded('City'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('pos_code')
                ->label(TranslationHelper::translateIfNeeded('Postal Code'))
                    ->searchable(),
                // Tables\Columns\TextColumn::make('state')
                // ->label(TranslationHelper::translateIfNeeded('State'))
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('country')
                // ->label(TranslationHelper::translateIfNeeded('Country'))
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('latitude')
                // ->label(TranslationHelper::translateIfNeeded('Latitude'))
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('longitude')
                // ->label(TranslationHelper::translateIfNeeded('Longitude'))
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('altitude')
                // ->label(TranslationHelper::translateIfNeeded('Altitude'))
                //     ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_visible')
                ->label('')
                ->options([
                    'current' => 'Current',
                    'archived' => 'Archived',
                ])
                ->default('current'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                        // Tables\Actions\ViewAction::make(),
                        Tables\Actions\Action::make('views')  
                            ->action(function ($record) {
                                session(['location_id' => $record->id]);
                                return redirect()->route('flight-location', ['location_id' => $record->id]);
                            })
                            ->label(TranslationHelper::translateIfNeeded('View'))
                            ->icon('heroicon-s-eye'),
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                        Tables\Actions\Action::make('Archive')->label(TranslationHelper::translateIfNeeded('Archive'))
                            ->hidden(fn ($record) => $record->status_visible == 'archived')
                                    ->action(function ($record) {
                                    $record->update(['status_visible' => 'archived']);
                                    Notification::make()
                                    ->title(TranslationHelper::translateIfNeeded('Status Updated'))
                                    ->body(TranslationHelper::translateIfNeeded("Status successfully changed."))
                                    ->success()
                                    ->send();
                                })->icon('heroicon-s-archive-box-arrow-down'),
                        Tables\Actions\Action::make('Un-Archive')->label(TranslationHelper::translateIfNeeded(' Un-Archive'))
                            ->hidden(fn ($record) => $record->status_visible == 'current')
                                    ->action(function ($record) {
                                    $record->update(['status_visible' => 'current']);
                                    Notification::make()
                                    ->title(TranslationHelper::translateIfNeeded('Status Updated'))
                                    ->body(TranslationHelper::translateIfNeeded("Status successfully changed."))
                                    ->success()
                                    ->send();
                                })->icon('heroicon-s-archive-box'),
                        //Shared action
                        Tables\Actions\Action::make('Shared')->label(TranslationHelper::translateIfNeeded('Shared'))
                        ->hidden(fn ($record) => 
                        ($record->shared == 1) ||
                        !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) && 
                        ($record->users_id != Auth()->user()->id))

                        ->action(function ($record) {
                            $record->update(['shared' => 1]);
                            Notification::make()
                            ->title(TranslationHelper::translateIfNeeded('Shared Updated'))
                            ->body(TranslationHelper::translateIfNeeded("Shared successfully changed."))
                            ->success()
                            ->send();
                        })->icon('heroicon-m-share'),
                    //Un-Shared action
                    Tables\Actions\Action::make('Un-Shared')->label(TranslationHelper::translateIfNeeded('Un-Shared'))
                        ->hidden(fn ($record) => 
                        ($record->shared == 0) ||
                        !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user')))&&
                        ($record->users_id != Auth()->user()->id))
                        ->action(function ($record) {
                            $record->update(['shared' => 0]);
                            Notification::make()
                            ->title(TranslationHelper::translateIfNeeded('Un-Shared Updated '))
                            ->body(TranslationHelper::translateIfNeeded("Un-Shared successfully changed."))
                            ->success()
                            ->send();
                        })->icon('heroicon-m-share'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist

        ->schema([
            Section::make(TranslationHelper::translateIfNeeded('Locations Overview'))
            ->schema([
                Group::make([
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('Location Name')),
                    TextEntry::make('projects.case')->label(TranslationHelper::translateIfNeeded('Project Case'))
                        ->url(fn($record)  =>  $record->projects_id ? route('filament.admin.resources.projects.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->projects_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('customers.name')->label(TranslationHelper::translateIfNeeded('Customers Name'))
                        ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->customers_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('description')->label(TranslationHelper::translateIfNeeded('Description'))
                ]),
                Group::make([
                    InfolistView::make('component.location.maps-view-location'),
                ])->columnSpan(2)
            ])->columns(3),
            Section::make(TranslationHelper::translateIfNeeded('Locations Address'))
            ->schema([
                TextEntry::make('address')->label(TranslationHelper::translateIfNeeded('Address')),
                TextEntry::make('city')->label(TranslationHelper::translateIfNeeded('City')),
                TextEntry::make('pos_code')->label(TranslationHelper::translateIfNeeded('Postal Code')),
                TextEntry::make('state')->label(TranslationHelper::translateIfNeeded('State')),
                TextEntry::make('country')->label(TranslationHelper::translateIfNeeded('Country')),
                TextEntry::make('latitude')->label(TranslationHelper::translateIfNeeded('Latitude')),
                TextEntry::make('longitude')->label(TranslationHelper::translateIfNeeded('Longitude')),
                TextEntry::make('altitude')->label(TranslationHelper::translateIfNeeded('Altitude'))
            ])->columns(4),
            Section::make('')
                ->schema([
                    InfolistView::make('component.flight-location'),
                ])

        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlighLocations::route('/'),
            'create' => Pages\CreateFlighLocation::route('/create'),
            'edit' => Pages\EditFlighLocation::route('/{record}/edit'),
            'view' => Pages\ViewFlighLocation::route('/{record}'),
        ];
    }
}
