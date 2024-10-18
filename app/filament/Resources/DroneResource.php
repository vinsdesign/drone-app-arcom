<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DroneResource\Pages;
use App\Filament\Resources\DroneResource\RelationManagers;
use App\Models\Drone;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
class DroneResource extends Resource
{
    protected static ?string $model = Drone::class;
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Drone';

    protected static ?string $navigationIcon = 'heroicon-m-rocket-launch';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Overview')
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')->label('Name')
                            ->required()
                            ->maxLength(255)->columnSpan(1),
                        Forms\Components\TextInput::make('idlegal')->label('Legal ID')
                            ->required()
                            ->maxLength(255)->columnSpan(2),
                        Forms\Components\select::make('status')->label('Status')   
                            ->options([
                                'airworthy' => 'Airworthy',
                               'maintenance' => 'Maintenance',
                               'retired' => 'Retired',
                            ])
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('brand')->label('Brand')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')->label('Model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')->label('Type')
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
                    Forms\Components\Wizard\Step::make('Drone Details')
                    ->schema([
                        Forms\Components\Select::make('geometry')->label('Drone Geometry')
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
                        ->required(),
                        Forms\Components\TextInput::make('color')->label('Color')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('inventory_asset')->label('Inventory/Asset')
                            ->options([
                                'asset'=> 'Assets',
                                'inventory'=> 'Inventory',
                            ])
                            ->required(),
                        Forms\Components\Select::make('users_id')->label('Owner')
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
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('firmware_v')->label('Firmware version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_v')->label('Hardware Version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('propulsion_v')->label('Propulsion Version')
                            ->options([
                                'electric' => 'Electric',
                                'fuel'=> 'Fuel',
                                'hydrogen' => 'Hydrogen',
                                'hybrid' => 'Hybrid',
                                'turbine' => 'Turbine',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')->label('Description')
                            ->required()
                            ->maxLength(255)->columnSpanFull(),

                    ])->columns(3),
                    //and wizard 2
                    Forms\Components\Wizard\Step::make('connect')
                    ->schema([
                        Forms\Components\TextInput::make('serial_p')->label('Serial Printed')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('serial_i')->label('Serial Internal')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('flight_c')->label('Flight controller')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('remote_c')->label('Remote Controller')
                            ->required()
                            ->maxLength(255),
                            Forms\Components\TextInput::make('remote_cc')->label('Remote Controller2')
                            ->required()
                            ->maxLength(255)->columnSpan(2),
                        Forms\Components\TextInput::make('remote')->label('Remote ID')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('conn_card')->label('Connection Card')
                            ->required()
                            ->maxLength(255)->columnSpan(2),
                    ])->columns(3),
                    //and wizard 3
                ])->columnSpanFull(),
                //end wizard
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Drone Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')
                ->color(fn ($record) => match ($record->status){
                    'airworthy' => Color::Green,
                   'maintenance' =>Color::Red,
                   'retired' => Color::Zinc
                 })
                    ->searchable(),
                Tables\Columns\TextColumn::make('idlegal')->label('Legal ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')->label('Owners')
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('viewDrone')
                    ->action(function(Drone $record, $livewire){
                        $livewire->emit('viewDrone', $record->id);
                    }),
                Tables\Actions\EditAction::make(),
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
            TextEntry::make('name')->label('Name'),
            TextEntry::make('idlegal')->label('Legal ID'),
            TextEntry::make('status')->label('Status')
            ->color(fn ($record) => match ($record->status){
                'airworthy' => Color::Green,
               'maintenance' =>Color::Red,
               'retired' => Color::Zinc
             }),
            TextEntry::make('brand')->label('Brand'),
            TextEntry::make('model')->label('Model'),
            TextEntry::make('type')->label('Type'),
                ])->columns(3),
                Section::make('Drone Details')
                ->schema([
            TextEntry::make('geometry')->label('Drone Geometry'),
            TextEntry::make('color')->label('Color'),
            TextEntry::make('inventory_asset')->label('Inventory/Asset'),
            TextEntry::make('users.name')->label('Owner')
            ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                'tenant' => Auth()->user()->teams()->first()->id,
                'record' => $record->users_id,
            ]) : null)->color(Color::Blue),
            TextEntry::make('firmware_v')->label('Firmware Version'),
            TextEntry::make('hardware_v')->label('Hardware Version'),
            TextEntry::make('propulsion_v')->label('Propulsion Version'),
            TextEntry::make('description')->label('Description'),
                ])->columns(5),
                Section::make('Connect')
                ->schema([
                    TextEntry::make('serial_p')->label('Serial Printed'),
                    TextEntry::make('serial_i')->label('Serial Internal'),
                    TextEntry::make('flight_c')->label('Flight controller'),
                    TextEntry::make('remote_c')->label('Remote Controller'),
                    TextEntry::make('remote_cc')->label('Remote Controller2'),
                    TextEntry::make('remote')->label('Remote ID'),
                    TextEntry::make('conn_card')->label('Connection Card'),
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
