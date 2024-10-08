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
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
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
                            ->relationship('drones', 'name'),
                    ])->columns(4),
                    //form ke dua
                    Forms\Components\Wizard\Step::make('Extra Information')
                        ->schema([
                        Forms\Components\Select::make('users_id')->label('Owner')
                            ->relationship('users', 'name'),
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
                        Forms\Components\Checkbox::make('is_loaner')->label('Loaner Equidment'),
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
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inventory_asset')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('drones.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurable_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('firmware_v')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hardware_v')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_loaner')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
            ])
            ->filters([
                //
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
            'view' => Pages\ViewEquidment::route('/{record}'),
            'edit' => Pages\EditEquidment::route('/{record}/edit'),
        ];
    }
}
