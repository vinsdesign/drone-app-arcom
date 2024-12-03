<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DroneResource\Pages;
use App\Filament\Resources\DroneResource\RelationManagers;
use App\Models\Drone;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\View;
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
use App\Helpers\TranslationHelper;
use Filament\Infolists\Components\View as InfolistView;

class DroneResource extends Resource
{
    protected static ?string $model = Drone::class;
    // protected static ?string $navigationGroup = 'Inventory';
    // protected static ?string $navigationLabel = 'Drone';
    protected static ?string $navigationIcon = 'heroicon-m-rocket-launch';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;
    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Drone');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Drone');
    }

    public static function form(Form $form): Form
    {
        $cloneId = request()->query('clone');
        $defaultData = [];

        if ($cloneId) {
            $record = Drone::find($cloneId);
            if ($record) {
                $defaultData = $record->toArray();
                $defaultData['name'] = $record->name . ' - CLONE';
            }
        }
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Overview'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                        ->label(TranslationHelper::translateIfNeeded('Name'))
                            ->required()
                            ->maxLength(255)->columnSpan(1)
                            ->default($defaultData['name'] ?? null),
                        Forms\Components\TextInput::make('idlegal')
                        ->label(TranslationHelper::translateIfNeeded('Legal ID'))
                            ->required()
                            ->maxLength(255)->columnSpan(2)
                            ->default($defaultData['idlegal'] ?? null),
                        Forms\Components\select::make('status')
                        ->label(TranslationHelper::translateIfNeeded('Status'))  
                            ->options([
                                'airworthy' => 'Airworthy',
                               'maintenance' => 'Maintenance',
                               'retired' => 'Retired',
                            ])
                            ->required()
                            ->columnSpanFull()
                            ->default($defaultData['status'] ?? null),
                        Forms\Components\TextInput::make('brand')
                        ->label(TranslationHelper::translateIfNeeded('Brand'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['brand'] ?? null),
                        Forms\Components\TextInput::make('model')
                        ->label(TranslationHelper::translateIfNeeded('Model'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['model'] ?? null),
                        Forms\Components\Select::make('type')
                        ->label(TranslationHelper::translateIfNeeded('Type')) 
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
                        ])->searchable()->required()
                        ->default($defaultData['type'] ?? null),
                    ])->columns(3),
                    //and wizard 1
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Drone Details'))
                    ->schema([
                        Forms\Components\Select::make('geometry')
                        ->label(TranslationHelper::translateIfNeeded('Drone Geometry'))
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
                        ->required()->searchable()
                        ->default($defaultData['geometry'] ?? null),
                        Forms\Components\TextInput::make('color')
                        ->label(TranslationHelper::translateIfNeeded('Color'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['color'] ?? null),
                        Forms\Components\Select::make('inventory_asset')
                        ->label(TranslationHelper::translateIfNeeded('Inventory/Asset'))
                            ->options([
                                'asset'=> 'Assets',
                                'inventory'=> 'Inventory',
                            ])
                            ->required()
                            ->default($defaultData['inventory_asset'] ?? null),
                        Forms\Components\Select::make('users_id')
                        ->label(TranslationHelper::translateIfNeeded('Owner'))
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
                            ->columnSpanFull()
                            ->default($defaultData['users_id'] ?? null),
                        Forms\Components\TextInput::make('firmware_v')
                        ->label(TranslationHelper::translateIfNeeded('Firmware Version'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['firmware_v'] ?? null),
                        Forms\Components\TextInput::make('hardware_v')
                        ->label(TranslationHelper::translateIfNeeded('Hardware Version'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['hardware_v'] ?? null),
                        Forms\Components\Select::make('propulsion_v')
                        ->label(TranslationHelper::translateIfNeeded('Propulsion Version'))
                            ->options([
                                'electric' => 'Electric',
                                'fuel'=> 'Fuel',
                                'hydrogen' => 'Hydrogen',
                                'hybrid' => 'Hybrid',
                                'turbine' => 'Turbine',
                            ])
                            ->required()
                            ->default($defaultData['propulsion_v'] ?? null),
                        //max_Flight_Distance
                        Forms\Components\TextInput::make('max_flight_time')
                        ->label(TranslationHelper::translateIfNeeded('Max Flight Time'))
                        ->placeholder('hh:mm:ss')
                        ->extraAttributes([
                            'oninput' => "this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');", 
                            'placeholder' => 'HH:mm:ss'
                        ])->default('00:00:00')
                        ->default($defaultData['max_flight_time'] ?? null),
                        //initial_Flight
                        Forms\Components\TextInput::make('initial_flight')
                        ->label(TranslationHelper::translateIfNeeded('Initial Flight'))
                        ->numeric()
                        ->default($defaultData['initial_flight'] ?? null),
                        //initial FLight Time
                        Forms\Components\TextInput::make('initial_flight_time')
                        ->label(TranslationHelper::translateIfNeeded('Initial Flight Time'))
                        ->placeholder('hh:mm:ss')
                        ->extraAttributes([
                            'oninput' => "this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');", 
                            'placeholder' => 'HH:mm:ss'
                            
                        ])->default('00:00:00')
                        ->default($defaultData['initial_flight_time'] ?? null),
                        Forms\Components\Textarea::make('description')
                        ->label(TranslationHelper::translateIfNeeded('Description'))
                            ->required()
                            ->maxLength(255)->columnSpanFull()
                            ->default($defaultData['description'] ?? null),

                    ])->columns(3),
                    //and wizard 2
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Connect'))
                    ->schema([
                        Forms\Components\TextInput::make('serial_p')
                        ->label(TranslationHelper::translateIfNeeded('Serial Printed'))
                            ->required()
                            ->default($defaultData['serial_p'] ?? null),
                        Forms\Components\TextInput::make('serial_i')
                        ->label(TranslationHelper::translateIfNeeded('Serial Internal'))
                            ->required()
                            ->default($defaultData['serial_i'] ?? null),
                        Forms\Components\TextInput::make('flight_c')
                        ->label(TranslationHelper::translateIfNeeded('Flight Controller'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['flight_c'] ?? null),
                        Forms\Components\TextInput::make('remote_c')
                        ->label(TranslationHelper::translateIfNeeded('Remote Controller'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['remote_c'] ?? null),
                            Forms\Components\TextInput::make('remote_cc')
                            ->label(TranslationHelper::translateIfNeeded('Remote Controller2'))
                            ->required()
                            ->maxLength(255)->columnSpan(2)
                            ->default($defaultData['remote_cc'] ?? null),
                        Forms\Components\TextInput::make('remote')
                        ->label(TranslationHelper::translateIfNeeded('Remote ID'))
                            ->required()
                            ->maxLength(255)
                            ->default($defaultData['remote'] ?? null),
                        Forms\Components\TextInput::make('conn_card')
                        ->label(TranslationHelper::translateIfNeeded('Connection Card'))
                            ->required()
                            ->maxLength(255)->columnSpan(2)
                            ->default($defaultData['conn_card'] ?? null),
                    ])->columns(3),
                    //and wizard 3
                ])->columnSpanFull(),
                //end wizard
            ]);
    }
    


    public static function table(Table $table): Table
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $table
        //edit query untuk action shared un-shared
            // ->modifyQueryUsing(function (Builder $query) {
            //     $userId = auth()->user()->id;
            //     if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
            //         return $query;
            //     }else{
            //         $query->where(function ($query) use ($userId) {
            //             $query->where('users_id', $userId);
            //         })
            //         ->orWhere(function ($query) use ($userId) {
            //             $query->where('users_id', '!=', $userId)->where('shared', 1);
            //         });
            //         return $query;
            //     }
            // })
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                return $query->accessibleBy($user);
            }) 
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Drone Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(TranslationHelper::translateIfNeeded('Status'))
                    ->formatStateUsing(function ($state) {
                        $colors = [
                            'airworthy' => '#28a745',
                            'maintenance' => 'red',
                            'retired' => 'gray',
                        ];
                
                        $color = $colors[$state] ?? 'gray';
                
                        return "<span style='
                                display: inline-block;
                                width: 10px;
                                height: 10px;
                                background-color: {$color};
                                border-radius: 50%;
                                margin-right: 5px;
                            '></span><span style='color: {$color};'>{$state}</span>";
                    })
                    ->html()
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('flight_date')
                    
                ->label(TranslationHelper::translateIfNeeded('Last Flight Date'))
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
                    
                        return "<div>({$totalFlights}) " . TranslationHelper::translateIfNeeded('Flights') ." <div class='inline-block border border-gray-300 dark:border-gray-600 px-2 py-1 rounded bg-gray-200 dark:bg-gray-700'>
                            <strong class='text-gray-800 dark:text-gray-200'>{$totalDuration}</strong> </div> <br> {$lastFlightDate}</div>";
                    })
                    ->sortable()
                    ->html(),
                Tables\Columns\TextColumn::make('idlegal')
                ->label(TranslationHelper::translateIfNeeded('Legal ID'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')
                ->label(TranslationHelper::translateIfNeeded('Owners'))
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
                ->label(TranslationHelper::translateIfNeeded('Filter by Status')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('showDrone')
                    ->url(fn ($record) => route('drone.statistik', ['drone_id' => $record->id]))->label(TranslationHelper::translateIfNeeded('View'))
                    ->icon('heroicon-s-eye'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\EditAction::make(),
                    //Shared action
                    Tables\Actions\Action::make('shared')->label(TranslationHelper::translateIfNeeded('Shared'))
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
                Tables\Actions\Action::make('add')
                    ->label(TranslationHelper::translateIfNeeded('Add Doc'))
                    ->icon('heroicon-s-document-plus')
                    ->modalHeading('Upload Drone Document')
                    ->modalButton('Save')
                    ->form(function ($record) {
                        return[
                        Forms\Components\TextInput::make('name')
                            ->label(TranslationHelper::translateIfNeeded('Name'))
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\DatePicker::make('expired_date')
                            ->label(TranslationHelper::translateIfNeeded('Expiration Date'))
                            ->required(),
                            
                        Forms\Components\TextArea::make('description')
                            ->label(TranslationHelper::translateIfNeeded('Notes'))
                            ->maxLength(255)
                            ->columnSpan(2),
                            
                        Forms\Components\TextInput::make('refnumber')
                            ->label(TranslationHelper::translateIfNeeded('Reference Number'))
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('type')
                            ->label(TranslationHelper::translateIfNeeded('Type'))
                            ->options([
                                'Regulatory_Certificate' => 'Regulatory Certificate',
                                'Registration' => 'Registration #',
                                'Insurance_Certificate' => 'Insurance Certificate',
                                'Checklist' => 'Checklist',
                                'Manual' => 'Manual',
                                'Other_Certification' => 'Other Certification',
                                'Safety_Instruction' => 'Safety Instruction',
                                'Other' => 'Other',
                            ])
                            ->required(),
                            
                        Forms\Components\FileUpload::make('doc')
                            ->label(TranslationHelper::translateIfNeeded('Upload File'))
                            ->acceptedFileTypes(['application/pdf']),
                            
                        Forms\Components\TextInput::make('external link')
                            ->label(TranslationHelper::translateIfNeeded('Or External Link'))
                            ->maxLength(255),
                            
                        Forms\Components\Hidden::make('users_id')
                            ->default(auth()->id()),

                        Forms\Components\Hidden::make('teams_id')
                            ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\Hidden::make('drone_id')
                            ->default($record->id),
                        ];})
                    
                    ->action(function (array $data) {
                        $document = \App\Models\Document::create([
                            'name' => $data['name'],
                            'expired_date' => $data['expired_date'],
                            'description' => $data['description'] ?? null,
                            'refnumber' => $data['refnumber'],
                            'type' => $data['type'],
                            'doc' => $data['doc'] ?? null,
                            'external link' => $data['external link'] ?? null,
                            'scope' => 'Drones',
                            'users_id' => $data['users_id'],
                            'teams_id' => $data['teams_id'],
                            'drone_id' => $data['drone_id']
                        ]);
                        if($document){
                            $document->teams()->attach($data['teams_id']);
                        }

                        Notification::make()
                        ->title(TranslationHelper::translateIfNeeded('Added Success'))
                        ->body(TranslationHelper::translateIfNeeded("Document added successfully with scope Drone!"))
                        ->success()
                        ->send();
                    }),
                    Tables\Actions\Action::make('clone')
                        ->label('Clone')
                        ->icon('heroicon-s-document-duplicate')
                        ->url(function ($record) {
                            return route('filament.admin.resources.drones.create', [
                                'tenant' => Auth()->user()->teams()->first()->id,
                                'clone' => $record->id,
                            ]);
                        }),
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
            Section::make(TranslationHelper::translateIfNeeded('Overview'))
                ->schema([       
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('Name')),
                    TextEntry::make('idlegal')->label(TranslationHelper::translateIfNeeded('Legal ID')),
                    TextEntry::make('status')->label(TranslationHelper::translateIfNeeded('Status'))
                        ->color(fn ($record) => match ($record->status) {
                            'airworthy' => Color::Green,
                            'maintenance' => Color::Red,
                            'retired' => Color::Zinc
                        }),
                    TextEntry::make('brand')->label(TranslationHelper::translateIfNeeded('Brand')),
                    TextEntry::make('model')->label(TranslationHelper::translateIfNeeded('Model')),
                    TextEntry::make('type')->label(TranslationHelper::translateIfNeeded('Type')),
                ])->columns(3),
            Section::make(TranslationHelper::translateIfNeeded('Drone Details'))
                ->schema([
                    TextEntry::make('geometry')->label(TranslationHelper::translateIfNeeded('Drone Geometry')),
                    TextEntry::make('color')->label(TranslationHelper::translateIfNeeded('Color')),
                    TextEntry::make('inventory_asset')->label(TranslationHelper::translateIfNeeded('Inventory/Asset')),
                    TextEntry::make('users.name')->label(TranslationHelper::translateIfNeeded('Owner'))
                        ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->users_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('firmware_v')->label(TranslationHelper::translateIfNeeded('Firmware Version')),
                    TextEntry::make('hardware_v')->label(TranslationHelper::translateIfNeeded('Hardware Version')),
                    TextEntry::make('propulsion_v')->label(TranslationHelper::translateIfNeeded('Propulsion Version')),
                    TextEntry::make('description')->label(TranslationHelper::translateIfNeeded('Description')),
                ])->columns(5),
            Section::make(TranslationHelper::translateIfNeeded('Connect'))
                ->schema([
                    TextEntry::make('serial_p')->label(TranslationHelper::translateIfNeeded('Serial Printed')),
                    TextEntry::make('serial_i')->label(TranslationHelper::translateIfNeeded('Serial Internal')),
                    TextEntry::make('flight_c')->label(TranslationHelper::translateIfNeeded('Flight controller')),
                    TextEntry::make('remote_c')->label(TranslationHelper::translateIfNeeded('Remote Controller')),
                    TextEntry::make('remote_cc')->label(TranslationHelper::translateIfNeeded('Remote Controller2')),
                    TextEntry::make('remote')->label(TranslationHelper::translateIfNeeded('Remote ID')),
                    TextEntry::make('conn_card')->label(TranslationHelper::translateIfNeeded('Connection Card')),
                ])->columns(4),
            Section::make('')
                ->schema([
                    InfolistView::make('component.tabViewResorce.drone-tab')
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
            'index' => Pages\ListDrones::route('/'),
            'create' => Pages\CreateDrone::route('/create'),
            'view' => Pages\ViewDrone::route('/{record}'),
            // 'widget' => Pages\WidgetDrone::route('/{record}/widget'),
            'edit' => Pages\EditDrone::route('/{record}/edit'),
        ];
    }
    
}
