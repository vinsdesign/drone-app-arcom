<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquidmentResource\Pages;
use App\Filament\Resources\EquidmentResource\RelationManagers;
use App\Models\Equidment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Infolists\Components\Section;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquidmentResource extends Resource
{
    protected static ?string $model = Equidment::class;
    protected static ?string $navigationLabel = 'Equipment' ;
    protected static ?string $navigationIcon = 'heroicon-m-cube';
    protected static?string $navigationGroup = 'Inventory';

    protected static?string $modelLabel = 'Equipment';

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
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')->label('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\select::make('status')->label('Status')
                            ->options([
                                'airworthy' => 'Airworthy',
                                'maintenance' => 'Maintenance',
                                'retired' => 'Retired',
                            ])
                            ->required(),
                        Forms\Components\Select::make('inventory_asset')->label('Inventory / Asset')
                            ->options([
                                'asset'=> 'Assets',
                                'inventory'=> 'Inventory',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('serial')->label('Serial')
                            ->required()->columnSpan(2)
                            ->numeric(),
                        Forms\Components\select::make('type')->label('Type')
                            ->options([
                                'airframe' => 'Airframe',
                                'anenometer' => 'Anenometer',
                                'battery' => 'Battery',
                                'battery_charger' => 'Battery Charger',
                                'camera' => 'Camera',
                                'charger' => 'Charger',
                                'cradle' => 'Cradle',
                                'drives(disc, flash)' => 'Drives(disc, flash)',
                                'flight_controller' => 'Flight controller',
                                'fvp_glasses' => 'FVP Glasses',
                                'gimbal' => 'Gimbal',
                                'gps' => 'GPS',
                                'lens' => 'Lens',
                                'lights' => 'Lights',
                                'monitor' => 'Monitor',
                                'motor' => 'Motor',
                                'parachute' => 'Parachute',
                                'phone/tablet' => 'Phone/Tablet',
                                'power_supply' => 'Power Supply',
                                'prop_guards' => 'Prop Guards',
                                'propeller' => 'Propeller',
                                'radio_receiver' => 'Radio Receiver',
                                'radio_transmitter' => 'Radio Transmitter',
                                'radio_extender' => 'Radio Extender',
                                'radio_finder(laser)' => 'Radio Finder(laser)',
                                'remote_controller' => 'Remote Controller',
                                'sensors' => 'Sensors',
                                'spreader' => 'Spreader',
                                'telementry_radio' => 'Telementry Radio',
                                'tripod' => 'Tripod',
                                'video_transmitter' => 'Video Transmitter'
                            ])
                            ->required()->columnSpan(2),
                        Forms\Components\Select::make('drones_id')->label('For Drone (Optional)')
                            ->relationship('drones', 'name', function (Builder $query){
                                $currentTeamId = auth()->user()->teams()->first()->id;
                                $query->where('teams_id', $currentTeamId);
                            }),
                    ])->columns(4),
                    //form ke dua
                    Forms\Components\Wizard\Step::make('Extra Information')
                        ->schema([
                        Forms\Components\Select::make('users_id')->label('Owner')
                            //->relationship('users', 'name')
                            ->options(function () {
                                $currentTeamId = auth()->user()->teams()->first()->id; 
                        
                                return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_user.team_id', $currentTeamId); 
                                })->pluck('name', 'id'); 
                            }),
                        Forms\Components\DatePicker::make('purchase_date')->label('Purchase date')
                            ->required(),
                        Forms\Components\TextInput::make('insurable_value')->label('Insurable Value')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('weight')->label('Weight')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('firmware_v')->label('Firmware Version ')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_v')->label('Hardware Version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Checkbox::make('is_loaner')->label('Loaner Equipment'),
                        Forms\Components\TextArea::make('description')->label('Description')
                            ->maxLength(255),
                        ])->columns(3),
                        //end wizard2
                ])->columnSpanFull(),
                //end wizard

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                     ->searchable(),
                Tables\Columns\TextColumn::make('model')
                     ->searchable(),
                Tables\Columns\TextColumn::make('type')
                     ->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')
                     ->color(fn ($record) => match ($record->status){
                         'airworthy' => Color::Green,
                        'maintenance' =>Color::Red,
                        'retired' => Color::Zinc,
                    })
                    ->searchable(),
                 Tables\Columns\TextColumn::make('drones.name')
                     ->numeric()
                     ->url(fn($record) =>$record->for_drone? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]):null)->color(Color::Blue)
                     ->sortable(),
                 Tables\Columns\TextColumn::make('users.name')
                    ->label('Owner')
                    ->url(fn($record) => $record->users_id? route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue)
                     ->numeric()
                     ->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            Section::make('Overview')
                ->schema([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('model')->label('Model'),
                TextEntry::make('status')->label('Status')
                    ->color(fn ($record) => match ($record->status){
                    'airworthy' => Color::Green,
                    'maintenance' =>Color::Red,
                    'retired' => Color::Zinc
                }),
                TextEntry::make('inventory_asset')->label('Inventory/Asset'),
                TextEntry::make('serial')->label('Serial'),
                TextEntry::make('type')->label('Type'),
                TextEntry::make('drones.name')->label('Drones')
                    ->url(fn($record) =>$record->for_drone? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]):null)->color(Color::Blue),
                    ])->columns(4),
            Section::make('Extra Information')
            ->schema([
                TextEntry::make('users.name')->label('Owner')
                    ->url(fn($record) => $record->users_id? route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue),
                TextEntry::make('purchase_date')->label('Purchase Date')->date('Y-m-d'),
                TextEntry::make('insurable_value')->label('Insurable Value'),
                TextEntry::make('weight')->label('Weight'),
                TextEntry::make('firmware_v')->label('Firmware Version'),
                TextEntry::make('hardware_v')->label('Hardware Version'),
                IconEntry::make('is_loaner')->boolean()->label('Loaner Equipment'),
                TextEntry::make('description')->label('Description'),
            ])->columns(4)
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
            'index' => Pages\ListEquidments::route('/'),
            'create' => Pages\CreateEquidment::route('/create'),
            'edit' => Pages\EditEquidment::route('/{record}/edit'),
        ];
    }
}
