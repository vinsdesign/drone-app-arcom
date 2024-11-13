<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DroneResource\Pages;
use App\Filament\Resources\DroneResource\RelationManagers;
use App\Models\Drone;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Carbon\Carbon;
use Stichoza\GoogleTranslate\GoogleTranslate;
class DroneResource extends Resource
{
    protected static ?string $model = Drone::class;
    protected static ?string $navigationGroup = 'Inventory';
    // protected static ?string $navigationLabel = 'Drone';
    protected static ?string $navigationIcon = 'heroicon-m-rocket-launch';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;
    public static function getNavigationBadge(): ?string{
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Drone', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Drone', session('locale') ?? 'en');
    }
    // public static function getNavigationGroup(): string
    // {
    //     return GoogleTranslate::trans('Inventory', session('locale') ?? 'en');
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Overview', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255)->columnSpan(1),
                        Forms\Components\TextInput::make('idlegal')->label(GoogleTranslate::trans('Legal ID', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255)->columnSpan(2),
                        Forms\Components\select::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))   
                            ->options([
                                'airworthy' => 'Airworthy',
                               'maintenance' => 'Maintenance',
                               'retired' => 'Retired',
                            ])
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('brand')->label(GoogleTranslate::trans('Brand', session('locale') ?? 'en'))  
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en'))  
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')->label(GoogleTranslate::trans('Type', session('locale') ?? 'en')) 
                        ->options([
                            'aircraft' => 'Aircraft',
                            'autoPilot' => 'AutoPilot',
                            'boat' => 'Boat',
                            'fixed_wing' => 'Fixed-Wing',
                            'flight controller' => 'Flight Controller',
                            'flying-wings' => 'Flying-Wings',
                            'fpv' => 'FPV',
                            'hexsacopter' => 'Hexsacopter',
                            'home-made' => 'Home-Made',
                            'multi-rotors' => 'Multi-Rotors',
                            'quadcopter' => 'Quadcopter',
                            'rover' => 'Rover',
                            'rpa' => 'RPA',
                            'Submersible' => 'Submersible',
                        ])->searchable()->required(),
                    ])->columns(3),
                    //and wizard 1
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Drone Details', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\Select::make('geometry')->label(GoogleTranslate::trans('Drone Geometry', session('locale') ?? 'en'))
                        ->options([
                            'dual_rotor_coaxial' => 'Dual Rotor Coaxial',
                            'fixed_wing_1' => 'Fixed Wing 1',
                            'fixed_wing_2' => 'Fixed Wing 2',
                            'fixed_wing_3' => 'Fixed Wing 3',
                            'hexa_plus' => 'Hexa +',
                            'hexa_x' => 'Hexa x',
                            'octa_plus' => 'Octa +',
                            'octa_v' => 'Octa V',
                            'octa_x' => 'Octa X',
                            'quad_plus' => 'Quad +',
                            'quad_x' => 'Quad X',
                            'quad_x_dji' => 'Quad X DJI',
                            'single_rotor' => 'Single Rotor',
                            'tri' => 'Tri',
                            'vtol_1' => 'VTOL 1',
                            'vtol_2' => 'VTOL 2',
                            'vtol_3' => 'VTOL 3',
                            'vtol_4' => 'VTOL 4',
                            'x8_coaxial' => 'X8 Coaxial',
                            'x6_coaxial' => 'X6 Coaxial',
                        ])
                        ->required()->searchable(),
                        Forms\Components\TextInput::make('color')->label(GoogleTranslate::trans('Color', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('inventory_asset')->label(GoogleTranslate::trans('Inventory/Asset', session('locale') ?? 'en'))
                            ->options([
                                'asset'=> 'Assets',
                                'inventory'=> 'Inventory',
                            ])
                            ->required(),
                        Forms\Components\Select::make('users_id')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                            // ->relationship('users','name', function (Builder $query){
                            //     $currentTeamId = auth()->user()->teams()->first()->id;
                            //     $query->where('team_user.teams_id', $currentTeamId);
                            // })   
                            ->options(function () {
                                $currentTeamId = auth()->user()->teams()->first()->id; 
                        
                                return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_user.team_id', $currentTeamId); 
                                })->pluck('name', 'id'); 
                            }) 
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('firmware_v')->label(GoogleTranslate::trans('Firmware Version', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_v')->label(GoogleTranslate::trans('Hardware Version', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('propulsion_v')->label(GoogleTranslate::trans('Propulsion Version', session('locale') ?? 'en'))
                            ->options([
                                'electric' => 'Electric',
                                'fuel'=> 'Fuel',
                                'hydrogen' => 'Hydrogen',
                                'hybrid' => 'Hybrid',
                                'turbine' => 'Turbine',
                            ])
                            ->required(),
                        //max_Flight_Distance
                        Forms\Components\TextInput::make('max_flight_time')
                        // ->label('Max Flight Time')
                        ->label(GoogleTranslate::trans('Max Flight Time', session('locale') ?? 'en'))
                        ->placeholder('hh:mm:ss')
                        ->extraAttributes([
                            'oninput' => "this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');", 
                            'placeholder' => 'HH:mm:ss'
                        ])->default('00:00:00'),
                        //initial_Flight
                        Forms\Components\TextInput::make('initial_flight')->label(GoogleTranslate::trans('Initial Flight', session('locale') ?? 'en'))
                        ->numeric(),
                        //initial FLight Time
                        Forms\Components\TextInput::make('initial_flight_time')->label(GoogleTranslate::trans('Initial Flight Time', session('locale') ?? 'en'))
                        ->placeholder('hh:mm:ss')
                        ->extraAttributes([
                            'oninput' => "this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');", 
                            'placeholder' => 'HH:mm:ss'
                            
                        ])->default('00:00:00'),
                        Forms\Components\Textarea::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255)->columnSpanFull(),

                    ])->columns(3),
                    //and wizard 2
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Connect', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\TextInput::make('serial_p')->label(GoogleTranslate::trans('Serial Printed', session('locale') ?? 'en'))
                            ->required(),
                        Forms\Components\TextInput::make('serial_i')->label(GoogleTranslate::trans('Serial Internal', session('locale') ?? 'en'))
                            ->required(),
                        Forms\Components\TextInput::make('flight_c')->label(GoogleTranslate::trans('Flight Controller', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('remote_c')->label(GoogleTranslate::trans('Remote Controller', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                            Forms\Components\TextInput::make('remote_cc')->label(GoogleTranslate::trans('Remote Controller2', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255)->columnSpan(2),
                        Forms\Components\TextInput::make('remote')->label(GoogleTranslate::trans('Remote ID', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('conn_card')->label(GoogleTranslate::trans('Connection Card', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255)->columnSpan(2),
                    ])->columns(3),
                    //and wizard 3
                ])->columnSpanFull(),
                //end wizard
            ]);
    }
    
//edit query untuk action shared un-shared
    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->user()->id;
        $query = parent::getEloquentQuery();

        if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
            return $query;
        }else{
            $query->where(function ($query) use ($userId) {
                $query->where('users_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('users_id', '!=', $userId)->where('shared', 1);
            });
            return $query;
        }

    }

    public static function table(Table $table): Table
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(GoogleTranslate::trans('Drone Name', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                ->color(fn ($record) => match ($record->status){
                    'airworthy' => Color::Green,
                   'maintenance' =>Color::Red,
                   'retired' => Color::Zinc
                 })
                    ->searchable(),
                Tables\Columns\TextColumn::make('flight_date')
                    ->label(GoogleTranslate::trans('Last Flight Date', session('locale') ?? 'en'))
                    ->getStateUsing(function ($record) {
                        $flights = $record->fligh;
                        $totalFlights = $flights->count();
                    
                        $totalSeconds = 0;
                        foreach ($flights as $flight) {
                            $start = $flight->start_date_flight;
                            $end = $flight->end_date_flight;
                    
                            if ($start && $end) {
                                $totalSeconds += Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                            }
                        }
                    
                        $hours = floor($totalSeconds / 3600);
                        $minutes = floor(($totalSeconds % 3600) / 60);
                        $seconds = $totalSeconds % 60;
                        $totalDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    
                        $lastFlight = $flights->sortByDesc('start_date_flight')->first();
                        $lastFlightDate = optional($lastFlight)->start_date_flight ? $lastFlight->start_date_flight : '';
                    
                        return "<div>{$totalFlights} Flight(s) <div style='border: 1px solid #ccc; padding: 3px; display: inline-block; border-radius: 5px; background-color: #D4D4D4; '>
                            <strong>{$totalDuration}</strong> </div> <br> {$lastFlightDate}</div>";
                    })
                    ->sortable()
                    ->html(),
                Tables\Columns\TextColumn::make('idlegal')->label(GoogleTranslate::trans('Legal ID', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')->label(GoogleTranslate::trans('Owners', session('locale') ?? 'en'))
                ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->users_id,
                ]) : null)->color(Color::Blue)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('brand')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('model')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('serial_p')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('serial_i')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('flight_c')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('remote_c')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('remote_cc')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('inventory_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('inventory_asset')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('description')
                //     ->searchable(),

                // Tables\Columns\TextColumn::make('firmware_v')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('hardware_v')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('propulsion_v')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('color')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('remote')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('conn_card')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'airworthy' => 'Airworthy',
                   'maintenance' => 'Maintenance',
                   'retired' => 'Retired'
                ])
                ->label('Filter by Status'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('showDrone')
                    ->url(fn ($record) => route('drone.statistik', ['drone_id' => $record->id]))->label(GoogleTranslate::trans('View', session('locale') ?? 'en'))
                    ->icon('heroicon-s-eye'),
                    Tables\Actions\EditAction::make(),
                    //Shared action
                    Tables\Actions\Action::make('shared')->label('Shared')
                    ->hidden(fn ($record) => 
                    ($record->shared == 1) ||
                    !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) && 
                    ($record->users_id != Auth()->user()->id))
    
                    ->action(function ($record) {
                        $record->update(['shared' => 1]);
                        Notification::make()
                        ->title('Shared Updated')
                        ->body("Shared successfully changed.")
                        ->success()
                        ->send();
                    })->icon('heroicon-m-share'),
                //Un-Shared action
                Tables\Actions\Action::make('Un-Shared')->label('Un-Shared')
                    ->hidden(fn ($record) => 
                    ($record->shared == 0) ||
                    !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user')))&&
                    ($record->users_id != Auth()->user()->id))
                    ->action(function ($record) {
                        $record->update(['shared' => 0]);
                        Notification::make()
                        ->title('Un-Shared Updated ')
                        ->body("Un-Shared successfully changed.")
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

    //infolist
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        
        ->schema([
            Section::make('Overview')
                ->schema([
            TextEntry::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en')),
            TextEntry::make('idlegal')->label(GoogleTranslate::trans('Legal ID', session('locale') ?? 'en')),
            TextEntry::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
            ->color(fn ($record) => match ($record->status){
                'airworthy' => Color::Green,
               'maintenance' =>Color::Red,
               'retired' => Color::Zinc
             }),
            TextEntry::make('brand')->label(GoogleTranslate::trans('Brand', session('locale') ?? 'en')),
            TextEntry::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en')),
            TextEntry::make('type')->label(GoogleTranslate::trans('Type', session('locale') ?? 'en')),
                ])->columns(3),
                Section::make('Drone Details')
                ->schema([
            TextEntry::make('geometry')->label(GoogleTranslate::trans('Drone Geometry', session('locale') ?? 'en')),
            TextEntry::make('color')->label(GoogleTranslate::trans('Color', session('locale') ?? 'en')),
            TextEntry::make('inventory_asset')->label(GoogleTranslate::trans('Inventory/Asset', session('locale') ?? 'en')),
            TextEntry::make('users.name')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
            ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                'tenant' => Auth()->user()->teams()->first()->id,
                'record' => $record->users_id,
            ]) : null)->color(Color::Blue),
            TextEntry::make('firmware_v')->label(GoogleTranslate::trans('Firmware Version', session('locale') ?? 'en')),
            TextEntry::make('hardware_v')->label(GoogleTranslate::trans('Hardware Version', session('locale') ?? 'en')),
            TextEntry::make('propulsion_v')->label(GoogleTranslate::trans('Propulsion Version', session('locale') ?? 'en')),
            TextEntry::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en')),
                ])->columns(5),
                Section::make('Connect')
                ->schema([
                    TextEntry::make('serial_p')->label(GoogleTranslate::trans('Serial Printed', session('locale') ?? 'en')),
                    TextEntry::make('serial_i')->label(GoogleTranslate::trans('Serial Internal', session('locale') ?? 'en')),
                    TextEntry::make('flight_c')->label(GoogleTranslate::trans('Flight controller', session('locale') ?? 'en')),
                    TextEntry::make('remote_c')->label(GoogleTranslate::trans('Remote Controller', session('locale') ?? 'en')),
                    TextEntry::make('remote_cc')->label(GoogleTranslate::trans('Remote Controller2', session('locale') ?? 'en')),
                    TextEntry::make('remote')->label(GoogleTranslate::trans('Remote ID', session('locale') ?? 'en')),
                    TextEntry::make('conn_card')->label(GoogleTranslate::trans('Connection Card', session('locale') ?? 'en')),
                ])->columns(4),
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
            'index' => Pages\ListDrones::route('/'),
            'create' => Pages\CreateDrone::route('/create'),
            'view' => Pages\ViewDrone::route('/{record}'),
            // 'widget' => Pages\WidgetDrone::route('/{record}/widget'),
            'edit' => Pages\EditDrone::route('/{record}/edit'),
        ];
    }
    
}
